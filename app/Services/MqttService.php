<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MqttService
{
    /**
     * Memformat konfigurasi lengkap perangkat (aktuator dan jadwal)
     * lalu mengirimkannya ke Node.js MQTT bridge untuk dipublish ke ESP32.
     *
     * @param Device $device
     * @return bool
     */
    public static function publishDeviceConfig(Device $device): bool
    {
        // Load relasi secara lengkap
        $device->load([
            'actuators.feedSchedules',
            'actuators.growLightSchedules',
            'actuators.logs' => function ($q) {
                $q->latest()->limit(1);
            }
        ]);

        $payload = [
            'mac_address' => $device->mac_address,
            'actuators' => $device->actuators->map(function ($actuator) {
                $lastLog = $actuator->logs->first();
                $state = $lastLog ? $lastLog->action : $actuator->default_state;

                $data = [
                    'id' => $actuator->id,
                    'name' => $actuator->name,
                    'type' => $actuator->type,
                    'state' => $state,
                ];

                if ($actuator->type === 'feeder') {
                    $data['schedules'] = $actuator->feedSchedules->map(function ($schedule) {
                        return [
                            'id' => $schedule->id,
                            'time' => substr($schedule->time, 0, 5), // Format HH:MM
                            'duration' => (int) $schedule->duration, // menit
                        ];
                    })->values()->all();
                } elseif ($actuator->type === 'grow_light') {
                    $data['schedules'] = $actuator->growLightSchedules->map(function ($schedule) {
                        return [
                            'id' => $schedule->id,
                            'start_time' => substr($schedule->start_time, 0, 5), // Format HH:MM
                            'end_time' => substr($schedule->end_time, 0, 5), // Format HH:MM
                        ];
                    })->values()->all();
                } else {
                    $data['schedules'] = [];
                }

                return $data;
            })->values()->all()
        ];

        try {
            // URL local Node.js bridge. Dapat dikonfigurasi melalui .env.
            // Port default adalah 5000.
            $nodeUrl = env('NODE_MQTT_API_URL');
            
            Log::info("Mengirim konfigurasi perangkat {$device->mac_address} ke Node.js MQTT bridge: " . json_encode($payload));

            $response = Http::timeout(5)->post($nodeUrl, $payload);

            if ($response->successful()) {
                Log::info("MQTT config berhasil dikirim untuk perangkat: {$device->mac_address}");
                return true;
            }

            Log::error("Gagal mengirim MQTT config untuk perangkat: {$device->mac_address}. Status: " . $response->status() . " Response: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Kesalahan saat mempublikasikan konfigurasi MQTT untuk perangkat {$device->mac_address}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Mempublikasikan daftar slave aktif ke master site terkait.
     *
     * @param Site $site
     * @return bool
     */
    public static function publishMasterSync(Site $site): bool
    {
        // Ambil semua slave aktif milik site: site_devices.ended_at IS NULL
        $activeSlaves = $site->devices()
            ->wherePivot('ended_at', null)
            ->get();

        $slavesPayload = $activeSlaves->map(function ($device) {
            return [
                'mac_address' => $device->mac_address,
            ];
        })->values()->all();

        // Menggunakan kolom mac_address dari sites
        $masterMac = $site->mac_address;

        if (empty($masterMac)) {
            Log::warning("Sinkronisasi master diabaikan untuk site ID {$site->id} karena mac_address kosong.");
            return false;
        }

        $payload = [
            'master_mac' => $masterMac,
            'site_id' => $site->id,
            'slaves' => $slavesPayload,
        ];

        try {
            $nodeUrl = env('NODE_MQTT_SYNC_URL');

            Log::info("Mengirim sinkronisasi master {$masterMac} ke Node.js MQTT bridge: " . json_encode($payload));

            $response = Http::timeout(5)->post($nodeUrl, $payload);

            if ($response->successful()) {
                Log::info("MQTT master sync berhasil dikirim untuk master: {$masterMac}");
                return true;
            }

            Log::error("Gagal mengirim MQTT master sync untuk master: {$masterMac}. Status: " . $response->status() . " Response: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Kesalahan saat mempublikasikan sinkronisasi MQTT untuk master {$masterMac}: " . $e->getMessage());
        }

        return false;
    }
}
