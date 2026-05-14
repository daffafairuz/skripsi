<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sensors')->insert([
            [
                'device_id' => 1,
                'name' => 'Temperature Sensor 1',
                'type' => 'temperature',
                'unit' => '°C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 1,
                'name' => 'Humidity Sensor 1',
                'type' => 'humidity',
                'unit' => '%',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 2,
                'name' => 'Temperature Sensor 2',
                'type' => 'temperature',
                'unit' => '°C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 2,
                'name' => 'Humidity Sensor 2',
                'type' => 'humidity',
                'unit' => '%',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 3,
                'name' => 'Temperature Sensor 3',
                'type' => 'temperature',
                'unit' => '°C',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 3,
                'name' => 'Humidity Sensor 3',
                'type' => 'humidity',
                'unit' => '%',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
