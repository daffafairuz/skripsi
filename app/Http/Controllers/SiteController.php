<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->role == 'admin') {
            $sites = Site::with([
                'user',
                'activeDevices.sensors',
                'activeDevices.actuators'
            ])->get();

            $users = User::where('role', 'user')->get();

            return view(
                'admin.sites.index',
                compact('sites', 'users')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $site = Site::with([
            'activeDevices.sensors',
            'activeDevices.actuators',
            'notifications'
        ])
        ->where('user_id', auth()->id())
        ->first();

        $totalSensors = $site
            ? $site->activeDevices->sum(function ($d) {
                return $d->sensors->count();
            })
            : 0;

        $totalActuators = $site
            ? $site->activeDevices->sum(function ($d) {
                return $d->actuators->count();
            })
            : 0;

        $hasSite = $site != null;

        return view(
            'user.sites.index',
            compact(
                'site',
                'hasSite',
                'totalSensors',
                'totalActuators'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        if (auth()->user()->role != 'admin') {
            abort(403);
        }

        $users = User::where('role', 'user')->get();

        return view(
            'admin.users.create',
            compact('users')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mac_address' => 'required|string|max:255|unique:sites,mac_address',
        ]);

        Site::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'mac_address' => $request->mac_address,
        ]);

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site berhasil dibuat');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Site $site)
    {
        $site->load([
            'activeDevices.sensors.dataSensors' => function ($query) {
                $query->latest();
            },
            'activeDevices.actuators.logs' => function ($query) {
                $query->latest();
            },
            'notifications'
        ]);

        return view(
            'user.sites.show',
            compact('site')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Site $site)
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users.edit', compact('site', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Site $site)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'mac_address' => 'required|string|max:255|unique:sites,mac_address,' . $site->id,
        ]);

        $site->update([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'user_id' => $request->user_id,
            'mac_address' => $request->mac_address,
        ]);

        return redirect()->route('sites.index')
            ->with('success', 'Site berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('sites.index')
            ->with('success', 'Site berhasil dihapus');
    }
}