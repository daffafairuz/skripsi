<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            // === ADMIN USERS ===
            [
                'name' => 'Admin Utama',
                'email' => 'admin@gmail.com',
                'phone_number' => '081234567890',
                'role' => 'admin',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(6),
                'updated_at' => $now->copy()->subMonths(6),
            ],
            [
                'name' => 'Admin Teknis',
                'email' => 'admin.teknis@gmail.com',
                'phone_number' => '081298765432',
                'role' => 'admin',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(5),
                'updated_at' => $now->copy()->subMonths(5),
            ],

            // === REGULAR USERS ===
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'phone_number' => '082145678901',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti.rahayu@gmail.com',
                'phone_number' => '085367890123',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad.hidayat@gmail.com',
                'phone_number' => '087812345678',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'phone_number' => '081356789012',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'name' => 'Rizky Pratama',
                'email' => 'rizky.pratama@gmail.com',
                'phone_number' => '089678901234',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'name' => 'Nur Aisyah',
                'email' => 'nur.aisyah@gmail.com',
                'phone_number' => '082289012345',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
            [
                'name' => 'Eko Wijaya',
                'email' => 'eko.wijaya@gmail.com',
                'phone_number' => '085690123456',
                'role' => 'user',
                'status' => 'active',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonth(),
                'updated_at' => $now->copy()->subMonth(),
            ],
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri.handayani@gmail.com',
                'phone_number' => '087801234567',
                'role' => 'user',
                'status' => 'inactive',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'created_at' => $now->copy()->subMonths(5),
                'updated_at' => $now->copy()->subWeeks(2),
            ],
        ]);
    }
}
