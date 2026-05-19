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
        $user = auth()->user();

        if ($user->role === 'admin') {
            $sites = Site::with('user', 'devices')->latest()->get();
        } else {
            $sites = Site::with('user', 'devices')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('sites.index', compact('sites'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $users = User::where('role', 'user')->get();
        return view('sites.create', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'mac_address' => 'required|string|unique:sites,mac_address',
            'user_id' => 'required|exists:users,id',
        ], [
            'name.required' => 'Nama site wajib diisi',
            'location.required' => 'Lokasi wajib diisi',
            'mac_address.required' => 'MAC Address wajib diisi',
            'mac_address.unique' => 'MAC Address sudah terdaftar',
            'user_id.required' => 'Owner wajib dipilih',
        ]);

        Site::create([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'mac_address' => $request->mac_address,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('sites.index')
            ->with('success', 'Site berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(Site $site)
    {
        $users = User::where('role', 'user')->get();
        return view('sites.edit', compact('site', 'users'));
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
            'mac_address' => 'required|string|unique:sites,mac_address,' . $site->id,
            'user_id' => 'required|exists:users,id',
        ]);

        $site->update([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'mac_address' => $request->mac_address,
            'user_id' => $request->user_id,
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