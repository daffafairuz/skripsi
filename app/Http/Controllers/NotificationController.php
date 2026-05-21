<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Site;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // ADMIN -> lihat semua notifikasi
        if ($user->role === 'admin') {
            $notifications = Notification::latest()->get();
            // Mark all unread notifications as read
            Notification::where('is_read', false)->update(['is_read' => true]);
        } else {
            // Ambil semua site milik user
            $siteIds = $user->sites->pluck('id');
            // Ambil notifikasi berdasarkan site user
            $notifications = Notification::whereIn('site_id', $siteIds)
                ->latest()
                ->get();
            // Mark all user's site unread notifications as read
            Notification::whereIn('site_id', $siteIds)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
        return view('notification', compact('notifications'));
    }

    public function sensorAlert(Request $request)
    {
        $site = Site::find($request->site_id);

        if(!$site){
            return response()->json([
                'message'=>'Site tidak ditemukan'
            ],404);
        }

        // anti spam
        $exists=Notification::where(
            'site_id',
            $site->id
        )
        ->where(
            'message',
            $request->message
        )
        ->where(
            'created_at',
            '>=',
            now()->subMinutes(30)
        )
        ->exists();

        if(!$exists){

            $notification=Notification::create([
                'site_id'=>$site->id,
                'message'=>$request->message,
                'type'=>'warning',
                'is_read'=>false
            ]);

            Mail::to(
                $site->user->email
            )->send(
                new NotificationMail($notification)
            );
        }

        return response()->json([
            'message'=>'Notification sent'
        ]);
    }
}