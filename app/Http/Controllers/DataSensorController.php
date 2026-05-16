<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataSensorController extends Controller
{
    public function index()
    {
        return view('data_monitoring.riwayat_data_sensor');
    }
}
