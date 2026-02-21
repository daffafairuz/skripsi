<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Esp32Data;
use Illuminate\Support\Facades\Log;

class MqttEsp32Listener extends Command
{
    protected $signature = 'mqtt:listen-esp32';
    protected $description = 'Menerima data temperature dan pH dari ESP32';

    public function handle()
    {
        $this->info('🚀 MQTT Listener untuk ESP32 dimulai...');
        $this->info('Menunggu data temperature dan pH...');
        $this->newLine();

        try {
            $mqtt = MQTT::connection();

            // Subscribe ke topik ESP32 - sesuaikan dengan topik di ESP32 Anda
            $mqtt->subscribe('esp32/data', function (string $topic, string $message) {

                $this->line("📨 [Pesan Diterima] " . now()->format('H:i:s'));
                $this->line("   Topik : {$topic}");
                $this->line("   Data  : {$message}");

                // Parse JSON dari ESP32
                $data = json_decode($message, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->warn("⚠️ Format JSON tidak valid: " . json_last_error_msg());
                    return;
                }

                // Ambil temperature dan pH (fleksibel dengan berbagai kemungkinan nama field)
                $temperature = $data['temperature'] ?? $data['suhu'] ?? $data['temp'] ?? null;
                $ph = $data['ph'] ?? $data['pH'] ?? $data['Ph'] ?? null;

                // Validasi: kedua data harus ada
                if ($temperature === null) {
                    $this->warn("⚠️ Data temperature tidak ditemukan dalam pesan");
                    return;
                }

                if ($ph === null) {
                    $this->warn("⚠️ Data pH tidak ditemukan dalam pesan");
                    return;
                }

                // Validasi nilai (opsional)
                if (!is_numeric($temperature) || !is_numeric($ph)) {
                    $this->warn("⚠️ Nilai temperature atau pH harus berupa angka");
                    return;
                }

                // Simpan ke database
                $reading = Esp32Data::create([
                    'temperature' => (float) $temperature,
                    'ph' => (float) $ph
                ]);

                $this->info("✅ Data tersimpan! ID: {$reading->id}");
                $this->line("   🌡️  Temperature : {$reading->formatted_temperature}");
                $this->line("   🧪 pH          : {$reading->formatted_ph}");
                $this->line("   ⏱️  Waktu       : {$reading->created_at->format('H:i:s')}");
                $this->newLine();

                // Log ke file untuk monitoring
                Log::info("Data ESP32 tersimpan", [
                    'id' => $reading->id,
                    'temperature' => $temperature,
                    'ph' => $ph
                ]);

            }, 1); // QoS level 1

            $this->info('Menjalankan loop MQTT. Tekan Ctrl+C untuk berhenti.');
            $mqtt->loop(true);

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('MQTT Listener Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
