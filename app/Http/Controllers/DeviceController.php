<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Sensor;
use App\Models\Actuator;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST DEVICE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $query = Device::with([
            'sensors',
            'actuators',
            'sites'
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if($user->role=='admin')
        {
            $devices = $query->get();

            return view(
                'admin.devices.index',
                compact('devices')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */
        $hasSite = $user->sites()->exists();
        $devices=$query
            ->whereHas(
                'sites',
                function($q) use($user){

                    $q->where(
                        'user_id',
                        $user->id
                    );

                }
            )
            ->get();

        return view(
            'user.devices.index',
            compact('devices','hasSite')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view(
            'admin.devices.create'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'name'=>'required',
            'mac_address'=>'required|unique:devices',

            'description'=>'nullable'

        ]);

        /*
        |--------------------------------------------------------------------------
        | CREATE ESP
        |--------------------------------------------------------------------------
        */

        $device=Device::create([

            'name'=>$request->name,
            'mac_address'=>$request->mac_address,
            'description'=>$request->description,
            'status'=>'available'

        ]);

        /*
        |--------------------------------------------------------------------------
        | SENSOR
        |--------------------------------------------------------------------------
        */

        if($request->sensors)
        {
            foreach(
                $request->sensors
                as $sensor
            )
            {
                Sensor::create([

                    'device_id'=>$device->id,
                    'name'=>$sensor['name'],
                    'type'=>$sensor['type'],
                    'unit'=>$sensor['unit'] ?? null

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ACTUATOR
        |--------------------------------------------------------------------------
        */

        if($request->actuators)
        {
            foreach(
                $request->actuators
                as $actuator
            )
            {

                Actuator::create([

                    'device_id'=>$device->id,

                    'name'=>$actuator['name'],

                    'type'=>$actuator['type'],

                    'default_state'=>'off'

                ]);

            }
        }

        return redirect()
            ->route('devices.index')
            ->with(
                'success',
                'Device berhasil ditambahkan'
            );
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

    public function update(
        Request $request,
        Device $device
    )
    {

        $device->update([

            'name'=>$request->name,

            'mac_address'=>$request->mac_address,

            'description'=>$request->description

        ]);

        return redirect()
            ->route('devices.index')
            ->with(
                'success',
                'Device berhasil diupdate'
            );
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
            ->with(
                'success',
                'Device berhasil dihapus'
            );
    }
}