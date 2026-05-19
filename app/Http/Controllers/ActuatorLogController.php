<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActuatorLog;
use App\Models\Actuator;
use Illuminate\Support\Facades\Auth;

class ActuatorLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin lihat semua log
            $logs = ActuatorLog::with('actuator.device')
                ->latest()
                ->take(500)
                ->get();
        } else {
            // User lihat log berdasarkan site-nya
            $site = $user->sites()->first();

            if (!$site) {
                $logs = collect();
            } else {
                $deviceIds = $site->devices()->pluck('devices.id');
                $actuatorIds = Actuator::whereIn('device_id', $deviceIds)->pluck('id');

                $logs = ActuatorLog::with('actuator.device')
                    ->whereIn('actuator_id', $actuatorIds)
                    ->latest()
                    ->take(500)
                    ->get();
            }
        }

        // Group logs by actuator type for tabs
        $groupedLogs = $logs->groupBy(function ($log) {
            $type = strtolower($log->actuator->type ?? '');
            if (str_contains($type, 'pump') || str_contains($type, 'pompa')) return 'waterpump';
            if (str_contains($type, 'light') || str_contains($type, 'grow')) return 'growlight';
            if (str_contains($type, 'aerator') || str_contains($type, 'aera')) return 'aerator';
            if (str_contains($type, 'feeder') || str_contains($type, 'feed')) return 'feeder';
            return 'other';
        });

        return view('data_monitoring.log_aktuator', compact('logs', 'groupedLogs'));
    }
}
