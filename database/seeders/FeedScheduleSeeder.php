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

        $feeder = DB::table('actuators')->where('type', 'feeder')->first();
        $feederId = $feeder ? $feeder->id : 6;

        DB::table('feed_schedules')->insert([
            // Feeder 1
            [
                'actuator_id' => $feederId,
                'time' => '07:00:00',
                'duration' => 10,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'actuator_id' => $feederId,
                'time' => '12:00:00',
                'duration' => 10,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'actuator_id' => $feederId,
                'time' => '17:00:00',
                'duration' => 15,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
        ]);
    }
}
