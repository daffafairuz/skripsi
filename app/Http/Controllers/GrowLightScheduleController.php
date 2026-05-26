<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrowLightSchedule as Schedule;
use App\Models\Actuator;
use Illuminate\Support\Facades\Auth;

class GrowLightScheduleController extends Controller
{
    /**
     * Get accessible site IDs for the current user.
     */
    private function getUserSiteIds()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return null; // Admin has access to all sites
        }
        return $user->sites()->pluck('sites.id');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $schedules = Schedule::with('actuator.device')->orderBy('start_time', 'asc')->get();
            $sites = collect();
            $selectedSiteId = null;
        } else {
            // Find all sites owned by this user that have a device with a grow light actuator
            $sites = \App\Models\Site::where('user_id', $user->id)
                ->whereHas('devices', function ($q) {
                    $q->whereNull('site_devices.ended_at')
                      ->whereHas('actuators', function ($aq) {
                          $aq->where('type', 'grow_light');
                      });
                })
                ->get();

            $selectedSiteId = $request->query('site_id');

            $schedulesQuery = Schedule::whereHas('actuator.device.sites', function ($q) use ($user, $selectedSiteId) {
                $q->where('sites.user_id', $user->id)
                  ->whereNull('site_devices.ended_at');
                if ($selectedSiteId) {
                    $q->where('sites.id', $selectedSiteId);
                }
            })
            ->with('actuator.device')
            ->orderBy('start_time', 'asc');

            $schedules = $schedulesQuery->get();
        }

        return view('jadwal_grow_light.index', compact('schedules', 'sites', 'selectedSiteId'));
    }

    public function create()
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        if ($user->role === 'admin') {
            $growLights = Actuator::where('type', 'grow_light')->with('device')->get();
        } else {
            if ($siteIds->isEmpty()) {
                $growLights = collect();
            } else {
                $growLights = Actuator::where('type', 'grow_light')
                    ->whereHas('device.sites', function ($q) use ($siteIds) {
                        $q->whereIn('sites.id', $siteIds)
                          ->whereNull('site_devices.ended_at');
                    })
                    ->with('device')
                    ->get();
            }
        }

        return view('jadwal_grow_light.create', compact('growLights'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        // Validasi input
        $validated = $request->validate([
            'actuator_id' => 'required|exists:actuators,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'actuator_id.required' => 'Grow light wajib dipilih',
            'actuator_id.exists' => 'Grow light tidak valid',
            'start_time.required' => 'Start time wajib diisi',
            'start_time.date_format' => 'Format start time tidak valid',
            'end_time.required' => 'End time wajib diisi',
            'end_time.date_format' => 'Format end time tidak valid',
            'end_time.after' => 'End time harus setelah start time',
        ]);

        $actuatorId = $validated['actuator_id'];
        $actuator = Actuator::find($actuatorId);

        if (!$actuator || $actuator->type !== 'grow_light') {
            return back()->withErrors(['actuator_id' => 'Aktuator harus bertipe grow light'])->withInput();
        }

        // Validasi kepemilikan site/device bagi non-admin
        if ($user->role !== 'admin') {
            $hasAccess = $actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                return back()->withErrors(['actuator_id' => 'Anda tidak memiliki akses ke grow light ini'])->withInput();
            }
        }

        // Cek tabrakan waktu aktif untuk grow light yang sama
        $newStartTime = $validated['start_time'];
        $newEndTime = $validated['end_time'];

        $schedules = Schedule::where('actuator_id', $actuatorId)->get();

        foreach ($schedules as $sched) {
            if ($this->checkGrowLightOverlap($sched->start_time, $sched->end_time, $newStartTime, $newEndTime)) {
                return back()->withErrors(['start_time' => 'Jadwal bertabrakan dengan jadwal yang sudah ada (Waktu: ' . substr($sched->start_time, 0, 5) . ' - ' . substr($sched->end_time, 0, 5) . ')'])->withInput();
            }
        }

        Schedule::create([
            'actuator_id' => $actuatorId,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // Kirim perubahan ke MQTT
        \App\Services\MqttService::publishDeviceConfig($actuator->device);

        return redirect()->route('growlight.schedule')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        $schedule = Schedule::findOrFail($id);

        // Validasi akses non-admin
        if ($user->role !== 'admin') {
            $hasAccess = $schedule->actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                abort(403);
            }
        }

        // List grow lights untuk dropdown
        if ($user->role === 'admin') {
            $growLights = Actuator::where('type', 'grow_light')->with('device')->get();
        } else {
            $growLights = Actuator::where('type', 'grow_light')
                ->whereHas('device.sites', function ($q) use ($siteIds) {
                    $q->whereIn('sites.id', $siteIds)
                      ->whereNull('site_devices.ended_at');
                })
                ->with('device')
                ->get();
        }

        return view('jadwal_grow_light.edit', compact('schedule', 'growLights'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        $schedule = Schedule::findOrFail($id);

        // Validasi akses non-admin
        if ($user->role !== 'admin') {
            $hasAccess = $schedule->actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                abort(403);
            }
        }

        // Validasi input
        $validated = $request->validate([
            'actuator_id' => 'required|exists:actuators,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'actuator_id.required' => 'Grow light wajib dipilih',
            'actuator_id.exists' => 'Grow light tidak valid',
            'start_time.required' => 'Start time wajib diisi',
            'start_time.date_format' => 'Format start time tidak valid',
            'end_time.required' => 'End time wajib diisi',
            'end_time.date_format' => 'Format end time tidak valid',
            'end_time.after' => 'End time harus setelah start time',
        ]);

        $actuatorId = $validated['actuator_id'];
        $actuator = Actuator::find($actuatorId);

        if (!$actuator || $actuator->type !== 'grow_light') {
            return back()->withErrors(['actuator_id' => 'Aktuator harus bertipe grow light'])->withInput();
        }

        // Validasi kepemilikan bagi non-admin
        if ($user->role !== 'admin') {
            $hasAccess = $actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                return back()->withErrors(['actuator_id' => 'Anda tidak memiliki akses ke grow light ini'])->withInput();
            }
        }

        // Cek tabrakan waktu aktif untuk grow light yang sama (kecuali data sendiri)
        $newStartTime = $validated['start_time'];
        $newEndTime = $validated['end_time'];

        $schedules = Schedule::where('actuator_id', $actuatorId)
            ->where('id', '!=', $id)
            ->get();

        foreach ($schedules as $sched) {
            if ($this->checkGrowLightOverlap($sched->start_time, $sched->end_time, $newStartTime, $newEndTime)) {
                return back()->withErrors(['start_time' => 'Jadwal bertabrakan dengan jadwal yang sudah ada (Waktu: ' . substr($sched->start_time, 0, 5) . ' - ' . substr($sched->end_time, 0, 5) . ')'])->withInput();
            }
        }

        // Update jadwal
        $schedule->update([
            'actuator_id' => $actuatorId,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // Kirim perubahan ke MQTT
        \App\Services\MqttService::publishDeviceConfig($actuator->device);

        return redirect()->route('growlight.schedule')
            ->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        $schedule = Schedule::findOrFail($id);

        if ($user->role !== 'admin') {
            $hasAccess = $schedule->actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                abort(403);
            }
        }

        $device = $schedule->actuator->device;
        $schedule->delete();

        // Kirim perubahan ke MQTT
        \App\Services\MqttService::publishDeviceConfig($device);

        return redirect()->route('growlight.schedule')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    private function checkGrowLightOverlap($start1, $end1, $start2, $end2)
    {
        $s1 = $this->timeToMinutes($start1);
        $e1 = $this->timeToMinutes($end1);
        $s2 = $this->timeToMinutes($start2);
        $e2 = $this->timeToMinutes($end2);

        // Standard overlap: s1 < e2 && e1 > s2
        return ($s1 < $e2 && $e1 > $s2);
    }

    private function timeToMinutes($timeStr)
    {
        $parts = explode(':', $timeStr);
        return intval($parts[0]) * 60 + intval($parts[1]);
    }
}
