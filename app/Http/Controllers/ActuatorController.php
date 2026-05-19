<?php

namespace App\Http\Controllers;

use App\Models\Actuator;
use App\Models\ActuatorLog;
use App\Models\Device;
use Illuminate\Http\Request;

class ActuatorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $actuators = Actuator::with('device')->latest()->get();
        $devices = Device::all();

        return view('actuators.index', compact('actuators', 'devices'));
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
            'device_id' => 'required|exists:devices,id',
            'type' => 'required|string|max:255',
            'default_state' => 'required|in:on,off',
        ], [
            'name.required' => 'Nama aktuator wajib diisi',
            'device_id.required' => 'Device wajib dipilih',
            'type.required' => 'Tipe aktuator wajib diisi',
        ]);

        Actuator::create([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'type' => $request->type,
            'default_state' => $request->default_state,
        ]);

        return redirect('/actuators')
            ->with('success', 'Aktuator berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Actuator $actuator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'type' => 'required|string|max:255',
            'default_state' => 'required|in:on,off',
        ]);

        $actuator->update([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'type' => $request->type,
            'default_state' => $request->default_state,
        ]);

        return redirect('/actuators')
            ->with('success', 'Aktuator berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Actuator $actuator)
    {
        $actuator->delete();

        return redirect('/actuators')
            ->with('success', 'Aktuator berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE (from bima_view)
    |--------------------------------------------------------------------------
    */

    public function toggle(Request $request, Actuator $actuator)
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil status terakhir
        |--------------------------------------------------------------------------
        */

        $latest = $actuator
            ->logs()
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Toggle
        |--------------------------------------------------------------------------
        */

        $action = ($latest && $latest->action == "on")
            ? "off"
            : "on";

        /*
        |--------------------------------------------------------------------------
        | Simpan log
        |--------------------------------------------------------------------------
        */

        ActuatorLog::create([
            'actuator_id' => $actuator->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'triggered_by' => 'manual'
        ]);

        return response()->json([
            'success' => true,
            'action' => $action
        ]);
    }
}
