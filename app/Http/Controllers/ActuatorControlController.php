<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actuator;
use App\Models\ActuatorLog;
use Illuminate\Support\Facades\Auth;

class ActuatorControlController extends Controller
{
    /**
     * Halaman kontrol perangkat (user)
     * Menampilkan aktuator milik site user beserta status terkini
     */
    public function index()
    {
        $user = Auth::user();
        $site = $user->sites()->first();

        if (!$site) {
            return view('actuator_control', [
                'actuators' => collect(),
                'siteName' => null,
            ]);
        }

        // Ambil device yang terpasang di site user
        $deviceIds = $site->devices()->pluck('devices.id');

        // Ambil aktuator dari device-device tersebut
        $actuators = Actuator::with(['device', 'logs' => function ($q) {
            $q->latest()->limit(1);
        }])
            ->whereIn('device_id', $deviceIds)
            ->get()
            ->map(function ($actuator) {
                $lastLog = $actuator->logs->first();
                $actuator->current_state = $lastLog ? $lastLog->action : $actuator->default_state;
                $actuator->last_triggered = $lastLog ? $lastLog->created_at : null;
                $actuator->last_triggered_by = $lastLog ? $lastLog->triggered_by : '-';
                return $actuator;
            });

        return view('actuator_control', [
            'actuators' => $actuators,
            'siteName' => $site->name ?? $site->location,
        ]);
    }

    /**
     * Toggle aktuator ON/OFF
     */
    public function toggle(Request $request, $id)
    {
        $user = Auth::user();
        $site = $user->sites()->first();

        if (!$site) {
            return back()->with('error', 'User tidak memiliki site');
        }

        $deviceIds = $site->devices()->pluck('devices.id');

        $actuator = Actuator::whereIn('device_id', $deviceIds)
            ->findOrFail($id);

        // Cek status terakhir
        $lastLog = $actuator->logs()->latest()->first();
        $currentState = $lastLog ? $lastLog->action : $actuator->default_state;
        $newState = ($currentState === 'on') ? 'off' : 'on';

        // Buat log baru
        ActuatorLog::create([
            'actuator_id' => $actuator->id,
            'action' => $newState,
            'triggered_by' => 'manual',
        ]);

        return back()->with('success', $actuator->name . ' berhasil di-' . strtoupper($newState));
    }
}
