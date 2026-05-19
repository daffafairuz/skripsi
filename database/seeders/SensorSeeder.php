<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('sensors')->insert([
            // === Device 1 (ESP32-Slave Alpha) - Greenhouse Selada ===
            [
                'device_id' => 1,
                'name' => 'DHT22 Suhu Udara',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 18.00,
                'max_threshold' => 32.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 1,
                'name' => 'DHT22 Kelembapan Udara',
                'type' => 'humidity',
                'unit' => '%',
                'min_threshold' => 40.00,
                'max_threshold' => 85.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 1,
                'name' => 'Sensor pH Air',
                'type' => 'ph',
                'unit' => 'pH',
                'min_threshold' => 5.50,
                'max_threshold' => 7.50,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // === Device 2 (ESP32-Slave Bravo) - Monitoring TDS NFT ===
            [
                'device_id' => 2,
                'name' => 'Sensor TDS',
                'type' => 'tds',
                'unit' => 'ppm',
                'min_threshold' => 500.00,
                'max_threshold' => 1500.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 2,
                'name' => 'Sensor Water Level',
                'type' => 'water_level',
                'unit' => 'cm',
                'min_threshold' => 5.00,
                'max_threshold' => 30.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 2,
                'name' => 'DS18B20 Suhu Air',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 20.00,
                'max_threshold' => 30.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // === Device 3 (ESP32-Slave Charlie) - Kolam Aquaponik ===
            [
                'device_id' => 3,
                'name' => 'DS18B20 Suhu Kolam',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 24.00,
                'max_threshold' => 30.00,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 3,
                'name' => 'Sensor pH Kolam',
                'type' => 'ph',
                'unit' => 'pH',
                'min_threshold' => 6.50,
                'max_threshold' => 8.00,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 3,
                'name' => 'Sensor DO (Dissolved Oxygen)',
                'type' => 'dissolved_oxygen',
                'unit' => 'mg/L',
                'min_threshold' => 4.00,
                'max_threshold' => 10.00,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // === Device 4 (ESP32-Slave Delta) - Indoor Farming ===
            [
                'device_id' => 4,
                'name' => 'BH1750 Sensor Cahaya',
                'type' => 'light',
                'unit' => 'lux',
                'min_threshold' => 2000.00,
                'max_threshold' => 15000.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 4,
                'name' => 'DHT22 Suhu Indoor',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 20.00,
                'max_threshold' => 28.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 4,
                'name' => 'DHT22 Kelembapan Indoor',
                'type' => 'humidity',
                'unit' => '%',
                'min_threshold' => 50.00,
                'max_threshold' => 80.00,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // === Device 5 (ESP32-Slave Echo) - Sistem DWC ===
            [
                'device_id' => 5,
                'name' => 'DS18B20 Suhu Air DWC',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 18.00,
                'max_threshold' => 28.00,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 5,
                'name' => 'Sensor pH DWC',
                'type' => 'ph',
                'unit' => 'pH',
                'min_threshold' => 5.80,
                'max_threshold' => 6.50,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 5,
                'name' => 'Sensor EC DWC',
                'type' => 'ec',
                'unit' => 'mS/cm',
                'min_threshold' => 1.20,
                'max_threshold' => 2.50,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // === Device 6 (ESP32-Slave Foxtrot) - Greenhouse Strawberry ===
            [
                'device_id' => 6,
                'name' => 'SHT31 Suhu Greenhouse',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 15.00,
                'max_threshold' => 25.00,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 6,
                'name' => 'SHT31 Kelembapan Greenhouse',
                'type' => 'humidity',
                'unit' => '%',
                'min_threshold' => 60.00,
                'max_threshold' => 80.00,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 6,
                'name' => 'Soil Moisture Sensor',
                'type' => 'soil_moisture',
                'unit' => '%',
                'min_threshold' => 30.00,
                'max_threshold' => 70.00,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // === Device 7 (ESP32-Slave Golf) - Urban Farming ===
            [
                'device_id' => 7,
                'name' => 'Capacitive Soil Moisture',
                'type' => 'soil_moisture',
                'unit' => '%',
                'min_threshold' => 25.00,
                'max_threshold' => 65.00,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 7,
                'name' => 'LDR Sensor Cahaya',
                'type' => 'light',
                'unit' => 'lux',
                'min_threshold' => 5000.00,
                'max_threshold' => 50000.00,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // === Device 8 (ESP32-Slave Hotel) - Smart Garden ===
            [
                'device_id' => 8,
                'name' => 'Sensor pH Nutrisi',
                'type' => 'ph',
                'unit' => 'pH',
                'min_threshold' => 5.50,
                'max_threshold' => 7.00,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 8,
                'name' => 'Sensor TDS Nutrisi',
                'type' => 'tds',
                'unit' => 'ppm',
                'min_threshold' => 600.00,
                'max_threshold' => 1200.00,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 8,
                'name' => 'DHT22 Suhu Ambient',
                'type' => 'temperature',
                'unit' => '°C',
                'min_threshold' => 20.00,
                'max_threshold' => 35.00,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
        ]);
    }
}
