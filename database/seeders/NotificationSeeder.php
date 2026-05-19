<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('notifications')->insert([
            // Site 1 (Kebun Hidroponik Utama - Budi)
            [
                'site_id' => 1,
                'message' => 'Suhu udara terdeteksi tinggi (33.5°C) pada jam 13:00 WIB.',
                'type' => 'warning',
                'is_read' => true,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'site_id' => 1,
                'message' => 'pH air nutrisi turun di bawah batas aman (5.12). Harap periksa dosing tank.',
                'type' => 'alert',
                'is_read' => false,
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],

            // Site 2 (Kebun Aquaponik Belakang - Budi)
            [
                'site_id' => 2,
                'message' => 'Kadar Dissolved Oxygen kolam lele turun di bawah threshold (3.2 mg/L). Aerator diaktifkan otomatis.',
                'type' => 'alert',
                'is_read' => false,
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],

            // Site 3 (Green Farm Siti)
            [
                'site_id' => 3,
                'message' => 'Jadwal penyiraman otomatis berhasil dijalankan.',
                'type' => 'info',
                'is_read' => true,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],

            // Site 4 (Hidroponik Rooftop - Ahmad)
            [
                'site_id' => 4,
                'message' => 'pH larutan DWC di luar jangkauan (7.1 pH). Kebutuhan koreksi pH Down.',
                'type' => 'warning',
                'is_read' => false,
                'created_at' => $now->copy()->subHours(1),
                'updated_at' => $now->copy()->subHours(1),
            ],

            // Site 5 (Greenhouse Strawberry - Ahmad)
            [
                'site_id' => 5,
                'message' => 'Kelembapan tanah rendah (22%). Sistem fertigasi otomatis diaktifkan selama 5 menit.',
                'type' => 'info',
                'is_read' => true,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],

            // Site 7 (Smart Garden Rizky)
            [
                'site_id' => 7,
                'message' => 'Koneksi perangkat ESP32-Slave Hotel terputus selama 15 menit. Harap periksa catu daya atau sinyal Wi-Fi.',
                'type' => 'alert',
                'is_read' => false,
                'created_at' => $now->copy()->subMinutes(45),
                'updated_at' => $now->copy()->subMinutes(45),
            ],
        ]);
    }
}
