<?php

namespace App\Http\Controllers;

use App\Models\FeedSchedule;
use App\Models\Actuator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedScheduleController extends Controller
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

    // Halaman daftar jadwal
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $schedules = FeedSchedule::with('actuator.device')->orderBy('time')->get();
            $sites = collect();
            $selectedSiteId = null;
        } else {
            // Find all sites owned by this user that have a device with a feeder actuator
            $sites = \App\Models\Site::where('user_id', $user->id)
                ->whereHas('devices', function ($q) {
                    $q->whereNull('site_devices.ended_at')
                      ->whereHas('actuators', function ($aq) {
                          $aq->where('type', 'feeder');
                      });
                })
                ->get();

            $selectedSiteId = $request->query('site_id');

            $schedulesQuery = FeedSchedule::whereHas('actuator.device.sites', function ($q) use ($user, $selectedSiteId) {
                $q->where('sites.user_id', $user->id)
                  ->whereNull('site_devices.ended_at');
                if ($selectedSiteId) {
                    $q->where('sites.id', $selectedSiteId);
                }
            })
            ->with('actuator.device')
            ->orderBy('time');

            $schedules = $schedulesQuery->get();
        }

        return view('jadwal_pakan.index', compact('schedules', 'sites', 'selectedSiteId'));
    }

    // Halaman form tambah jadwal
    public function create()
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        if ($user->role === 'admin') {
            $feeders = Actuator::where('type', 'feeder')->with('device')->get();
        } else {
            if ($siteIds->isEmpty()) {
                $feeders = collect();
            } else {
                $feeders = Actuator::where('type', 'feeder')
                    ->whereHas('device.sites', function ($q) use ($siteIds) {
                        $q->whereIn('sites.id', $siteIds)
                          ->whereNull('site_devices.ended_at');
                    })
                    ->with('device')
                    ->get();
            }
        }

        return view('jadwal_pakan.create', compact('feeders'));
    }

    // Proses simpan jadwal baru
    public function store(Request $request)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        // Validasi
        $validated = $request->validate([
            'actuator_id' => 'required|exists:actuators,id',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1|max:60',
        ], [
            'actuator_id.required' => 'Feeder wajib dipilih',
            'actuator_id.exists' => 'Feeder tidak valid',
            'time.required' => 'Waktu wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (contoh: 08:00)',
            'duration.required' => 'Durasi wajib diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 1 menit',
            'duration.max' => 'Durasi maksimal 60 menit',
        ]);

        $actuatorId = $validated['actuator_id'];
        $actuator = Actuator::find($actuatorId);

        if (!$actuator || $actuator->type !== 'feeder') {
            return back()->withErrors(['actuator_id' => 'Aktuator harus bertipe feeder'])->withInput();
        }

        // Validasi kepemilikan site/device bagi non-admin
        if ($user->role !== 'admin') {
            $hasAccess = $actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                return back()->withErrors(['actuator_id' => 'Anda tidak memiliki akses ke feeder ini'])->withInput();
            }
        }

        // Cek tabrakan waktu aktif untuk feeder yang sama
        $newTime = $validated['time'];
        $newDuration = intval($validated['duration']);

        $schedules = FeedSchedule::where('actuator_id', $actuatorId)->get();

        foreach ($schedules as $sched) {
            if ($this->checkFeedOverlap($sched->time, $sched->duration, $newTime, $newDuration)) {
                return back()->withErrors(['time' => 'Jadwal bertabrakan dengan jadwal yang sudah ada (Waktu: ' . substr($sched->time, 0, 5) . ', Durasi: ' . $sched->duration . ' menit)'])->withInput();
            }
        }

        // Simpan data
        FeedSchedule::create([
            'actuator_id' => $actuatorId,
            'time' => $validated['time'] . ':00',
            'duration' => $validated['duration'],
        ]);

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil ditambahkan');
    }

    // Halaman form edit jadwal
    public function edit($id)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        $schedule = FeedSchedule::findOrFail($id);

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

        // List feeders untuk dropdown
        if ($user->role === 'admin') {
            $feeders = Actuator::where('type', 'feeder')->with('device')->get();
        } else {
            $feeders = Actuator::where('type', 'feeder')
                ->whereHas('device.sites', function ($q) use ($siteIds) {
                    $q->whereIn('sites.id', $siteIds)
                      ->whereNull('site_devices.ended_at');
                })
                ->with('device')
                ->get();
        }

        return view('jadwal_pakan.edit', compact('schedule', 'feeders'));
    }

    // Proses update jadwal
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        $schedule = FeedSchedule::findOrFail($id);

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

        // Validasi
        $validated = $request->validate([
            'actuator_id' => 'required|exists:actuators,id',
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1|max:60',
        ], [
            'actuator_id.required' => 'Feeder wajib dipilih',
            'actuator_id.exists' => 'Feeder tidak valid',
            'time.required' => 'Waktu wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (contoh: 08:00)',
            'duration.required' => 'Durasi wajib diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 1 menit',
            'duration.max' => 'Durasi maksimal 60 menit',
        ]);

        $actuatorId = $validated['actuator_id'];
        $actuator = Actuator::find($actuatorId);

        if (!$actuator || $actuator->type !== 'feeder') {
            return back()->withErrors(['actuator_id' => 'Aktuator harus bertipe feeder'])->withInput();
        }

        // Validasi kepemilikan bagi non-admin
        if ($user->role !== 'admin') {
            $hasAccess = $actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                return back()->withErrors(['actuator_id' => 'Anda tidak memiliki akses ke feeder ini'])->withInput();
            }
        }

        // Cek tabrakan waktu aktif untuk feeder yang sama (kecuali data sendiri)
        $newTime = $validated['time'];
        $newDuration = intval($validated['duration']);

        $schedules = FeedSchedule::where('actuator_id', $actuatorId)
            ->where('id', '!=', $id)
            ->get();

        foreach ($schedules as $sched) {
            if ($this->checkFeedOverlap($sched->time, $sched->duration, $newTime, $newDuration)) {
                return back()->withErrors(['time' => 'Jadwal bertabrakan dengan jadwal yang sudah ada (Waktu: ' . substr($sched->time, 0, 5) . ', Durasi: ' . $sched->duration . ' menit)'])->withInput();
            }
        }

        // Update data
        $schedule->update([
            'actuator_id' => $actuatorId,
            'time' => $validated['time'] . ':00',
            'duration' => $validated['duration'],
        ]);

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil diupdate');
    }

    // Proses hapus jadwal
    public function destroy($id)
    {
        $user = Auth::user();
        $siteIds = $this->getUserSiteIds();

        $schedule = FeedSchedule::findOrFail($id);

        if ($user->role !== 'admin') {
            $hasAccess = $schedule->actuator->device->sites()
                ->whereIn('sites.id', $siteIds)
                ->whereNull('site_devices.ended_at')
                ->exists();
            if (!$hasAccess) {
                abort(403);
            }
        }

        $schedule->delete();

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil dihapus');
    }
    private function checkFeedOverlap($time1, $duration1, $time2, $duration2)
    {
        $t1 = $this->timeToMinutes($time1);
        $t2 = $this->timeToMinutes($time2);

        $intervals1 = $this->getFeedIntervals($t1, $duration1);
        $intervals2 = $this->getFeedIntervals($t2, $duration2);

        foreach ($intervals1 as $int1) {
            foreach ($intervals2 as $int2) {
                if ($int1[0] < $int2[1] && $int1[1] > $int2[0]) {
                    return true;
                }
            }
        }

        return false;
    }

    private function timeToMinutes($timeStr)
    {
        $parts = explode(':', $timeStr);
        return intval($parts[0]) * 60 + intval($parts[1]);
    }

    private function getFeedIntervals($start, $duration)
    {
        $end = $start + $duration;
        if ($end > 1440) {
            return [
                [$start, 1440],
                [0, $end - 1440]
            ];
        }
        return [
            [$start, $end]
        ];
    }
}
