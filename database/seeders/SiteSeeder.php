<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sites')->insert([
            [
                'user_id' => 1,
                'location' => 'Location 1',
                'description' => 'loremipsum dolor sit amet',
                'mac_address' => '00:1B:44:11:3A:B7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'location' => 'Location 2',
                'description' => 'loremipsum dolor sit amet',
                'mac_address' => '00:1B:44:11:3A:B8',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'location' => 'Location 3',
                'description' => 'loremipsum dolor sit amet',
                'mac_address' => '00:1B:44:11:3A:B9',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
