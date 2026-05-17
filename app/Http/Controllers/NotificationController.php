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
        } else {
            // Ambil semua site milik user
            $siteIds = $user->sites->pluck('id');
            // Ambil notifikasi berdasarkan site user
            $notifications = Notification::whereIn('site_id', $siteIds)
                ->latest()
                ->get();
        }
        return view('notification', compact('notifications'));
    }
}