<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('devices')->insert([
            [
                'mac_address' => 'ESP:32:AA:BB:CC:01',
                'name' => 'ESP32-Slave Alpha',
                'description' => 'ESP32 Slave untuk greenhouse selada. Dilengkapi sensor suhu, kelembapan, dan pH air.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(5),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:02',
                'name' => 'ESP32-Slave Bravo',
                'description' => 'ESP32 Slave pendukung untuk monitoring TDS dan water level pada sistem NFT.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(5),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:03',
                'name' => 'ESP32-Slave Charlie',
                'description' => 'ESP32 Slave untuk kontrol pompa air dan aerator pada kolam aquaponik.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:04',
                'name' => 'ESP32-Slave Delta',
                'description' => 'ESP32 Slave indoor farming dengan sensor cahaya dan kontrol grow light otomatis.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:05',
                'name' => 'ESP32-Slave Echo',
                'description' => 'ESP32 Slave untuk sistem DWC dengan monitoring dissolved oxygen dan suhu air.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:06',
                'name' => 'ESP32-Slave Foxtrot',
                'description' => 'ESP32 Slave greenhouse strawberry untuk kontrol suhu dan kelembapan udara.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:07',
                'name' => 'ESP32-Slave Golf',
                'description' => 'ESP32 Slave urban farming untuk monitoring kelembapan tanah dan cahaya ambient.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:08',
                'name' => 'ESP32-Slave Hotel',
                'description' => 'ESP32 Slave smart garden untuk monitoring nutrisi dan pH larutan hidroponik.',
                'status' => 'assigned',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:09',
                'name' => 'ESP32-Slave India',
                'description' => 'ESP32 Slave cadangan yang belum dipasang. Siap terhubung ke Site Master.',
                'status' => 'available',
                'created_at' => $now->copy()->subMonth(),
                'updated_at' => $now->copy()->subMonth(),
            ],
            [
                'mac_address' => 'ESP:32:AA:BB:CC:10',
                'name' => 'ESP32-Slave Juliet',
                'description' => 'ESP32 Slave yang sedang dalam perbaikan. Sensor pH perlu kalibrasi ulang.',
                'status' => 'inactive',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subWeeks(1),
            ],
        ]);
    }
}
