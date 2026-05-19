<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData; 
use App\Models\DataSensor;

class SensorDataController extends Controller
{
    // 1. POST: Simpan Data dari ESP32
    public function store(Request $request)
    {
        // Validasi input 
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'ph'         => 'required|numeric',
        ]);

        // Simpan ke Database
        $sensor = SensorData::create($validated);

        return response()->json([
            'message' => 'Data berhasil disimpan ke Database',
            'data'    => $sensor
        ], 201);
    }

    // 2. GET: Ambil Data 
    public function index()
    {
        // Ambil data terbaru dulu, batasi 20 biar ringan
        $data = SensorData::orderBy('created_at', 'desc')->take(20)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }
    public function chartData()
    {
        $data = SensorData::latest()
            ->take(20)
            ->get()
            ->reverse();

        return response()->json([
            'labels' => $data->map(
                fn($r)=>optional($r->created_at)->format('H:i')
            )->values(),

            'temperature' => $data
                ->pluck('temperature')
                ->values(),

            'ph' => $data
                ->pluck('ph')
                ->values()
        ]);
    }
}