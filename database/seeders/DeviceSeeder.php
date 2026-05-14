<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('devices')->insert([
            [
                'mac_address' => '00:11:22:33:44:55',
                'name' => 'Device 1',
                'description' => 'lorem ipsum dolor',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mac_address' => '00:11:22:33:44:56',
                'name' => 'Device 2',
                'description' => 'lorem ipsum dolor',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'mac_address' => '00:11:22:33:44:57',
                'name' => 'Device 3',
                'description' => 'lorem ipsum dolor',
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
