<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Site;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $devices = Device::with(['sites', 'sensors', 'actuators'])->latest()->get();
        } else {
            // User hanya lihat device di site miliknya
            $siteIds = $user->sites->pluck('id');
            $deviceIds = \DB::table('site_devices')
                ->whereIn('site_id', $siteIds)
                ->pluck('device_id');

            $devices = Device::with(['sites', 'sensors', 'actuators'])
                ->whereIn('id', $deviceIds)
                ->latest()
                ->get();
        }

        return view('devices', compact('devices'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mac_address' => 'required|string|unique:devices,mac_address',
            'description' => 'nullable|string',
            'status' => 'required|in:available,assigned,inactive',
        ], [
            'name.required' => 'Nama device wajib diisi',
            'mac_address.required' => 'MAC Address wajib diisi',
            'mac_address.unique' => 'MAC Address sudah terdaftar',
        ]);

        Device::create([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect('/devices')
            ->with('success', 'Device berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mac_address' => 'required|string|unique:devices,mac_address,' . $device->id,
            'description' => 'nullable|string',
            'status' => 'required|in:available,assigned,inactive',
        ]);

        $device->update([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect('/devices')
            ->with('success', 'Device berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect('/devices')
            ->with('success', 'Device berhasil dihapus');
    }
}