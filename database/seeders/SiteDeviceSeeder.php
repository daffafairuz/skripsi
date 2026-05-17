<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('site_devices')->insert([
            [
                'site_id' => 1,
                'device_id' => 1,
                'started_at' => now(),
                'ended_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 1,
                'device_id' => 2,
                'started_at' => now(),
                'ended_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 2,
                'device_id' => 3,
                'started_at' => now(),
                'ended_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
