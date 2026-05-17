<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeederScheduleController extends Controller
{
    public function index()
    {
        return view('jadwal_pakan.index');
    }
}
