<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

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
}