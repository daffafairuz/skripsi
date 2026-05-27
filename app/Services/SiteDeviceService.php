<?php

namespace App\Services;

use App\Models\Site;
use App\Models\Device;
use App\Models\SiteDevice;
use App\Events\SiteDevicesUpdated;
use Illuminate\Support\Facades\DB;
use Exception;

class SiteDeviceService
{
    /**
     * Memasang perangkat slave ke site.
     *
     * @param Site $site
     * @param Device $device
     * @throws Exception
     */
    public function attachDevice(Site $site, Device $device): void
    {
        // 1. Validasi status device harus available
        if ($device->status !== 'available') {
            throw new Exception("Perangkat {$device->name} tidak tersedia (status saat ini: {$device->status}).");
        }

        // 2. Hindari duplicate assignment (device sedang aktif di site mana pun)
        $activeAssignment = SiteDevice::where('device_id', $device->id)
            ->whereNull('ended_at')
            ->exists();

        if ($activeAssignment) {
            throw new Exception("Perangkat {$device->name} sudah terhubung ke sebuah site aktif.");
        }

        DB::transaction(function () use ($site, $device) {
            // 3. Buat relasi pada site_devices dengan started_at = now()
            SiteDevice::create([
                'site_id' => $site->id,
                'device_id' => $device->id,
                'started_at' => now(),
            ]);

            // 4. Update status device menjadi assigned
            $device->update([
                'status' => 'assigned',
            ]);
        });

        // 5. Trigger event sinkronisasi daftar slave ke MQTT
        event(new SiteDevicesUpdated($site));
    }

    /**
     * Melepas perangkat slave dari site.
     *
     * @param Site $site
     * @param Device $device
     * @throws Exception
     */
    public function detachDevice(Site $site, Device $device): void
    {
        // Temukan relasi aktif
        $activeAssignment = SiteDevice::where('site_id', $site->id)
            ->where('device_id', $device->id)
            ->whereNull('ended_at')
            ->first();

        if (!$activeAssignment) {
            throw new Exception("Perangkat {$device->name} tidak sedang terhubung ke site ini.");
        }

        DB::transaction(function () use ($activeAssignment, $device) {
            // 1. Update ended_at = NOW()
            $activeAssignment->update([
                'ended_at' => now(),
            ]);

            // 2. Ubah status device menjadi available
            $device->update([
                'status' => 'available',
            ]);
        });

        // 3. Trigger event sinkronisasi ulang ke MQTT
        event(new SiteDevicesUpdated($site));
    }
}
