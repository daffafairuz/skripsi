<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'John Doe',
                'email' => 'john_doe@example.com',
                'phone_number' => '1234567890',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'phone_number' => '0987654321',
                'role' => 'admin',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane_smith@example.com',
                'phone_number' => '082918171',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ], 
            [
                'name' => 'Jane Doe',
                'email' => 'jane_doe@example.com',
                'phone_number' => '082911221',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
