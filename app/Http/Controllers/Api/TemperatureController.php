<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 


// temporary nanti diubah ke bentuk dinamis disimpan dalam database
class TemperatureController extends Controller
{
    // File penyimpanan
    private $fileName = 'data_sensor.json';

    // 1. FUNGSI MENYIMPAN DATA (POST)
    public function store(Request $request)
    {
        $suhu = $request->input('value');
        
        $newData = [
            'suhu' => $suhu,
            'waktu' => now()->toDateTimeString(),
        ];

        // --- LOGIKA SIMPAN KE JSON ---
        // Cek apakah file sudah ada
        if (Storage::exists($this->fileName)) {
            // Ambil data lama
            $currentData = json_decode(Storage::get($this->fileName), true);
        } else {
            // Buat array baru
            $currentData = [];
        }

        // Tambah data baru ke antrian paling atas (biar yang terbaru di atas)
        array_unshift($currentData, $newData);

        // Simpan kembali ke file
        Storage::put($this->fileName, json_encode($currentData, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Berhasil disimpan'], 200);
    }

    // 2. FUNGSI MENGAMBIL DATA (GET)
    public function index()
    {
        // Cek apakah file ada
        if (Storage::exists($this->fileName)) {
            // Ambil isi file
            $data = json_decode(Storage::get($this->fileName), true);
            
            return response()->json([
                'status' => 'success',
                'total_data' => count($data),
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 'empty',
                'message' => 'Belum ada data suhu yang tersimpan',
                'data' => []
            ], 200);
        }
    }
}