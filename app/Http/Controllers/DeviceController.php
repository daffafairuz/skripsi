<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Site;
use App\Models\Sensor;
use App\Models\Actuator;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Device::with([
            'sensors',
            'actuators',
            'sites' => function ($q) {
                $q->whereNull('site_devices.ended_at');
            },
            'sites.user',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if ($user->role == 'admin') {
            $devices = $query->get();
            $sites = Site::with('user')->latest()->get();

            return view(
                'admin.devices.index',
                compact('devices', 'sites')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $hasSite = $user->sites()->exists();
        $sites = $user->sites;
        $selectedSiteId = $request->query('site_id');

        $devices = $query
            ->whereHas('sites', function ($q) use ($user, $selectedSiteId) {
                $q->where('user_id', $user->id)
                    ->whereNull('site_devices.ended_at');
                if ($selectedSiteId) {
                    $q->where('sites.id', $selectedSiteId);
                }
            })
            ->get();

        return view(
            'user.devices.index',
            compact('devices', 'hasSite', 'sites', 'selectedSiteId')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.devices.create');
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
        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE ESP
        |--------------------------------------------------------------------------
        */

        $device = Device::create([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
            'description' => $request->description,
            'status' => 'available'
        ]);

        /*
        |--------------------------------------------------------------------------
        | SENSOR
        |--------------------------------------------------------------------------
        */

        if ($request->sensors) {
            foreach ($request->sensors as $sensor) {
                Sensor::create([
                    'device_id' => $device->id,
                    'name' => $sensor['name'],
                    'type' => $sensor['type'],
                    'unit' => $sensor['unit'] ?? null,
                    'min_threshold' => isset($sensor['min_threshold']) && $sensor['min_threshold'] !== '' ? $sensor['min_threshold'] : null,
                    'max_threshold' => isset($sensor['max_threshold']) && $sensor['max_threshold'] !== '' ? $sensor['max_threshold'] : null,
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUATOR
        |--------------------------------------------------------------------------
        */

        if ($request->actuators) {
            foreach ($request->actuators as $actuator) {
                Actuator::create([
                    'device_id' => $device->id,
                    'name' => $actuator['name'],
                    'type' => $actuator['type'],
                    'default_state' => 'off'
                ]);
            }
        }

        return redirect()
            ->route('devices.index')
            ->with('success', 'Device berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Device $device)
    {
        $device->load([
            'sensors',
            'actuators.logs',
            'sites'
        ]);

        return view(
            'devices.show',
            compact('device')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Device $device)
    {
        $device->load([
            'sensors',
            'actuators'
        ]);

        return view(
            'admin.devices.edit',
            compact('device')
        );
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
        ]);

        $device->update([
            'name' => $request->name,
            'mac_address' => $request->mac_address,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('devices.index')
            ->with('success', 'Device berhasil diupdate');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()
            ->route('devices.index')
            ->with('success', 'Device berhasil dihapus');
    }
}
