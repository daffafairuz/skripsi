<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataSensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('data_sensors')->insert([
            [
                'sensor_id' => 1,
                'value' => 25.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sensor_id' => 2,
                'value' => 60.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sensor_id' => 3,
                'value' => 6.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
