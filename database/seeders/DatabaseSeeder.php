<?php

namespace Database\Seeders;

use App\Models\SiteDevice;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            SiteSeeder::class,
            DeviceSeeder::class,
            ActuatorSeeder::class,
            FeedScheduleSeeder::class,
            GrowLightScheduleSeeder::class,
            SensorSeeder::class,
            DataSensorSeeder::class,
            ActuatorLogSeeder::class,
            NotificationSeeder::class,
            SiteDeviceSeeder::class,
        ]);
    }
}
