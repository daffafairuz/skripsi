<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\Device;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $sensors = Sensor::with('device')->latest()->get();
        $devices = Device::all();

        return view('sensors.index', compact('sensors', 'devices'));
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
            'unit' => 'required|string|max:50',
            'min_threshold' => 'nullable|numeric',
            'max_threshold' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama sensor wajib diisi',
            'device_id.required' => 'Device wajib dipilih',
            'type.required' => 'Tipe sensor wajib diisi',
            'unit.required' => 'Satuan wajib diisi',
        ]);

        Sensor::create([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'type' => $request->type,
            'unit' => $request->unit,
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
        ]);

        return redirect('/sensors')
            ->with('success', 'Sensor berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Sensor $sensor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'type' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_threshold' => 'nullable|numeric',
            'max_threshold' => 'nullable|numeric',
        ]);

        $sensor->update([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'type' => $request->type,
            'unit' => $request->unit,
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
        ]);

        return redirect('/sensors')
            ->with('success', 'Sensor berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Sensor $sensor)
    {
        $sensor->delete();

        return redirect('/sensors')
            ->with('success', 'Sensor berhasil dihapus');
    }
}
