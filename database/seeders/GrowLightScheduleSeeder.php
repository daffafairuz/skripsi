<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrowLightScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('grow_light_schedules')->insert([
            [
                'site_id' => 1,
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'last_time_active' => '2024-06-01 08:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 1,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'last_time_active' => '2024-06-01 09:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
