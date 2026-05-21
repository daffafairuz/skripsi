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

        $ledSelada = DB::table('actuators')->where('name', 'Grow Light LED Selada')->first();
        $fullSpectrum = DB::table('actuators')->where('name', 'Grow Light Full Spectrum')->first();
        $strawberry = DB::table('actuators')->where('name', 'Grow Light Strawberry')->first();
        $herbal = DB::table('actuators')->where('name', 'Grow Light Herbal')->first();

        DB::table('grow_light_schedules')->insert([
            // Grow Light LED Selada
            [
                'actuator_id' => $ledSelada ? $ledSelada->id : 1,
                'start_time' => '06:00:00',
                'end_time' => '18:00:00',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // Grow Light Full Spectrum
            [
                'actuator_id' => $fullSpectrum ? $fullSpectrum->id : 7,
                'start_time' => '07:00:00',
                'end_time' => '19:00:00',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // Grow Light Strawberry
            [
                'actuator_id' => $strawberry ? $strawberry->id : 11,
                'start_time' => '06:00:00',
                'end_time' => '17:00:00',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // Grow Light Herbal
            [
                'actuator_id' => $herbal ? $herbal->id : 14,
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
        ]);
    }
}
