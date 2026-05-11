<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | STATIC DATA
        |--------------------------------------------------------------------------
        */

        $devices = [

            [
                'id' => 1,
                'name' => 'ESP32 Kolam A',
                'type' => 'ESP32',
                'category' => 'Controller',
                'site' => 'Kolam Lele A',
                'owner' => 'Bima Aryono',
                'status' => 'online',
                'last_update' => '2 menit lalu',
            ],

            [
                'id' => 2,
                'name' => 'Sensor pH',
                'type' => 'pH Sensor',
                'category' => 'Sensor',
                'site' => 'Kolam Lele A',
                'owner' => 'Bima Aryono',
                'status' => 'online',
                'last_update' => '1 menit lalu',
            ],

            [
                'id' => 3,
                'name' => 'Sensor Suhu',
                'type' => 'Temperature',
                'category' => 'Sensor',
                'site' => 'Kolam Pakcoy',
                'owner' => 'Daffa Fairuz',
                'status' => 'offline',
                'last_update' => '15 menit lalu',
            ],

            [
                'id' => 4,
                'name' => 'Pompa Air',
                'type' => 'Pump',
                'category' => 'Actuator',
                'site' => 'Kolam Lele A',
                'owner' => 'Bima Aryono',
                'status' => 'online',
                'last_update' => '5 menit lalu',
            ],

            [
                'id' => 5,
                'name' => 'Feeder Otomatis',
                'type' => 'Feeder',
                'category' => 'Actuator',
                'site' => 'Aquaponik Greenhouse',
                'owner' => 'Admin',
                'status' => 'offline',
                'last_update' => '20 menit lalu',
            ],

        ];

        return view('devices', compact('devices'));
    }
}