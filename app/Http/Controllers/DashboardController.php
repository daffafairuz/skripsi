<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Site;
use App\Models\Device;
use App\Models\Sensor;
use App\Models\DataSensor;
use App\Models\SensorData;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role == "admin") {
            $adminStats = [
                'total_sites' => Site::count(),
                'total_users' => User::where('role', 'user')->count(),
                'total_devices' => Device::count(),
                'total_sensor_data' => DataSensor::count(),
                'total_notifications' => Notification::count()
            ];

            // Get all devices with relationships paginated
            $devices = Device::with(['sensors', 'actuators'])->paginate(5);

            // Get all sites for selection and list
            $sites = Site::with('user')->latest()->get();

            // Activities / notifications
            $activities = Notification::with('site')->latest()->take(5)->get();

            // Selected site for monitoring preview on admin dashboard
            $selectedSiteId = $request->input('site_id') ?? $sites->first()?->id;
            $selectedSite = $sites->firstWhere('id', $selectedSiteId) ?? $sites->first();

            return view('admin.dashboard', compact(
                'adminStats',
                'devices',
                'sites',
                'activities',
                'selectedSite'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $sites = Site::where('user_id', $user->id)->get();
        $hasSite = !$sites->isEmpty();

        $site = null;
        $userStats = [
            'total_sites' => $sites->count(),
            'total_devices' => 0,
            'total_sensors' => 0,
            'active_actuators' => 0,
            'warnings' => 0,
        ];
        $sensorsList = [];
        $notifications = collect();

        if ($hasSite) {
            $selectedSiteId = $request->input('site_id') ?? $sites->first()->id;

            $site = Site::with([
                'devices' => function ($q) {
                    $q->whereNull('site_devices.ended_at');
                },
                'devices.sensors.latestData',
                'devices.actuators.logs' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])->where('user_id', $user->id)->find($selectedSiteId);

            if (!$site) {
                $site = Site::with([
                    'devices' => function ($q) {
                        $q->whereNull('site_devices.ended_at');
                    },
                    'devices.sensors.latestData',
                    'devices.actuators.logs' => function ($q) {
                        $q->latest()->limit(1);
                    }
                ])->where('user_id', $user->id)->first();
            }

            if ($site) {
                $totalDevices = $site->devices->count();
                $totalSensors = 0;
                $activeActuatorsCount = 0;
                $warningsCount = 0;

                foreach ($site->devices as $device) {
                    foreach ($device->sensors as $sensor) {
                        $totalSensors++;
                        $latestVal = $sensor->latestData?->value;

                        $status = 'normal';
                        if ($latestVal !== null) {
                            if ($sensor->min_threshold !== null && $latestVal < $sensor->min_threshold) {
                                $status = 'low';
                                $warningsCount++;
                            } elseif ($sensor->max_threshold !== null && $latestVal > $sensor->max_threshold) {
                                $status = 'high';
                                $warningsCount++;
                            }
                        }

                        $sensorsList[] = [
                            'id' => $sensor->id,
                            'name' => $sensor->name,
                            'type' => $sensor->type,
                            'unit' => $sensor->unit,
                            'min_threshold' => $sensor->min_threshold,
                            'max_threshold' => $sensor->max_threshold,
                            'latest_value' => $latestVal,
                            'status' => $status,
                            'updated_at' => $sensor->latestData?->created_at,
                        ];
                    }

                    foreach ($device->actuators as $actuator) {
                        $lastLog = $actuator->logs->first();
                        $state = $lastLog ? $lastLog->action : $actuator->default_state;
                        if ($state === 'on') {
                            $activeActuatorsCount++;
                        }
                    }
                }

                $userStats = [
                    'total_sites' => $sites->count(),
                    'total_devices' => $totalDevices,
                    'total_sensors' => $totalSensors,
                    'active_actuators' => $activeActuatorsCount,
                    'warnings' => $warningsCount,
                ];

                $notifications = Notification::where('site_id', $site->id)
                    ->latest()
                    ->take(5)
                    ->get();
            }
        }

        return view('user.dashboard', compact(
            'hasSite',
            'sites',
            'site',
            'userStats',
            'sensorsList',
            'notifications'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CHART
    |--------------------------------------------------------------------------
    */

    public function chartData(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $siteId = $request->input('site_id') ?? Site::first()?->id;
        } else {
            $siteId = $request->input('site_id') ?? $user->sites()->first()?->id;
        }

        if (!$siteId) {
            return $this->fallbackChartData();
        }

        $sensors = Sensor::whereHas('device.sites', function ($q) use ($siteId) {
            $q->where('sites.id', $siteId)->whereNull('site_devices.ended_at');
        })->get();

        if ($sensors->isEmpty()) {
            return $this->fallbackChartData();
        }

        $labels = [];
        $datasets = [];

        $colors = [
            'temperature' => [
                'border' => 'rgba(239, 68, 68, 1)',
                'bg' => 'rgba(239, 68, 68, 0.05)'
            ],
            'ph' => [
                'border' => 'rgba(16, 185, 129, 1)',
                'bg' => 'rgba(16, 185, 129, 0.05)'
            ],
            'humidity' => [
                'border' => 'rgba(59, 130, 246, 1)',
                'bg' => 'rgba(59, 130, 246, 0.05)'
            ],
            'tds' => [
                'border' => 'rgba(245, 158, 11, 1)',
                'bg' => 'rgba(245, 158, 11, 0.05)'
            ],
            'water_level' => [
                'border' => 'rgba(6, 182, 212, 1)',
                'bg' => 'rgba(6, 182, 212, 0.05)'
            ],
            'light' => [
                'border' => 'rgba(234, 179, 8, 1)',
                'bg' => 'rgba(234, 179, 8, 0.05)'
            ],
            'ec' => [
                'border' => 'rgba(139, 92, 246, 1)',
                'bg' => 'rgba(139, 92, 246, 0.05)'
            ],
            'dissolved_oxygen' => [
                'border' => 'rgba(14, 165, 233, 1)',
                'bg' => 'rgba(14, 165, 233, 0.05)'
            ],
            'soil_moisture' => [
                'border' => 'rgba(120, 53, 4, 1)',
                'bg' => 'rgba(120, 53, 4, 0.05)'
            ],
            'turbidity' => [
                'border' => 'rgba(14, 116, 144, 1)',
                'bg' => 'rgba(14, 116, 144, 0.05)'
            ]
        ];

        foreach ($sensors as $sensor) {
            $query = DataSensor::where('sensor_id', $sensor->id);
            $this->applyConnectionPeriodFilter($query, $siteId, $sensor->device_id, $user, $request);
            $dataPoints = $query->latest()
                ->take(15)
                ->get()
                ->reverse();

            if ($dataPoints->isEmpty()) {
                continue;
            }

            if (empty($labels)) {
                $labels = $dataPoints->map(fn($d) => $d->created_at->format('H:i'))->values()->all();
            }

            $color = $colors[$sensor->type] ?? [
                'border' => 'rgba(107, 114, 128, 1)',
                'bg' => 'rgba(107, 114, 128, 0.05)'
            ];

            $datasets[] = [
                'label' => $sensor->name . ' (' . $sensor->unit . ')',
                'data' => $dataPoints->map(fn($d) => (float)$d->value)->values()->all(),
                'borderColor' => $color['border'],
                'backgroundColor' => $color['bg'],
                'fill' => true,
                'tension' => 0.4,
                'borderWidth' => 2,
                'pointRadius' => 3,
                'pointHoverRadius' => 6,
                'sensor_type' => $sensor->type,
                'unit' => $sensor->unit
            ];
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }

    private function fallbackChartData()
    {
        $data = SensorData::latest()->take(15)->get()->reverse();

        $labels = $data->map(fn($r) => optional($r->created_at)->format('H:i'))->values()->all();

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Suhu (°C)',
                    'data' => $data->pluck('temperature')->map(fn($v) => $v == -127 ? null : (float)$v)->values()->all(),
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.05)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 3
                ],
                [
                    'label' => 'pH Air',
                    'data' => $data->pluck('ph')->map(fn($v) => (float)$v)->values()->all(),
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.05)',
                    'fill' => true,
                    'tension' => 0.4,
                    'borderWidth' => 2,
                    'pointRadius' => 3
                ]
            ]
        ]);
    }

    private function applyConnectionPeriodFilter($query, $selectedSiteId, $selectedDeviceId, $user, $request, $dateColumn = 'created_at')
    {
        // 1. Admin bypass check
        if ($user->role === 'admin') {
            // In dashboard context, default is 'site' to show correct site telemetry.
            // But let's allow 'all' if explicitly requested.
            $scope = $request->input('scope', 'site');
            if ($scope === 'all') {
                return; // Admin wants to see everything, no filter
            }
        }

        // 2. Fetch periods
        $periodsQuery = DB::table('site_devices');

        if ($user->role === 'admin') {
            if ($selectedSiteId) {
                $periodsQuery->where('site_id', $selectedSiteId);
            } else {
                return;
            }
        } else {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            if (empty($userSiteIds)) {
                $query->whereRaw('1 = 0');
                return;
            }
            if ($selectedSiteId && in_array($selectedSiteId, $userSiteIds)) {
                $periodsQuery->where('site_id', $selectedSiteId);
            } else {
                $periodsQuery->whereIn('site_id', $userSiteIds);
            }
        }

        if ($selectedDeviceId) {
            $periodsQuery->where('device_id', $selectedDeviceId);
        }

        $periods = $periodsQuery->get(['device_id', 'started_at', 'ended_at']);

        if ($periods->isEmpty()) {
            $query->whereRaw('1 = 0');
            return;
        }

        // Group periods by device_id
        $periodsByDevice = $periods->groupBy('device_id');
        $deviceIds = $periods->pluck('device_id')->unique()->toArray();

        // Get sensors for these devices
        $sensorsByDevice = Sensor::whereIn('device_id', $deviceIds)->get(['id', 'device_id'])->groupBy('device_id');

        $query->where(function ($q) use ($periodsByDevice, $sensorsByDevice, $dateColumn) {
            $isFirstDevice = true;
            foreach ($periodsByDevice as $deviceId => $devicePeriods) {
                $sensorIds = $sensorsByDevice->get($deviceId, collect())->pluck('id')->toArray();
                if (empty($sensorIds)) {
                    continue;
                }
                
                $q->{ $isFirstDevice ? 'where' : 'orWhere' }(function ($subQ) use ($sensorIds, $devicePeriods, $dateColumn) {
                    $subQ->whereIn('sensor_id', $sensorIds);
                    $subQ->where(function ($dateQ) use ($devicePeriods, $dateColumn) {
                        foreach ($devicePeriods as $idx => $period) {
                            $started = $period->started_at;
                            $ended = $period->ended_at;
                            $dateQ->{ $idx === 0 ? 'where' : 'orWhere' }(function ($innerQ) use ($started, $ended, $dateColumn) {
                                $innerQ->where($dateColumn, '>=', $started);
                                if ($ended) {
                                    $innerQ->where($dateColumn, '<=', $ended);
                                }
                            });
                        }
                    });
                });
                $isFirstDevice = false;
            }
        });
    }
}