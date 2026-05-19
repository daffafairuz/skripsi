<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActuatorLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('actuator_logs')->insert([
            [
                'actuator_id' => 1,
                'action' => 'on',
                'triggered_by' => 'auto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'actuator_id' => 2,
                'action' => 'off',
                'triggered_by' => 'manual',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'actuator_id' => 3,
                'action' => 'on',
                'triggered_by' => 'auto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'actuator_id' => 3,
                'action' => 'off',
                'triggered_by' => 'auto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
