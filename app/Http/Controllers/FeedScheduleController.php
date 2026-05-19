<?php

namespace App\Http\Controllers;

use App\Models\FeedSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedScheduleController extends Controller
{
    /**
     * Get the site_id for the current user.
     */
    private function getUserSiteId()
    {
        $user = Auth::user();
        return optional($user->sites()->first())->id;
    }

    // Halaman daftar jadwal
    public function index()
    {
        $siteId = $this->getUserSiteId();

        if (!$siteId) {
            $schedules = collect();
        } else {
            $schedules = FeedSchedule::where('site_id', $siteId)
                ->orderBy('time')
                ->get();
        }

        return view('jadwal_pakan.index', compact('schedules'));
    }

    // Halaman form tambah jadwal
    public function create()
    {
        return view('jadwal_pakan.create');
    }

    // Proses simpan jadwal baru
    public function store(Request $request)
    {
        $siteId = $this->getUserSiteId();

        if (!$siteId) {
            return redirect()->back()
                ->with('error', 'User tidak memiliki site')
                ->withInput();
        }

        // Validasi
        $validated = $request->validate([
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1|max:60',
        ], [
            'time.required' => 'Waktu wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (contoh: 08:00)',
            'duration.required' => 'Durasi wajib diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 1 menit',
            'duration.max' => 'Durasi maksimal 60 menit',
        ]);

        // Cek duplikasi waktu
        $existing = FeedSchedule::where('site_id', $siteId)
            ->where('time', $validated['time'] . ':00')
            ->first();

        if ($existing) {
            return back()->withErrors(['time' => 'Jadwal sudah ada pada waktu ini'])->withInput();
        }

        // Simpan data
        FeedSchedule::create([
            'site_id' => $siteId,
            'time' => $validated['time'] . ':00',
            'duration' => $validated['duration'],
        ]);

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil ditambahkan');
    }

    // Halaman form edit jadwal
    public function edit($id)
    {
        $siteId = $this->getUserSiteId();

        $schedule = FeedSchedule::where('site_id', $siteId)
            ->findOrFail($id);

        return view('jadwal_pakan.edit', compact('schedule'));
    }

    // Proses update jadwal
    public function update(Request $request, $id)
    {
        $siteId = $this->getUserSiteId();

        $schedule = FeedSchedule::where('site_id', $siteId)
            ->findOrFail($id);

        // Validasi
        $validated = $request->validate([
            'time' => 'required|date_format:H:i',
            'duration' => 'required|integer|min:1|max:60',
        ], [
            'time.required' => 'Waktu wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (contoh: 08:00)',
            'duration.required' => 'Durasi wajib diisi',
            'duration.integer' => 'Durasi harus berupa angka',
            'duration.min' => 'Durasi minimal 1 menit',
            'duration.max' => 'Durasi maksimal 60 menit',
        ]);

        // Cek duplikasi waktu (kecuali data sendiri)
        $existing = FeedSchedule::where('site_id', $siteId)
            ->where('time', $validated['time'] . ':00')
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return back()->withErrors(['time' => 'Jadwal sudah ada pada waktu ini'])->withInput();
        }

        // Update data
        $schedule->update([
            'time' => $validated['time'] . ':00',
            'duration' => $validated['duration'],
        ]);

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil diupdate');
    }

    // Proses hapus jadwal
    public function destroy($id)
    {
        $siteId = $this->getUserSiteId();

        $schedule = FeedSchedule::where('site_id', $siteId)
            ->findOrFail($id);

        $schedule->delete();

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil dihapus');
    }
}
