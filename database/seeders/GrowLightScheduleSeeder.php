<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GrowLightScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('grow_light_schedules')->insert([
            // Site 1 (Kebun Hidroponik Utama - Budi)
            [
                'site_id' => 1,
                'start_time' => '06:00:00',
                'end_time' => '18:00:00',
                'last_time_active' => '06:00:00',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // Site 3 (Green Farm Siti)
            [
                'site_id' => 3,
                'start_time' => '07:00:00',
                'end_time' => '19:00:00',
                'last_time_active' => '07:00:00',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // Site 5 (Greenhouse Strawberry - Ahmad)
            [
                'site_id' => 5,
                'start_time' => '06:00:00',
                'end_time' => '17:00:00',
                'last_time_active' => '06:00:00',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // Site 7 (Smart Garden Rizky)
            [
                'site_id' => 7,
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'last_time_active' => '08:00:00',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
        ]);
    }
}
