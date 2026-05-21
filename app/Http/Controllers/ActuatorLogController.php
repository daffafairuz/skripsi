<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActuatorLog;
use App\Models\Actuator;
use App\Models\Site;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;

class ActuatorLogController extends Controller
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
            return view('data_monitoring.log_aktuator', [
                'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
                'groupedLogs' => collect(),
                'sites' => collect(),
                'devices' => collect(),
                'selectedSiteId' => null,
                'selectedDeviceId' => null,
                'availableTabs' => [],
                'activeTab' => 'all',
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

        // Determine which actuators are present on the filtered scope for dynamic tabs
        if ($selectedDeviceId) {
            $scopedActuatorTypes = Actuator::where('device_id', $selectedDeviceId)
                ->distinct()
                ->pluck('type');
        } elseif ($selectedSiteId) {
            $deviceIds = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $scopedActuatorTypes = Actuator::whereIn('device_id', $deviceIds)
                ->distinct()
                ->pluck('type');
        } else {
            $allSiteIds = $sites->pluck('id');
            $deviceIds = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                $q->whereIn('sites.id', $allSiteIds)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $scopedActuatorTypes = Actuator::whereIn('device_id', $deviceIds)
                ->distinct()
                ->pluck('type');
        }

        // Standard actuator type mapping definitions
        $typeMapping = [
            'waterpump' => [
                'label' => 'Pompa Air',
                'color' => 'blue',
                'bg_dot' => 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]',
                'text_color' => 'text-blue-600',
                'keywords' => ['pump', 'pompa'],
            ],
            'growlight' => [
                'label' => 'Grow Light',
                'color' => 'purple',
                'bg_dot' => 'bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]',
                'text_color' => 'text-purple-600',
                'keywords' => ['light', 'grow'],
            ],
            'aerator' => [
                'label' => 'Aerator',
                'color' => 'cyan',
                'bg_dot' => 'bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.5)]',
                'text_color' => 'text-cyan-600',
                'keywords' => ['aerator', 'aera'],
            ],
            'feeder' => [
                'label' => 'Feeder',
                'color' => 'amber',
                'bg_dot' => 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]',
                'text_color' => 'text-amber-600',
                'keywords' => ['feeder', 'feed'],
            ],
            'other' => [
                'label' => 'Lainnya',
                'color' => 'gray',
                'bg_dot' => 'bg-gray-500 shadow-[0_0_8px_rgba(107,114,128,0.5)]',
                'text_color' => 'text-gray-600',
                'keywords' => [],
            ],
        ];

        $activeTabs = [];
        foreach ($scopedActuatorTypes as $rawType) {
            $rawTypeLower = strtolower($rawType);
            $matchedGroup = 'other';
            foreach ($typeMapping as $groupKey => $info) {
                if ($groupKey === 'other') continue;
                foreach ($info['keywords'] as $keyword) {
                    if (str_contains($rawTypeLower, $keyword)) {
                        $matchedGroup = $groupKey;
                        break 2;
                    }
                }
            }
            if (!in_array($matchedGroup, $activeTabs)) {
                $activeTabs[] = $matchedGroup;
            }
        }

        $availableTabs = array_intersect_key($typeMapping, array_flip($activeTabs));

        // Build log query
        $query = ActuatorLog::with('actuator.device');

        if ($selectedDeviceId) {
            $actuatorIds = Actuator::where('device_id', $selectedDeviceId)->pluck('id');
            $query->whereIn('actuator_id', $actuatorIds);
        } elseif ($selectedSiteId) {
            $deviceIds = Device::whereHas('sites', function ($q) use ($selectedSiteId) {
                $q->where('sites.id', $selectedSiteId)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');
            $actuatorIds = Actuator::whereIn('device_id', $deviceIds)->pluck('id');
            $query->whereIn('actuator_id', $actuatorIds);
        } else {
            if ($user->role !== 'admin') {
                $allSiteIds = $sites->pluck('id');
                $deviceIds = Device::whereHas('sites', function ($q) use ($allSiteIds) {
                    $q->whereIn('sites.id', $allSiteIds)
                      ->whereNull('site_devices.ended_at');
                })->pluck('devices.id');
                $actuatorIds = Actuator::whereIn('device_id', $deviceIds)->pluck('id');
                $query->whereIn('actuator_id', $actuatorIds);
            }
        }

        // Get count for each tab in this filtered scope
        $allLogsInScope = $query->get();
        $groupedLogs = $allLogsInScope->groupBy(function ($log) use ($typeMapping) {
            $type = strtolower($log->actuator->type ?? '');
            foreach ($typeMapping as $groupKey => $info) {
                if ($groupKey === 'other') continue;
                foreach ($info['keywords'] as $keyword) {
                    if (str_contains($type, $keyword)) {
                        return $groupKey;
                    }
                }
            }
            return 'other';
        });

        // Ensure "other" is in available tabs if we have logs categorized as other
        if (isset($groupedLogs['other']) && $groupedLogs['other']->isNotEmpty() && !isset($availableTabs['other'])) {
            $availableTabs['other'] = $typeMapping['other'];
        }

        // Filter database query by active tab parameter if requested
        $activeTab = $request->input('tab', 'all');
        if ($activeTab !== 'all') {
            $query->whereHas('actuator', function ($q) use ($activeTab) {
                if ($activeTab === 'waterpump') {
                    $q->where(fn($sq) => $sq->where('type', 'like', '%pump%')->orWhere('type', 'like', '%pompa%'));
                } elseif ($activeTab === 'growlight') {
                    $q->where(fn($sq) => $sq->where('type', 'like', '%light%')->orWhere('type', 'like', '%grow%'));
                } elseif ($activeTab === 'aerator') {
                    $q->where(fn($sq) => $sq->where('type', 'like', '%aerator%')->orWhere('type', 'like', '%aera%'));
                } elseif ($activeTab === 'feeder') {
                    $q->where(fn($sq) => $sq->where('type', 'like', '%feeder%')->orWhere('type', 'like', '%feed%'));
                } else {
                    // other
                    $q->where(fn($sq) => $sq->where('type', 'not like', '%pump%')
                        ->where('type', 'not like', '%pompa%')
                        ->where('type', 'not like', '%light%')
                        ->where('type', 'not like', '%grow%')
                        ->where('type', 'not like', '%aerator%')
                        ->where('type', 'not like', '%aera%')
                        ->where('type', 'not like', '%feeder%')
                        ->where('type', 'not like', '%feed%')
                    );
                }
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 30, 50, 100])) {
            $perPage = 10;
        }

        $logs = $query->latest()->paginate($perPage)->withQueryString();

        return view('data_monitoring.log_aktuator', compact(
            'logs',
            'groupedLogs',
            'sites',
            'devices',
            'selectedSiteId',
            'selectedDeviceId',
            'availableTabs',
            'activeTab',
            'perPage'
        ));
    }
}
