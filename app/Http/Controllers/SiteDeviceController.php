<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Site;
use App\Models\SiteDevice;
use Illuminate\Http\Request;

class SiteDeviceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Form Tambah Device
    |--------------------------------------------------------------------------
    */

    public function create(Site $site)
    {
        /*
        | tampilkan device yang belum dipakai
        */

        $devices = Device::where(
            'status',
            'available'
        )->get();

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
    | Simpan
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Site $site
    )
    {
        $request->validate([

            'device_id'=>'required|exists:devices,id'

        ]);

        /*
        |--------------------------------------------------------------------------
        | Cegah duplicate
        |--------------------------------------------------------------------------
        */

        $exists=SiteDevice::where([
            'site_id'=>$site->id,
            'device_id'=>$request->device_id
        ])->exists();

        if($exists)
        {
            return back()
                ->with(
                    'error',
                    'Device sudah ditambahkan'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Tambah ke pivot
        |--------------------------------------------------------------------------
        */

        SiteDevice::create([

            'site_id'=>$site->id,

            'device_id'=>$request->device_id,

            'started_at'=>now()

        ]);

        /*
        |--------------------------------------------------------------------------
        | Update status
        |--------------------------------------------------------------------------
        */

        Device::find(
            $request->device_id
        )
        ->update([

            'status'=>'assigned'

        ]);

        return redirect()
            ->route(
                'sites.index'
            )
            ->with(
                'success',
                'Device berhasil ditambahkan'
            );
    }
}