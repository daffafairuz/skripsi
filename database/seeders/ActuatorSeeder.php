<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActuatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('actuators')->insert([
            // === Device 1 (ESP32-Slave Alpha) - Greenhouse Selada ===
            [
                'device_id' => 1,
                'name' => 'Grow Light LED Selada',
                'type' => 'grow_light',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 1,
                'name' => 'Pompa Nutrisi NFT',
                'type' => 'water_pump',
                'default_state' => 'on',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // === Device 2 (ESP32-Slave Bravo) ===
            [
                'device_id' => 2,
                'name' => 'Pompa Sirkulasi NFT',
                'type' => 'water_pump',
                'default_state' => 'on',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // === Device 3 (ESP32-Slave Charlie) - Aquaponik ===
            [
                'device_id' => 3,
                'name' => 'Aerator Kolam',
                'type' => 'water_pump',
                'default_state' => 'on',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 3,
                'name' => 'Pompa Sirkulasi Aquaponik',
                'type' => 'water_pump',
                'default_state' => 'on',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 3,
                'name' => 'Feeder Ikan Otomatis',
                'type' => 'feeder',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // === Device 4 (ESP32-Slave Delta) - Indoor Farming ===
            [
                'device_id' => 4,
                'name' => 'Grow Light Full Spectrum',
                'type' => 'grow_light',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'device_id' => 4,
                'name' => 'Kipas Exhaust',
                'type' => 'fan',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // === Device 5 (ESP32-Slave Echo) - DWC ===
            [
                'device_id' => 5,
                'name' => 'Pompa Aerasi DWC',
                'type' => 'water_pump',
                'default_state' => 'on',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'device_id' => 5,
                'name' => 'Pompa Nutrisi DWC',
                'type' => 'water_pump',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // === Device 6 (ESP32-Slave Foxtrot) - Greenhouse Strawberry ===
            [
                'device_id' => 6,
                'name' => 'Mist Sprayer Greenhouse',
                'type' => 'water_pump',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 6,
                'name' => 'Kipas Ventilasi Greenhouse',
                'type' => 'fan',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 6,
                'name' => 'Grow Light Strawberry',
                'type' => 'grow_light',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // === Device 7 (ESP32-Slave Golf) - Urban Farming ===
            [
                'device_id' => 7,
                'name' => 'Pompa Drip Irrigation',
                'type' => 'water_pump',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // === Device 8 (ESP32-Slave Hotel) - Smart Garden ===
            [
                'device_id' => 8,
                'name' => 'Pompa Dosing pH Up',
                'type' => 'water_pump',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 8,
                'name' => 'Pompa Dosing pH Down',
                'type' => 'water_pump',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'device_id' => 8,
                'name' => 'Grow Light Herbal',
                'type' => 'grow_light',
                'default_state' => 'off',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
        ]);
    }
}
