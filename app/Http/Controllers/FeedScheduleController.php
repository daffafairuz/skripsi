<?php

namespace App\Http\Controllers;

use App\Models\FeedSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedScheduleController extends Controller
{
    // Halaman daftar jadwal
    public function index()
    {
        $schedules = FeedSchedule::where('site_id', Auth::user()->id)
            ->orderBy('time')
            ->get();

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
        // Validasi
        $validated = $request->validate([
            'time' => 'required|date_format:H:i',
            'amount' => 'required|integer|min:1|max:10000',
        ], [
            'time.required' => 'Waktu wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (contoh: 08:00)',
            'amount.required' => 'Jumlah pakan wajib diisi',
            'amount.integer' => 'Jumlah pakan harus berupa angka',
            'amount.min' => 'Jumlah pakan minimal 1 gram',
            'amount.max' => 'Jumlah pakan maksimal 10000 gram',
        ]);

        // Cek duplikasi waktu
        $existing = FeedSchedule::where('site_id', Auth::user()->id)
            ->where('time', $validated['time'] . ':00')
            ->first();

        if ($existing) {
            return back()->withErrors(['time' => 'Jadwal sudah ada pada waktu ini'])->withInput();
        }

        // Simpan data
        FeedSchedule::create([
            'site_id' => Auth::user()->id,
            'time' => $validated['time'] . ':00',
            'amount' => $validated['amount'],
            'last_time_active' => null,
        ]);

        return redirect()->route('jadwal-pakan.index')
            ->with('success', 'Jadwal pakan berhasil ditambahkan');
    }

    // Halaman form edit jadwal
    public function edit($id)
    {
        $schedule = FeedSchedule::where('site_id', Auth::user()->id)
            ->findOrFail($id);

        return view('jadwal_pakan.edit', compact('schedule'));
    }

    // Proses update jadwal
    public function update(Request $request, $id)
    {
        $schedule = FeedSchedule::where('site_id', Auth::user()->id)
            ->findOrFail($id);

        // Validasi
        $validated = $request->validate([
            'time' => 'required|date_format:H:i',
            'amount' => 'required|integer|min:1|max:10000',
        ], [
            'time.required' => 'Waktu wajib diisi',
            'time.date_format' => 'Format waktu tidak valid (contoh: 08:00)',
            'amount.required' => 'Jumlah pakan wajib diisi',
            'amount.integer' => 'Jumlah pakan harus berupa angka',
            'amount.min' => 'Jumlah pakan minimal 1 gram',
            'amount.max' => 'Jumlah pakan maksimal 10000 gram',
        ]);

        // Cek duplikasi waktu (kecuali data sendiri)
        $existing = FeedSchedule::where('site_id', Auth::user()->id)
            ->where('time', $validated['time'] . ':00')
            ->where('id', '!=', $id)
            ->first();

        if ($existing) {
            return back()->withErrors(['time' => 'Jadwal sudah ada pada waktu ini'])->withInput();
        }

        // Update data
        $schedule->update([
            'time' => $validated['time'] . ':00',
            'amount' => $validated['amount'],
        ]);

        return redirect()->route('jadwal_pakan.index')
            ->with('success', 'Jadwal pakan berhasil diupdate');
    }

    // Proses hapus jadwal
    public function destroy($id)
    {
        $schedule = FeedSchedule::where('site_id', Auth::user()->id)
            ->findOrFail($id);

        $schedule->delete();

        return redirect()->route('jadwal_pakan.index')
            ->with('success', 'Jadwal pakan berhasil dihapus');
    }
}
