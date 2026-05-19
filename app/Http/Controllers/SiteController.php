<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
class SiteController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if(auth()->user()->role=='admin')
        {
            $sites = Site::with([
                'user',
                'devices.sensors',
                'devices.actuators'
            ])->get();

            $users = User::where(
                'role',
                'user'
            )->get();

            return view(
                'admin.sites.index',
                compact(
                    'sites',
                    'users'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $site=Site::with([
            'devices.sensors',
            'devices.actuators',
            'feedSchedules',
            'notifications'
        ])
        ->where(
            'user_id',
            auth()->id()
        )
        ->first();

        $totalSensors = $site
            ? $site->devices->sum(function($d){
                return $d->sensors->count();
            })
            : 0;

        $totalActuators = $site
            ? $site->devices->sum(function($d){
                return $d->actuators->count();
            })
            : 0;

        $hasSite=$site!=null;

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
        if(auth()->user()->role!='admin')
        {
            abort(403);
        }

        $users=User::where(
            'role',
            'user'
        )->get();

        return view(
            'admin.sites.create',
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

            'user_id'=>'required',

            'name'=>'required',

            'location'=>'required',

            'description'=>'nullable'

        ]);

        Site::create([

            'user_id'=>$request->user_id,

            'name'=>$request->name,

            'location'=>$request->location,

            'description'=>$request->description

        ]);

        return redirect()
            ->route('sites.index')
            ->with(
                'success',
                'Site berhasil dibuat'
            );
    }

    public function show(Site $site)
    {
        $site->load([

            'devices.sensors.dataSensors' => function($query){

                $query->latest();

            },

            'devices.actuators.logs' => function($query){

                $query->latest();

            },

            'notifications',
            'feedSchedules'

        ]);

        return view(
            'user.sites.show',
            compact('site')
        );
    }
    
}