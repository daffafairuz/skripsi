<?php

namespace App\Http\Controllers;

use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {

            $notifications =
            Notification::latest()
            ->get();

            Notification::where(
                'is_read',
                false
            )
            ->update([
                'is_read'=>true
            ]);

        } else {

            $siteIds =
            $user->sites
            ->pluck('id');

            $notifications =
            Notification::whereIn(
                'site_id',
                $siteIds
            )
            ->latest()
            ->get();

            Notification::whereIn(
                'site_id',
                $siteIds
            )
            ->where(
                'is_read',
                false
            )
            ->update([
                'is_read'=>true
            ]);

        }

        return view(
            'notification',
            compact(
                'notifications'
            )
        );
    }


    public function sensorAlert(
        Request $request
    )
    {

        $secret =
        config(
            'services.sensor_alert.secret'
        );

        if(blank($secret)){

            return response()->json([

                'message' =>
                'Sensor alert secret belum dikonfigurasi'

            ],503);

        }


        if(
            !hash_equals(
                $secret,
                (string)
                $request->header(
                    'X-Sensor-Alert-Secret'
                )
            )
        ){

            return response()->json([

                'message'=>'Unauthorized'

            ],403);

        }


        $validated=
        $request->validate([

            'site_id'=>
            [
                'required',
                'integer',
                'exists:sites,id'
            ],

            'message'=>
            [
                'required',
                'string',
                'max:1000'
            ],

            'type'=>
            [
                'nullable',
                'in:info,warning,alert'
            ]

        ]);


        $site=
        Site::with(
            'user'
        )
        ->find(
            $validated['site_id']
        );


        if(!$site){

            return response()->json([

                'message'=>
                'Site tidak ditemukan'

            ],404);

        }


        // =====================
        // ANTI SPAM
        // =====================

        $exists=
        Notification::where(
            'site_id',
            $site->id
        )
        ->where(
            'message',
            $validated['message']
        )
        ->where(
            'created_at',
            '>=',
            now()->subMinutes(
                30
            )
        )
        ->exists();


        if(!$exists){

            $notification=
            Notification::create([

                'site_id'=>
                $site->id,

                'message'=>
                $validated['message'],

                'type'=>
                $validated['type']
                ?? 'warning',

                'is_read'=>
                false

            ]);


            // penting
            $notification->load(
                'site'
            );


            if ($site->user->email_notification) {
                try{

                    Mail::to(
                        $site->user->email
                    )
                    ->send(
                        new NotificationMail(
                            $notification
                        )
                    );

                }
                catch(Throwable $e) {

                    report($e);
                
                    return response()->json([
                
                        'message'=>
                        'Notification saved, but email failed',
                
                        'created'=>
                        true,
                
                        'notification_id'=>
                        $notification->id,
                
                        'error'=>
                        $e->getMessage(),
                
                        'file'=>
                        basename(
                            $e->getFile()
                        ),
                
                        'line'=>
                        $e->getLine()
                
                    ],202);
                
                }
            }

        }


        return response()->json([

            'message'=>
            'Notification sent',

            'created'=>
            !$exists,

            'notification_id'=>
            $notification->id ?? null

        ]);

    }
}