<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActuatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('actuators')->insert([
            [
                'device_id' => 1,
                'name' => 'Grow Light 1',
                'type' => 'grow_light',
                'default_state' => 'off',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 2,
                'name' => 'Grow Light 2',
                'type' => 'grow_light',
                'default_state' => 'off',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'device_id' => 3,
                'name' => 'Water Pump 1',
                'type' => 'water_pump',
                'default_state' => 'off',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
