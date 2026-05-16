<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActuatorLog;

class ActuatorLogController extends Controller
{
    public function index()
    {
        return view('data_monitoring.log_aktuator');
    }
}
