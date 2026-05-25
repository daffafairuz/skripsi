<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Site;
use App\Models\SiteDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteDeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Form Tambah Device
    |--------------------------------------------------------------------------
    */

    public function create(Site $site)
    {
        $this->authorizeSiteAccess($site);

        $devices = Device::with(['sensors', 'actuators'])
            ->where('status', 'available')
            ->whereDoesntHave('sites', function ($query) {
                $query->whereNull('site_devices.ended_at');
            })
            ->get();

        return view(
            'user.sites.add-device',
            compact(
                'site',
                'devices'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan dari halaman site/user
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, Site $site)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
        ]);

        $this->authorizeSiteAccess($site);

        if (! $this->attachDevice($site, Device::findOrFail($request->device_id))) {
            return redirect()
                ->back()
                ->with('error', 'Device masih terhubung ke site lain');
        }

        return redirect()
            ->route('devices.index', [
                'site_id' => $site->id,
            ])
            ->with('success', 'Device berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | Simpan dari halaman admin/device
    |--------------------------------------------------------------------------
    */

    public function attach(Request $request)
    {
        $request->validate([
            'site_id' => 'required|exists:sites,id',
            'device_id' => 'required|exists:devices,id',
        ]);

        $site = Site::findOrFail($request->site_id);

        $this->authorizeSiteAccess($site);

        if (! $this->attachDevice($site, Device::findOrFail($request->device_id))) {
            return redirect()
                ->back()
                ->with('error', 'Device masih terhubung ke site lain');
        }

        return redirect()
            ->back()
            ->with('success', 'Device berhasil dihubungkan');
    }

    /*
    |--------------------------------------------------------------------------
    | Copot Device
    |--------------------------------------------------------------------------
    */

    public function destroy(Site $site, Device $device)
    {
        $this->authorizeSiteAccess($site);

        $active = SiteDevice::where('site_id', $site->id)
            ->where('device_id', $device->id)
            ->whereNull('ended_at')
            ->first();

        if (! $active) {
            return redirect()
                ->back()
                ->with('error', 'Device tidak sedang terhubung ke site ini');
        }

        DB::transaction(function () use ($active, $device) {
            $active->update([
                'ended_at' => now(),
            ]);

            $stillAssigned = SiteDevice::where('device_id', $device->id)
                ->whereNull('ended_at')
                ->exists();

            if (! $stillAssigned) {
                $device->update([
                    'status' => 'available',
                ]);
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Device berhasil dicopot');
    }

    private function attachDevice(Site $site, Device $device): bool
    {
        $activeAssignment = SiteDevice::where('device_id', $device->id)
            ->whereNull('ended_at')
            ->exists();

        if ($activeAssignment || $device->status !== 'available') {
            return false;
        }

        DB::transaction(function () use ($site, $device) {
            SiteDevice::create([
                'site_id' => $site->id,
                'device_id' => $device->id,
                'started_at' => now(),
            ]);

            $device->update([
                'status' => 'assigned',
            ]);
        });

        return true;
    }

    private function authorizeSiteAccess(Site $site): void
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $site->user_id !== $user->id) {
            abort(403);
        }
    }
}
