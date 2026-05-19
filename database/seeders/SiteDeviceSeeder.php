<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('site_devices')->insert([
            // Site 1 (Kebun Hidroponik Utama - Budi) -> Device 1 & 2
            [
                'site_id' => 1,
                'device_id' => 1,
                'started_at' => $now->copy()->subMonths(4),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'site_id' => 1,
                'device_id' => 2,
                'started_at' => $now->copy()->subMonths(4),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // Site 2 (Kebun Aquaponik Belakang - Budi) -> Device 3
            [
                'site_id' => 2,
                'device_id' => 3,
                'started_at' => $now->copy()->subMonths(3),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // Site 3 (Green Farm Siti) -> Device 4
            [
                'site_id' => 3,
                'device_id' => 4,
                'started_at' => $now->copy()->subMonths(4),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // Site 4 (Hidroponik Rooftop - Ahmad) -> Device 5
            [
                'site_id' => 4,
                'device_id' => 5,
                'started_at' => $now->copy()->subMonths(3),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // Site 5 (Greenhouse Strawberry - Ahmad) -> Device 6
            [
                'site_id' => 5,
                'device_id' => 6,
                'started_at' => $now->copy()->subMonths(2),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // Site 6 (Urban Farm Dewi) -> Device 7
            [
                'site_id' => 6,
                'device_id' => 7,
                'started_at' => $now->copy()->subMonths(3),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // Site 7 (Smart Garden Rizky) -> Device 8
            [
                'site_id' => 7,
                'device_id' => 8,
                'started_at' => $now->copy()->subMonths(2),
                'ended_at' => null,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // === Historical records (devices yang pernah dipindah) ===
            // Device 10 (Juliet, inactive) pernah di Site 8 tapi sudah dicopot
            [
                'site_id' => 8,
                'device_id' => 10,
                'started_at' => $now->copy()->subMonths(3),
                'ended_at' => $now->copy()->subWeeks(2),
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subWeeks(2),
            ],

            // Device 9 (India, available) pernah di Site 1, sudah dipindah
            [
                'site_id' => 1,
                'device_id' => 9,
                'started_at' => $now->copy()->subMonths(5),
                'ended_at' => $now->copy()->subMonths(4),
                'created_at' => $now->copy()->subMonths(5),
                'updated_at' => $now->copy()->subMonths(4),
            ],
        ]);
    }
}
