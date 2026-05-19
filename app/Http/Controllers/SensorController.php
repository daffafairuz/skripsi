<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Sensor;

class SensorController extends Controller
{
    public function device(Device $device)
    {
        $device->load([

            'sensors.dataSensors'=>function($query){

                $query
                ->latest()
                ->take(20);

            }

        ]);

        return view(
            'sensors.device',
            compact(
                'device'
            )
        );
    }


    public function chart(Sensor $sensor)
    {
        $data=$sensor
            ->dataSensors()
            ->latest()
            ->take(20)
            ->get()
            ->reverse();

        return response()->json([

            'labels'=>

            $data
            ->map(
                fn($d)=>
                $d->created_at
                ->format('H:i')
            ),

            'values'=>

            $data
            ->pluck(
                'value'
            ),

            'sensor'=>

            $sensor->name,

            'unit'=>

            $sensor->unit

        ]);
    }
}