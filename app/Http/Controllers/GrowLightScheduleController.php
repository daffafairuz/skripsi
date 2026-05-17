<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GrowLightScheduleController extends Controller
{
    public function index()
    {
        return view('jadwal_grow_light.index');
    }
}
