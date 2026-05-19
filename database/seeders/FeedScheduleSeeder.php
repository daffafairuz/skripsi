<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('feed_schedules')->insert([
            [
                'site_id' => 1,
                'time' => '08:00:00',
                'last_time_active' => '2024-06-01 08:00:00',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 1,
                'time' => '12:00:00',
                'last_time_active' => '2024-06-01 12:00:00',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 1,
                'time' => '18:00:00',
                'last_time_active' => '2024-06-01 18:00:00',
                'amount' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
