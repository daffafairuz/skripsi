<?php

namespace App\Http\Controllers;

use App\Models\Actuator;
use App\Models\ActuatorLog;
use Illuminate\Http\Request;

class ActuatorController extends Controller
{
    public function toggle(
        Request $request,
        Actuator $actuator
    )
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil status terakhir
        |--------------------------------------------------------------------------
        */

        $latest = $actuator
            ->logs()
            ->latest()
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Toggle
        |--------------------------------------------------------------------------
        */

        $action =
            ($latest &&
            $latest->action=="on")
            ? "off"
            : "on";

        /*
        |--------------------------------------------------------------------------
        | Simpan log
        |--------------------------------------------------------------------------
        */

        ActuatorLog::create([

            'actuator_id'=>$actuator->id,

            'user_id'=>auth()->id(),

            'action'=>$action,

            'triggered_by'=>'manual'

        ]);

        return response()->json([

            'success'=>true,

            'action'=>$action

        ]);
    }
}