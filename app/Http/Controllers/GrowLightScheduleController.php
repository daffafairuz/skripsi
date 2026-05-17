<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrowLightSchedule as Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class GrowLightScheduleController extends Controller
{
    public function index()
    {
        $siteId = $this->getUserSiteId();

        if (!$siteId) {
            $schedules = collect();
        } else {
            $schedules = Schedule::where('site_id', $siteId)
                                ->orderBy('start_time', 'asc')
                                ->get();
        }

        return view('jadwal_grow_light.index', compact('schedules'));
    }

    public function create()
    {
        return view('jadwal_grow_light.create');
    }

    public function store(Request $request)
    {
        $siteId = $this->getUserSiteId();

        // Validasi input
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'start_time.required' => 'Start time wajib diisi',
            'start_time.date_format' => 'Format start time tidak valid',
            'end_time.required' => 'End time wajib diisi',
            'end_time.date_format' => 'Format end time tidak valid',
            'end_time.after' => 'End time harus setelah start time',
        ]);

        if (!$siteId) {
            return redirect()->back()
                ->with('error', 'User tidak memiliki site_id')
                ->withInput();
        }

        Schedule::create([
            'site_id' => $siteId,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'last_time_active' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('growlight.schedule')
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit($id)
    {
        $siteId = $this->getUserSiteId();

        $schedule = Schedule::where('id', $id)
                            ->where('site_id', $siteId)
                            ->firstOrFail();

        return view('jadwal_grow_light.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $siteId = $this->getUserSiteId();

        // Validasi input
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'start_time.required' => 'Start time wajib diisi',
            'start_time.date_format' => 'Format start time tidak valid',
            'end_time.required' => 'End time wajib diisi',
            'end_time.date_format' => 'Format end time tidak valid',
            'end_time.after' => 'End time harus setelah start time',
        ]);

        $schedule = Schedule::where('id', $id)
                            ->where('site_id', $siteId)
                            ->firstOrFail();

        // Update jadwal
        $schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'updated_at' => Carbon::now(),
            // last_time_active tidak diupdate karena otomatis diisi oleh sistem
        ]);

        return redirect()->route('growlight.schedule')
            ->with('success', 'Jadwal berhasil diupdate');
    }

    public function destroy($id)
    {
        $siteId = $this->getUserSiteId();

        $schedule = Schedule::where('id', $id)
                            ->where('site_id', $siteId)
                            ->firstOrFail();

        $schedule->delete();

        return redirect()->route('growlight.schedule')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    private function getUserSiteId()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return null;
        }
        return optional($user->sites()->first())->id;
    }

    // Optional: Method untuk update last_time_active (dipanggil saat grow light menyala)
    public function updateLastActive($id)
    {
        $siteId = $this->getUserSiteId();

        $schedule = Schedule::where('id', $id)
                            ->where('site_id', $siteId)
                            ->firstOrFail();

        $schedule->update([
            'last_time_active' => Carbon::now()
        ]);

        return response()->json(['success' => true]);
    }
}
