<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Site;
use App\Models\Device;
use App\Models\SensorData;
use App\Models\Notification;

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
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if($user->role=="admin")
        {
            $adminStats=[

                'total_sites'=>Site::count(),

                'total_users'=>
                User::where(
                    'role',
                    'user'
                )->count(),

                'total_devices'=>
                Device::count(),

                'total_sensor_data'=>
                SensorData::count(),

                'total_notifications'=>
                Notification::count()

            ];

            /*
            |--------------------------------------------------------------------------
            | Device Dashboard
            |--------------------------------------------------------------------------
            */

            $devices=Device::with([
                'sensors',
                'actuators'
            ])->paginate(5);


            /*
            |--------------------------------------------------------------------------
            | Site list
            |--------------------------------------------------------------------------
            */

            $sites = Site::with(
                'user'
            )
            ->latest()
            ->take(5)
            ->get();


            /*
            |--------------------------------------------------------------------------
            | Notifications / aktivitas
            |--------------------------------------------------------------------------
            */

            $activities = Notification::latest()
            ->take(5)
            ->get();


            return view(
                'admin.dashboard',
                compact(
                    'adminStats',
                    'devices',
                    'sites',
                    'activities'
                )
            );

            return view(
                'admin.dashboard',
                compact(
                    'adminStats',
                    'devices'
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $site=Site::with(
            'devices'
        )
        ->where(
            'user_id',
            $user->id
        )
        ->first();

        $hasSite=
        $site!=null;


        $userStats=[

            'total_sites'=>

            $site
            ?1
            :0,

            'total_devices'=>

            $site
            ?$site
                ->devices
                ->count()
            :0,

            'latest_sensor'=>

            SensorData::latest()
            ->first()
            ?->value ?? '-',

            'total_notifications'=>

            Notification::where(
                'user_id',
                $user->id
            )
            ->count()

        ];

        return view(
            'user.dashboard',
            compact(
                'hasSite',
                'userStats'
            )
        );
    }



    /*
    |--------------------------------------------------------------------------
    | CHART
    |--------------------------------------------------------------------------
    */

    public function chartData()
    {
        $data=
        SensorData::latest()
        ->take(20)
        ->get()
        ->reverse();

        return response()->json([

            'labels'=>

            $data
            ->map(
                fn($r)=>
                optional(
                    $r->created_at
                )
                ->format('H:i')
            )
            ->values(),


            'temperature'=>

            $data
            ->pluck(
                'temperature'
            )
            ->map(function($v){

                $v=(float)$v;

                return $v==-127
                ?null
                :$v;

            })
            ->values(),


            'ph'=>

            $data
            ->pluck(
                'ph'
            )
            ->map(
                fn($v)=>
                (float)$v
            )
            ->values()

        ]);
    }
}