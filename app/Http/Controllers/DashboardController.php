<?php

namespace App\Http\Controllers;
use App\Models\SensorData;
class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function dashboard()
    {
        $user = auth()->user();

        // STATIC DULU (nanti diganti DB)
        $hasSite = false;

        return view('dashboard', compact('hasSite'));
    }

     public function chartData()
    {
        $data = SensorData::latest()->take(20)->get()->reverse();
        
        return response()->json([
            // pastikan array murni (tanpa key 19..0)
            'labels' => $data->map(fn($r) => optional($r->created_at)->format('H:i'))->values(),
            'temperature' => $data->pluck('temperature')
                ->map(function ($v) {
                    $v = (float) $v;
                    return ($v == -127) ? null : $v;
                })
                ->values(),
            'ph' => $data->pluck('ph')->map(fn($v) => (float) $v)->values(),
        ]);
    }
}
