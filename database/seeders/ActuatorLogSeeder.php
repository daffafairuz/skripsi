<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActuatorLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $logs = [];

        // Actuators:
        // 1: Grow Light LED Selada (Device 1)
        // 2: Pompa Nutrisi NFT (Device 1)
        // 3: Pompa Sirkulasi NFT (Device 2)
        // 4: Aerator Kolam (Device 3)
        // 5: Pompa Sirkulasi Aquaponik (Device 3)
        // 6: Feeder Ikan Otomatis (Device 3)
        // 7: Grow Light Full Spectrum (Device 4)
        // 8: Kipas Exhaust (Device 4)

        // Seed logs for the last 5 days
        for ($day = 5; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);

            // Grow Lights turn on in the morning (06:00/07:00) and off in the evening (18:00/19:00)
            // Actuator 1
            $logs[] = [
                'actuator_id' => 1,
                'action' => 'on',
                'triggered_by' => 'auto',
                'created_at' => $date->copy()->setTime(6, 0, 0),
                'updated_at' => $date->copy()->setTime(6, 0, 0),
            ];
            $logs[] = [
                'actuator_id' => 1,
                'action' => 'off',
                'triggered_by' => 'auto',
                'created_at' => $date->copy()->setTime(18, 0, 0),
                'updated_at' => $date->copy()->setTime(18, 0, 0),
            ];

            // Actuator 7
            $logs[] = [
                'actuator_id' => 7,
                'action' => 'on',
                'triggered_by' => 'auto',
                'created_at' => $date->copy()->setTime(7, 0, 0),
                'updated_at' => $date->copy()->setTime(7, 0, 0),
            ];
            $logs[] = [
                'actuator_id' => 7,
                'action' => 'off',
                'triggered_by' => 'auto',
                'created_at' => $date->copy()->setTime(19, 0, 0),
                'updated_at' => $date->copy()->setTime(19, 0, 0),
            ];

            // Feeder Ikan (Actuator 6) runs at 07:00, 12:00, 17:00
            $feedTimes = [7, 12, 17];
            foreach ($feedTimes as $hour) {
                $logs[] = [
                    'actuator_id' => 6,
                    'action' => 'on',
                    'triggered_by' => 'auto',
                    'created_at' => $date->copy()->setTime($hour, 0, 0),
                    'updated_at' => $date->copy()->setTime($hour, 0, 0),
                ];
                $logs[] = [
                    'actuator_id' => 6,
                    'action' => 'off',
                    'triggered_by' => 'auto',
                    'created_at' => $date->copy()->setTime($hour, 0, 15), // active for 15 seconds
                    'updated_at' => $date->copy()->setTime($hour, 0, 15),
                ];
            }

            // Exhaust Fan (Actuator 8) triggered when temp is high in afternoon (12:00 - 15:00)
            $logs[] = [
                'actuator_id' => 8,
                'action' => 'on',
                'triggered_by' => 'auto',
                'created_at' => $date->copy()->setTime(12, 30, 0),
                'updated_at' => $date->copy()->setTime(12, 30, 0),
            ];
            $logs[] = [
                'actuator_id' => 8,
                'action' => 'off',
                'triggered_by' => 'auto',
                'created_at' => $date->copy()->setTime(15, 15, 0),
                'updated_at' => $date->copy()->setTime(15, 15, 0),
            ];

            // Manual pump toggles by Budi (User 3) on Actuator 2
            if ($day % 2 === 0) {
                $logs[] = [
                    'actuator_id' => 2,
                    'action' => 'off',
                    'triggered_by' => 'manual',
                    'created_at' => $date->copy()->setTime(10, 15, 0),
                    'updated_at' => $date->copy()->setTime(10, 15, 0),
                ];
                $logs[] = [
                    'actuator_id' => 2,
                    'action' => 'on',
                    'triggered_by' => 'manual',
                    'created_at' => $date->copy()->setTime(10, 45, 0),
                    'updated_at' => $date->copy()->setTime(10, 45, 0),
                ];
            }
        }

        DB::table('actuator_logs')->insert($logs);
    }
}
