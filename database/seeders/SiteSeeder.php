<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('sites')->insert([
            // User 3 (Budi Santoso) - 2 sites
            [
                'user_id' => 3,
                'name' => 'Kebun Hidroponik Utama',
                'location' => 'Jl. Raya Bogor KM 30, Depok, Jawa Barat',
                'description' => 'Greenhouse utama dengan sistem NFT hidroponik untuk budidaya selada dan bayam. Kapasitas 500 lubang tanam.',
                'mac_address' => 'AA:BB:CC:11:22:01',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],
            [
                'user_id' => 3,
                'name' => 'Kebun Aquaponik Belakang',
                'location' => 'Jl. Cendana No. 15, Cibinong, Jawa Barat',
                'description' => 'Sistem aquaponik kombinasi ikan lele dan kangkung. Kolam ikan 3x5 meter.',
                'mac_address' => 'AA:BB:CC:11:22:02',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // User 4 (Siti Rahayu) - 1 site
            [
                'user_id' => 4,
                'name' => 'Green Farm Siti',
                'location' => 'Jl. Dago Atas No. 88, Bandung, Jawa Barat',
                'description' => 'Vertical farming indoor untuk microgreens dan herbs. Menggunakan grow light LED full spectrum.',
                'mac_address' => 'AA:BB:CC:11:22:03',
                'created_at' => $now->copy()->subMonths(4),
                'updated_at' => $now->copy()->subMonths(4),
            ],

            // User 5 (Ahmad Hidayat) - 2 sites
            [
                'user_id' => 5,
                'name' => 'Hidroponik Rooftop',
                'location' => 'Jl. Pemuda No. 45, Semarang, Jawa Tengah',
                'description' => 'Sistem DWC (Deep Water Culture) di rooftop gedung. Fokus budidaya tomat cherry dan paprika mini.',
                'mac_address' => 'AA:BB:CC:11:22:04',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],
            [
                'user_id' => 5,
                'name' => 'Greenhouse Strawberry',
                'location' => 'Jl. Dieng KM 5, Wonosobo, Jawa Tengah',
                'description' => 'Greenhouse khusus strawberry dengan sistem drip irrigation dan kontrol suhu otomatis.',
                'mac_address' => 'AA:BB:CC:11:22:05',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // User 6 (Dewi Lestari) - 1 site
            [
                'user_id' => 6,
                'name' => 'Urban Farm Dewi',
                'location' => 'Jl. Kertajaya No. 120, Surabaya, Jawa Timur',
                'description' => 'Urban farming dengan sistem wick untuk tanaman hias dan sayuran organik di area terbatas.',
                'mac_address' => 'AA:BB:CC:11:22:06',
                'created_at' => $now->copy()->subMonths(3),
                'updated_at' => $now->copy()->subMonths(3),
            ],

            // User 7 (Rizky Pratama) - 1 site
            [
                'user_id' => 7,
                'name' => 'Smart Garden Rizky',
                'location' => 'Jl. Malioboro No. 77, Yogyakarta',
                'description' => 'Taman pintar dengan monitoring pH dan nutrisi otomatis. Fokus pada tanaman herbal dan rempah-rempah.',
                'mac_address' => 'AA:BB:CC:11:22:07',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],

            // User 8 (Nur Aisyah) - 1 site
            [
                'user_id' => 8,
                'name' => 'Kebun Sayur Organik',
                'location' => 'Jl. Sudirman No. 200, Medan, Sumatera Utara',
                'description' => 'Kebun sayur organik menggunakan media tanam cocopeat dengan sistem fertigasi terkontrol.',
                'mac_address' => 'AA:BB:CC:11:22:08',
                'created_at' => $now->copy()->subMonths(2),
                'updated_at' => $now->copy()->subMonths(2),
            ],
        ]);
    }
}
