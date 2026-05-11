<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | STATIC DATA DULU
        |--------------------------------------------------------------------------
        */

        $sites = [

            [
                'id' => 1,
                'name' => 'Kolam Lele A',
                'location' => 'Wonosobo',
                'owner' => 'Bima Aryono',
                'devices' => 5,
                'status' => 'active',
            ],

            [
                'id' => 2,
                'name' => 'Kolam Pakcoy B',
                'location' => 'Magelang',
                'owner' => 'Daffa Fairuz',
                'devices' => 3,
                'status' => 'inactive',
            ],

            [
                'id' => 3,
                'name' => 'Aquaponik Greenhouse',
                'location' => 'Yogyakarta',
                'owner' => 'Admin',
                'devices' => 7,
                'status' => 'active',
            ],

        ];

        return view('sites.index', compact('sites'));
    }
}