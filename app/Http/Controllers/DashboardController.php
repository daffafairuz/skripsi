<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | STATIC DATA DULU
        |--------------------------------------------------------------------------
        | Nanti tinggal diganti query database
        */

        // USER
        $hasSite = false;

        // ADMIN STATS
        $adminStats = [
            'total_sites' => 0,
            'total_users' => 0,
            'total_devices' => 0,
            'total_sensor_data' => 0,
            'total_notifications' => 0,
        ];

        // USER STATS
        $userStats = [
            'total_sites' => 0,
            'total_devices' => 0,
            'latest_sensor' => '-',
            'total_notifications' => 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | ROLE BASED VIEW
        |--------------------------------------------------------------------------
        */

        if ($user->role === 'admin') {

            return view('dashboard', [
                'hasSite' => true,
                'adminStats' => $adminStats,
                'userStats' => $userStats,
            ]);
        }

        return view('dashboard', [
            'hasSite' => $hasSite,
            'adminStats' => $adminStats,
            'userStats' => $userStats,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHART DATA
    |--------------------------------------------------------------------------
    */

    public function chartData()
    {
        $data = SensorData::latest()
            ->take(20)
            ->get()
            ->reverse();

        return response()->json([

            'labels' => $data
                ->map(fn($r) => optional($r->created_at)->format('H:i'))
                ->values(),

            'temperature' => $data
                ->pluck('temperature')
                ->map(function ($v) {

                    $v = (float) $v;

                    return ($v == -127)
                        ? null
                        : $v;
                })
                ->values(),

            'ph' => $data
                ->pluck('ph')
                ->map(fn($v) => (float) $v)
                ->values(),
        ]);
    }
}