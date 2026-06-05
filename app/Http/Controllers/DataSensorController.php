<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\Sensor;
use App\Models\Site;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataSensorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Get sites
        if ($user->role === 'admin') {
            $sites = Site::latest()->get();
        } else {
            $sites = $user->sites;
        }

        if ($sites->isEmpty()) {
            return view('data_monitoring.riwayat_data_sensor', [
                'pivotedData' => new LengthAwarePaginator([], 0, 10),
                'sites' => collect(),
                'devices' => collect(),
                'selectedSiteId' => null,
                'selectedDeviceId' => null,
                'activeSensorColumns' => [],
                'perPage' => 10,
            ]);
        }

        $selectedSiteId = $request->input('site_id');
        $selectedDeviceId = $request->input('device_id');

        // Validate selectedSiteId
        if ($selectedSiteId && !$sites->contains('id', $selectedSiteId)) {
            $selectedSiteId = null;
        }

        // Get devices for filtering
        if ($selectedSiteId) {
            $devices = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->get();
        } else {
            // Devices from all accessible sites
            $allSiteIds = $sites->pluck('id');
            $devices = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                $q->whereIn('sites.id', $allSiteIds)
                  ->whereNull('site_devices.ended_at');
            })->get();
        }

        // Validate selectedDeviceId
        if ($selectedDeviceId && !$devices->contains('id', $selectedDeviceId)) {
            $selectedDeviceId = null;
        }

        // Now query DataSensor based on selected site / device
        $query = DataSensor::with(['sensor.device.sites']);

        if ($selectedDeviceId) {
            $sensorIds = Sensor::where('device_id', $selectedDeviceId)->pluck('id');
            $query->whereIn('sensor_id', $sensorIds);
        } elseif ($selectedSiteId) {
            $deviceIds = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $sensorIds = Sensor::whereIn('device_id', $deviceIds)->pluck('id');
            $query->whereIn('sensor_id', $sensorIds);
        } else {
            if ($user->role !== 'admin') {
                $allSiteIds = $sites->pluck('id');
                $deviceIds = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                    $q->whereIn('sites.id', $allSiteIds)
                      ->whereNull('site_devices.ended_at');
                })->pluck('devices.id');
                $sensorIds = Sensor::whereIn('device_id', $deviceIds)->pluck('id');
                $query->whereIn('sensor_id', $sensorIds);
            }
        }

        $rawSensors = $query->orderBy('created_at', 'desc')->take(3000)->get();

        // Pivot EAV data: Group by device & 5-minute time window
        $tempGroups = [];

        foreach ($rawSensors as $record) {
            $sensor = $record->sensor;
            if (!$sensor) continue;

            $device = $sensor->device;
            if (!$device) continue;

            // Get first associated site, if any (Site acts as the ESP32 Master gateway)
            $site = $device->sites->first();

            // Round created_at timestamp to the nearest 5-minute interval
            $time = Carbon::parse($record->created_at);
            $minute = $time->minute;
            $roundedMinute = floor($minute / 5) * 5;
            $timeKey = $time->copy()->minute($roundedMinute)->second(0)->format('Y-m-d H:i:00');

            // Unique key per device & time interval
            $groupKey = $timeKey . '_' . $device->id;

            if (!isset($tempGroups[$groupKey])) {
                $tempGroups[$groupKey] = [
                    'waktu' => $timeKey,
                    'device_id' => $device->id,
                    'device_name' => $device->name, // Slave ESP32
                    'site_name' => $site ? $site->name : '-', // Master ESP32
                    'values' => [
                        'temperature' => null,
                        'humidity' => null,
                        'ph' => null,
                        'tds' => null,
                        'water_level' => null,
                        'dissolved_oxygen' => null,
                        'ec' => null,
                        'soil_moisture' => null,
                        'light' => null,
                        'turbidity' => null,
                    ],
                    'raw_created' => $record->created_at, // used for sorting
                ];
            }

            // Normalise the sensor type to associate with our pivot columns
            $type = strtolower($sensor->type);
            if (array_key_exists($type, $tempGroups[$groupKey]['values'])) {
                $tempGroups[$groupKey]['values'][$type] = $record->value;
            }
        }

        // Format as sorted collection
        $pivotedData = collect(array_values($tempGroups))
            ->sortByDesc('raw_created')
            ->values();

        // Define sensor columns mapping with styling & formatting
        $sensorColumns = [
            'temperature' => [
                'label' => 'Suhu',
                'unit' => '°C',
                'bg_color' => 'bg-orange-50 border border-orange-100 text-orange-600',
                'format' => '%.1f',
            ],
            'humidity' => [
                'label' => 'Kelembapan',
                'unit' => '%',
                'bg_color' => 'bg-blue-50 border border-blue-100 text-blue-600',
                'format' => '%.1f',
            ],
            'ph' => [
                'label' => 'pH Air',
                'unit' => '',
                'bg_color' => 'bg-emerald-50 border border-emerald-100 text-emerald-600',
                'format' => '%.2f',
            ],
            'tds' => [
                'label' => 'TDS',
                'unit' => 'ppm',
                'bg_color' => 'bg-purple-50 border border-purple-100 text-purple-600',
                'format' => '%.0f',
            ],
            'water_level' => [
                'label' => 'Water Level',
                'unit' => 'cm',
                'bg_color' => 'bg-cyan-50 border border-cyan-100 text-cyan-600',
                'format' => '%.1f',
            ],
            'dissolved_oxygen' => [
                'label' => 'DO',
                'unit' => 'mg/L',
                'bg_color' => 'bg-teal-50 border border-teal-100 text-teal-600',
                'format' => '%.1f',
            ],
            'ec' => [
                'label' => 'EC',
                'unit' => 'mS',
                'bg_color' => 'bg-indigo-50 border border-indigo-100 text-indigo-600',
                'format' => '%.2f',
            ],
            'soil_moisture' => [
                'label' => 'Soil Moist.',
                'unit' => '%',
                'bg_color' => 'bg-rose-50 border border-rose-100 text-rose-600',
                'format' => '%.1f',
            ],
            'light' => [
                'label' => 'Cahaya',
                'unit' => 'lux',
                'bg_color' => 'bg-yellow-50 border border-yellow-200 text-yellow-700',
                'format' => '%.0f',
            ],
            'turbidity' => [
                'label' => 'Turbidity',
                'unit' => 'NTU',
                'bg_color' => 'bg-sky-50 border border-sky-100 text-sky-600',
                'format' => '%.1f',
            ],
        ];

        // Determine which sensors are present on the filtered scope for dynamic columns
        if ($selectedDeviceId) {
            $scopedSensorTypes = Sensor::where('device_id', $selectedDeviceId)
                ->distinct()
                ->pluck('type');
        } elseif ($selectedSiteId) {
            $deviceIds = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $scopedSensorTypes = Sensor::whereIn('device_id', $deviceIds)
                ->distinct()
                ->pluck('type');
        } else {
            $allSiteIds = $sites->pluck('id');
            $deviceIds = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                $q->whereIn('sites.id', $allSiteIds)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $scopedSensorTypes = Sensor::whereIn('device_id', $deviceIds)
                ->distinct()
                ->pluck('type');
        }

        $scopedSensorTypesArr = $scopedSensorTypes->map(fn($t) => strtolower($t))->toArray();

        $activeSensorColumns = array_filter($sensorColumns, function($key) use ($scopedSensorTypesArr) {
            return in_array(strtolower($key), $scopedSensorTypesArr);
        }, ARRAY_FILTER_USE_KEY);

        // Perform manual pagination on pivoted collection
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 30, 50, 100])) {
            $perPage = 10;
        }

        $currentItems = $pivotedData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $pivotedDataPaginated = new LengthAwarePaginator(
            $currentItems,
            $pivotedData->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query()
            ]
        );

        return view('data_monitoring.riwayat_data_sensor', [
            'pivotedData' => $pivotedDataPaginated,
            'sites' => $sites,
            'devices' => $devices,
            'selectedSiteId' => $selectedSiteId,
            'selectedDeviceId' => $selectedDeviceId,
            'activeSensorColumns' => $activeSensorColumns,
            'perPage' => $perPage,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = Auth::user();

        // 1. Get sites (same logic as index)
        if ($user->role === 'admin') {
            $sites = Site::latest()->get();
        } else {
            $sites = $user->sites;
        }

        if ($sites->isEmpty()) {
            return response()->streamDownload(function () {
                echo "Tidak ada data";
            }, 'data_sensor_kosong.csv');
        }

        $selectedSiteId = $request->input('site_id');
        $selectedDeviceId = $request->input('device_id');

        if ($selectedSiteId && !$sites->contains('id', $selectedSiteId)) {
            $selectedSiteId = null;
        }

        if ($selectedSiteId) {
            $devices = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->get();
        } else {
            $allSiteIds = $sites->pluck('id');
            $devices = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                $q->whereIn('sites.id', $allSiteIds)
                  ->whereNull('site_devices.ended_at');
            })->get();
        }

        if ($selectedDeviceId && !$devices->contains('id', $selectedDeviceId)) {
            $selectedDeviceId = null;
        }

        // Query DataSensor
        $query = DataSensor::with(['sensor.device.sites']);

        if ($selectedDeviceId) {
            $sensorIds = Sensor::where('device_id', $selectedDeviceId)->pluck('id');
            $query->whereIn('sensor_id', $sensorIds);
        } elseif ($selectedSiteId) {
            $deviceIds = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $sensorIds = Sensor::whereIn('device_id', $deviceIds)->pluck('id');
            $query->whereIn('sensor_id', $sensorIds);
        } else {
            if ($user->role !== 'admin') {
                $allSiteIds = $sites->pluck('id');
                $deviceIds = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                    $q->whereIn('sites.id', $allSiteIds)
                      ->whereNull('site_devices.ended_at');
                })->pluck('devices.id');
                $sensorIds = Sensor::whereIn('device_id', $deviceIds)->pluck('id');
                $query->whereIn('sensor_id', $sensorIds);
            }
        }

        $rawSensors = $query->orderBy('created_at', 'desc')->get();

        // Pivot EAV data (same logic as index)
        $tempGroups = [];

        foreach ($rawSensors as $record) {
            $sensor = $record->sensor;
            if (!$sensor) continue;

            $device = $sensor->device;
            if (!$device) continue;

            $site = $device->sites->first();

            $time = Carbon::parse($record->created_at);
            $minute = $time->minute;
            $roundedMinute = floor($minute / 5) * 5;
            $timeKey = $time->copy()->minute($roundedMinute)->second(0)->format('Y-m-d H:i:00');

            $groupKey = $timeKey . '_' . $device->id;

            if (!isset($tempGroups[$groupKey])) {
                $tempGroups[$groupKey] = [
                    'waktu' => $timeKey,
                    'device_id' => $device->id,
                    'device_name' => $device->name,
                    'site_name' => $site ? $site->name : '-',
                    'values' => [
                        'temperature' => null,
                        'humidity' => null,
                        'ph' => null,
                        'tds' => null,
                        'water_level' => null,
                        'dissolved_oxygen' => null,
                        'ec' => null,
                        'soil_moisture' => null,
                        'light' => null,
                        'turbidity' => null,
                    ],
                    'raw_created' => $record->created_at,
                ];
            }

            $type = strtolower($sensor->type);
            if (array_key_exists($type, $tempGroups[$groupKey]['values'])) {
                $tempGroups[$groupKey]['values'][$type] = $record->value;
            }
        }

        $pivotedData = collect(array_values($tempGroups))
            ->sortByDesc('raw_created')
            ->values();

        // Sensor columns definition
        $sensorColumns = [
            'temperature' => ['label' => 'Suhu (°C)', 'format' => '%.1f'],
            'humidity' => ['label' => 'Kelembapan (%)', 'format' => '%.1f'],
            'ph' => ['label' => 'pH Air', 'format' => '%.2f'],
            'tds' => ['label' => 'TDS (ppm)', 'format' => '%.0f'],
            'water_level' => ['label' => 'Water Level (cm)', 'format' => '%.1f'],
            'dissolved_oxygen' => ['label' => 'DO (mg/L)', 'format' => '%.1f'],
            'ec' => ['label' => 'EC (mS)', 'format' => '%.2f'],
            'soil_moisture' => ['label' => 'Soil Moisture (%)', 'format' => '%.1f'],
            'light' => ['label' => 'Cahaya (lux)', 'format' => '%.0f'],
            'turbidity' => ['label' => 'Turbidity (NTU)', 'format' => '%.1f'],
        ];

        // Determine active sensor columns in scope
        if ($selectedDeviceId) {
            $scopedSensorTypes = Sensor::where('device_id', $selectedDeviceId)->distinct()->pluck('type');
        } elseif ($selectedSiteId) {
            $deviceIds = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $scopedSensorTypes = Sensor::whereIn('device_id', $deviceIds)->distinct()->pluck('type');
        } else {
            $allSiteIds = $sites->pluck('id');
            $deviceIds = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                $q->whereIn('sites.id', $allSiteIds)->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $scopedSensorTypes = Sensor::whereIn('device_id', $deviceIds)->distinct()->pluck('type');
        }

        $scopedArr = $scopedSensorTypes->map(fn($t) => strtolower($t))->toArray();
        $activeCols = array_filter($sensorColumns, fn($v, $k) => in_array($k, $scopedArr), ARRAY_FILTER_USE_BOTH);

        $filename = 'laporan_data_sensor_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($pivotedData, $activeCols) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            $header = ['No', 'Waktu', 'Device (Slave)', 'Site (Master)'];
            foreach ($activeCols as $col) {
                $header[] = $col['label'];
            }
            fputcsv($handle, $header);

            foreach ($pivotedData as $index => $row) {
                $line = [
                    $index + 1,
                    \Carbon\Carbon::parse($row['waktu'])->format('d/m/Y H:i'),
                    $row['device_name'],
                    $row['site_name'],
                ];
                foreach ($activeCols as $colKey => $colInfo) {
                    $val = $row['values'][$colKey] ?? null;
                    $line[] = $val !== null ? sprintf($colInfo['format'], $val) : '-';
                }
                fputcsv($handle, $line);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
