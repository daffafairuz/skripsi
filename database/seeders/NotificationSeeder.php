<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\type;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'site_id' => 1,
                'message' => 'Your plant needs watering.',
                'type' => 'info',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 2,
                'message' => 'Your grow light is on.',
                'type' => 'info',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'site_id' => 3,
                'message' => 'Your sensor detected high humidity.',
                'type' => 'warning',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
                        [
                'site_id' => 3,
                'message' => 'Your sensor detected high humidity and Ph.',
                'type' => 'warning',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
