<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataSensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $records = [];
        
        // Let's seed for the last 24 hours, every 5 minutes
        // 24 hours * 12 = 288 data points per sensor
        $points = 288;
        $startTime = $now->copy()->subHours(24);

        for ($i = 0; $i < $points; $i++) {
            // Align to exact 5 minute intervals: e.g., 10:00, 10:05, 10:10...
            $time = $startTime->copy()->addMinutes($i * 5);
            $hour = (int) $time->format('H');

            // --- DEVICE 1 (Greenhouse Selada: Temperature, Humidity, pH) ---
            // Temperature diurnal pattern (cooler at night, warmer at day)
            $tempVal = ($hour >= 10 && $hour <= 16) ? 28.5 : 23.2;
            $tempVal += mt_rand(-10, 10) / 10;
            
            // Humidity inverse of temperature
            $humiVal = ($hour >= 10 && $hour <= 16) ? 62.0 : 81.5;
            $humiVal += mt_rand(-20, 20) / 10;

            // pH slow drift
            $phVal = 6.4 + sin($i / 10) * 0.3 + (mt_rand(-5, 5) / 100);

            $records[] = [
                'sensor_id' => 1,
                'value' => round($tempVal, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 2,
                'value' => round($humiVal, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 3,
                'value' => round($phVal, 2),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 2 (Monitoring TDS NFT: TDS, Water Level, Water Temp) ---
            // TDS slow absorption depletion
            $tdsVal = 950.0 - ($i % 50) * 2 + mt_rand(-5, 5);
            
            // Water Level steady with occasional drops
            $wlvlVal = 24.5 - ($i % 30) * 0.15 + mt_rand(-2, 2)/10;
            
            // Water Temperature
            $wtempVal = 24.8 + mt_rand(-8, 8) / 10;

            $records[] = [
                'sensor_id' => 4,
                'value' => round($tdsVal, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 5,
                'value' => round($wlvlVal, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 6,
                'value' => round($wtempVal, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 3 (Aquaponik Kolam: Suhu Kolam, pH Kolam, Dissolved Oxygen) ---
            $colTemp = 26.5 + sin($i / 15) * 1.5 + (mt_rand(-5, 5) / 10);
            $colPh = 7.1 + cos($i / 20) * 0.2 + (mt_rand(-3, 3) / 100);
            
            // DO higher during day (photosynthesis/aeration)
            $colDo = ($hour >= 8 && $hour <= 18) ? 7.8 : 5.9;
            $colDo += mt_rand(-5, 5) / 10;

            $records[] = [
                'sensor_id' => 7,
                'value' => round($colTemp, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 8,
                'value' => round($colPh, 2),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 9,
                'value' => round($colDo, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 4 (Indoor Farming: Light, Suhu, Kelembapan) ---
            // Light (grow light active 06:00 to 18:00)
            $lightVal = ($hour >= 6 && $hour <= 18) ? 12000.0 : 10.0;
            $lightVal += ($hour >= 6 && $hour <= 18) ? mt_rand(-300, 300) : mt_rand(-2, 2);

            $indTemp = 22.5 + mt_rand(-5, 5)/10;
            $indHumi = 65.0 + mt_rand(-15, 15)/10;

            $records[] = [
                'sensor_id' => 10,
                'value' => round($lightVal, 0),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 11,
                'value' => round($indTemp, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 12,
                'value' => round($indHumi, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 5 (Sistem DWC: Suhu Air DWC, pH DWC, EC DWC) ---
            $dwcTemp = 21.8 + mt_rand(-4, 4)/10;
            $dwcPh = 6.0 + sin($i / 25) * 0.15 + (mt_rand(-2, 2) / 100);
            $dwcEC = 1.9 + mt_rand(-5, 5)/100;

            $records[] = [
                'sensor_id' => 13,
                'value' => round($dwcTemp, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 14,
                'value' => round($dwcPh, 2),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 15,
                'value' => round($dwcEC, 2),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 6 (Greenhouse Strawberry: Suhu, Kelembapan, Soil) ---
            $strTemp = ($hour >= 10 && $hour <= 16) ? 22.5 : 16.8;
            $strTemp += mt_rand(-8, 8)/10;
            
            $strHumi = ($hour >= 10 && $hour <= 16) ? 65.0 : 78.0;
            $strHumi += mt_rand(-20, 20)/10;

            // Soil Moisture slowly drops, then jumps up at 08:00 (watering)
            $soilVal = 58.0 - (($i % 48) * 0.4);
            if (($i % 48) < 12) {
                // morning watering cycle
                $soilVal = min(68.0, 52.0 + (($i % 48) * 1.5));
            }
            $soilVal += mt_rand(-5, 5)/10;

            $records[] = [
                'sensor_id' => 16,
                'value' => round($strTemp, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 17,
                'value' => round($strHumi, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 18,
                'value' => round($soilVal, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 7 (Urban Farming: Soil Moisture, Light) ---
            $urbSoil = 48.0 - (($i % 96) * 0.2) + mt_rand(-5, 5)/10;
            $urbLight = ($hour >= 6 && $hour <= 18) ? 35000 * sin(pi() * ($hour - 6) / 12) : 5.0;
            $urbLight = max(0, $urbLight + mt_rand(-1000, 1000));

            $records[] = [
                'sensor_id' => 19,
                'value' => round($urbSoil, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 20,
                'value' => round($urbLight, 0),
                'created_at' => $time,
                'updated_at' => $time,
            ];

            // --- DEVICE 8 (Smart Garden: pH Nutrisi, TDS Nutrisi, Suhu Ambient) ---
            $sgPh = 6.2 + mt_rand(-8, 8)/100;
            $sgTds = 820.0 + mt_rand(-15, 15);
            $sgTemp = ($hour >= 10 && $hour <= 16) ? 31.0 : 25.5;
            $sgTemp += mt_rand(-12, 12)/10;

            $records[] = [
                'sensor_id' => 21,
                'value' => round($sgPh, 2),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 22,
                'value' => round($sgTds, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
            $records[] = [
                'sensor_id' => 23,
                'value' => round($sgTemp, 1),
                'created_at' => $time,
                'updated_at' => $time,
            ];
        }

        // Insert in chunks of 500 records to be highly efficient
        $chunks = array_chunk($records, 500);
        foreach ($chunks as $chunk) {
            DB::table('data_sensors')->insert($chunk);
        }
    }
}
