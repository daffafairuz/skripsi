<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\SensorData; // Menggunakan model buatanmu

class FetchThingSpeakData extends Command
{
    // Nama perintah yang akan dieksekusi oleh sistem
    protected $signature = 'sensor:fetch';
    
    // Deskripsi perintah
    protected $description = 'Menarik data suhu dan pH akuaponik dari ThingSpeak ke database lokal';

    public function handle()
    {
        $channelId = '3281899';
        $readApiKey = 'NSGMDFC7WQN2W230';

        // Menarik 1 data terbaru
        $response = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
            'api_key' => $readApiKey,
            'results' => 1
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $feed = $data['feeds'][0] ?? null;

            if ($feed) {
                // Menyimpan ke tabel sensor_data sesuai struktur tabelmu
                SensorData::create([
                    'temperature' => $feed['field1'],
                    'ph'          => $feed['field2'],
                ]);
                
                $this->info('Data akuaponik terbaru berhasil disimpan ke database!');
            } else {
                $this->warn('Feed ThingSpeak kosong.');
            }
        } else {
            $this->error('Gagal terhubung ke API ThingSpeak.');
        }
    }
}
