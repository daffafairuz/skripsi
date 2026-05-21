<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actuator;
use App\Models\ActuatorLog;
use App\Models\Site;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;

class ActuatorControlController extends Controller
{
    /**
     * Halaman kontrol perangkat (user)
     * Menampilkan aktuator milik site user beserta status terkini
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil semua site yang dapat diakses oleh user/admin
        if ($user->role === 'admin') {
            $sites = Site::latest()->get();
        } else {
            $sites = $user->sites;
        }

        if ($sites->isEmpty()) {
            return view('actuator_control', [
                'sites' => collect(),
                'devices' => collect(),
                'actuators' => collect(),
                'selectedSiteId' => null,
                'selectedDeviceId' => null,
                'siteName' => null,
            ]);
        }

        // Ambil input filter
        $selectedSiteId = $request->input('site_id');
        $selectedDeviceId = $request->input('device_id');

        // Jika device_id terpilih, otomatis hubungkan ke site aktif yang sesuai
        if ($selectedDeviceId) {
            $device = Device::find($selectedDeviceId);
            if ($device) {
                $activeSite = $device->sites()
                    ->whereNull('site_devices.ended_at')
                    ->whereIn('sites.id', $sites->pluck('id'))
                    ->first();
                if ($activeSite) {
                    $selectedSiteId = $activeSite->id;
                }
            }
        }

        // Validasi site_id yang dipilih agar milik user/admin tersebut
        if ($selectedSiteId && !$sites->contains('id', $selectedSiteId)) {
            $selectedSiteId = null;
        }

        // Ambil device berdasarkan site yang difilter (atau dari semua site jika filter site kosong)
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

        // Validasi device_id yang dipilih agar termasuk ke dalam device yang terasosiasi dengan site terfilter
        if ($selectedDeviceId && !$devices->contains('id', $selectedDeviceId)) {
            $selectedDeviceId = null;
        }

        // Mulai query untuk aktuator
        $query = Actuator::with(['device', 'logs' => function ($q) {
            $q->latest()->limit(1);
        }]);

        if ($selectedDeviceId) {
            $query->where('device_id', $selectedDeviceId);
        } else {
            $query->whereIn('device_id', $devices->pluck('id'));
        }

        $actuators = $query->get()->map(function ($actuator) {
            $lastLog = $actuator->logs->first();
            $actuator->current_state = $lastLog ? $lastLog->action : $actuator->default_state;
            $actuator->last_triggered = $lastLog ? $lastLog->created_at : null;
            $actuator->last_triggered_by = $lastLog ? $lastLog->triggered_by : '-';
            return $actuator;
        });

        // Tentukan nama site untuk tampilan
        $siteName = 'Semua Site';
        if ($selectedSiteId) {
            $currentSite = $sites->firstWhere('id', $selectedSiteId);
            $siteName = $currentSite->name ?? $currentSite->location;
        }

        return view('actuator_control', [
            'sites' => $sites,
            'devices' => $devices,
            'actuators' => $actuators,
            'selectedSiteId' => $selectedSiteId,
            'selectedDeviceId' => $selectedDeviceId,
            'siteName' => $siteName,
        ]);
    }

    /**
     * Toggle aktuator ON/OFF
     */
    public function toggle(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $actuator = Actuator::findOrFail($id);
        } else {
            $siteIds = $user->sites()->pluck('sites.id');
            $deviceIds = Device::whereHas('sites', function ($q) use ($siteIds) {
                $q->whereIn('sites.id', $siteIds)
                  ->whereNull('site_devices.ended_at');
            })->pluck('devices.id');

            $actuator = Actuator::whereIn('device_id', $deviceIds)->findOrFail($id);
        }

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
