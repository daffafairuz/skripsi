@php
    $user = auth()->user();
    $unreadNotificationsCount = 0;
    if ($user) {
        if ($user->role === 'admin') {
            $unreadNotificationsCount = \App\Models\Notification::where('is_read', false)->count();
        } else {
            $siteIds = $user->sites->pluck('id');
            $unreadNotificationsCount = \App\Models\Notification::whereIn('site_id', $siteIds)
                ->where('is_read', false)
                ->count();
        }
    }
@endphp
<div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex-shrink-0">
    <!-- Notification Bell Link -->
    <a href="{{ route('notifications') }}" 
       id="user-card-notification"
       class="relative p-1.5 text-gray-500 hover:text-red-500 hover:bg-gray-50 rounded-xl transition-all duration-200 flex items-center justify-center">
        <!-- SVG Bell -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <!-- Red Badge -->
        @if($unreadNotificationsCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold px-1 rounded-full min-w-[16px] h-[16px] flex items-center justify-center shadow-md animate-pulse">
                {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
            </span>
        @endif
    </a>

    <!-- Divider Line -->
    <div class="h-6 w-[1px] bg-gray-100"></div>

    <div class="text-right hidden sm:block">
        <p class="text-xs font-bold text-gray-800 leading-none">
            {{ auth()->user()->name ?? 'User' }}
        </p>
        <p class="text-[10px] text-blue-500 font-medium italic">
            {{ $subtitle ?? (auth()->user()->role === 'admin' ? 'Administrator' : 'User') }}
        </p>
    </div>
    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-100 flex-shrink-0">
        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
    </div>
</div>
