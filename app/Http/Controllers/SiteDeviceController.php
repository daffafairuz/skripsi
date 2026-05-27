<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Site;
use App\Services\SiteDeviceService;
use Illuminate\Http\Request;
use Exception;

class SiteDeviceController extends Controller
{
    protected $siteDeviceService;

    /**
     * Create a new controller instance.
     *
     * @param SiteDeviceService $siteDeviceService
     */
    public function __construct(SiteDeviceService $siteDeviceService)
    {
        $this->siteDeviceService = $siteDeviceService;
    }

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

        try {
            $device = Device::findOrFail($request->device_id);
            $this->siteDeviceService->attachDevice($site, $device);

            return redirect()
                ->route('devices.index', [
                    'site_id' => $site->id,
                ])
                ->with('success', 'Device berhasil ditambahkan');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
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

        try {
            $device = Device::findOrFail($request->device_id);
            $this->siteDeviceService->attachDevice($site, $device);

            return redirect()
                ->back()
                ->with('success', 'Device berhasil dihubungkan');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Copot Device
    |--------------------------------------------------------------------------
    */

    public function destroy(Site $site, Device $device)
    {
        $this->authorizeSiteAccess($site);

        try {
            $this->siteDeviceService->detachDevice($site, $device);

            return redirect()
                ->back()
                ->with('success', 'Device berhasil dicopot');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Authorize access to a specific site.
     *
     * @param Site $site
     * @return void
     */
    private function authorizeSiteAccess(Site $site): void
    {
        $user = auth()->user();

        if ($user->role !== 'admin' && $site->user_id !== $user->id) {
            abort(403);
        }
    }
}
