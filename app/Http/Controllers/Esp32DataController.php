<?php

namespace App\Http\Controllers;

use App\Models\Esp32Data;
use Illuminate\Http\Request;

class Esp32DataController extends Controller
{
    public function index()
    {
        // Ambil data terbaru dulu, batasi 20 biar ringan
        $data = Esp32Data::latest()->take(20)->get();

        $stats = [
            'total' => Esp32Data::count(),
            'avg_temp' => Esp32Data::avg('temperature'),
            'avg_ph' => Esp32Data::avg('ph'),
            'max_temp' => Esp32Data::max('temperature'),
            'min_temp' => Esp32Data::min('temperature'),
            'max_ph' => Esp32Data::max('ph'),
            'min_ph' => Esp32Data::min('ph'),
            'latest' => Esp32Data::latest()->first()
        ];

        return view('esp32.index', compact('data', 'stats'));
    }

    public function latest()
    {
        $latest = Esp32Data::latest()->first();
        return response()->json($latest);
    }

    public function chartData(){
        $data = Esp32Data::latest()->take(20)->get()->reverse()->values();
        return response()->json([
            'labels' => $data->map(fn($item) => $item->created_at->format('H:i:s')),
            'temperature' => $data->pluck('temperature'),
            'ph' => $data->pluck('ph')
        ]);
    }
}
