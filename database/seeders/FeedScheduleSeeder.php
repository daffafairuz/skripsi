<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('feed_schedules')->insert([
            // Site 2 (Kebun Aquaponik Belakang - Budi)
            [
                'site_id' => 2,
                'time' => '07:00:00',
                'duration' => 10,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'site_id' => 2,
                'time' => '12:00:00',
                'duration' => 10,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'site_id' => 2,
                'time' => '17:00:00',
                'duration' => 15,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // Site 4 (Hidroponik Rooftop - Ahmad)
            [
                'site_id' => 4,
                'time' => '08:00:00',
                'duration' => 5,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'site_id' => 4,
                'time' => '16:00:00',
                'duration' => 5,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
        ]);
    }
}
