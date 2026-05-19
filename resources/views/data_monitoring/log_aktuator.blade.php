@extends('layouts.app')

@section('content')

<!-- Header Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Actuator History Logs</h1>
        <p class="text-sm text-gray-500">Menampilkan hingga 500 riwayat aktivitas terakhir untuk setiap perangkat</p>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-gray-800 leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
            <p class="text-[10px] text-green-500 font-medium">Online</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold shadow-lg shadow-green-100">
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ activeTab: 'all' }">
    
    <!-- Tab Navigation Bar -->
    <div class="flex flex-wrap border-b border-gray-100 bg-gray-50/50 p-3 gap-2">
        <button @click="activeTab = 'all'" 
            :class="activeTab === 'all' ? 'bg-white shadow-md text-gray-800 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-gray-500 shadow-[0_0_8px_rgba(107,114,128,0.5)]"></span>
            Semua ({{ $logs->count() }})
        </button>

        <button @click="activeTab = 'waterpump'" 
            :class="activeTab === 'waterpump' ? 'bg-white shadow-md text-blue-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
            Pompa Air ({{ ($groupedLogs['waterpump'] ?? collect())->count() }})
        </button>
        
        <button @click="activeTab = 'growlight'" 
            :class="activeTab === 'growlight' ? 'bg-white shadow-md text-purple-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]"></span>
            Grow Light ({{ ($groupedLogs['growlight'] ?? collect())->count() }})
        </button>

        <button @click="activeTab = 'aerator'" 
            :class="activeTab === 'aerator' ? 'bg-white shadow-md text-cyan-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.5)]"></span>
            Aerator ({{ ($groupedLogs['aerator'] ?? collect())->count() }})
        </button>

        <button @click="activeTab = 'feeder'" 
            :class="activeTab === 'feeder' ? 'bg-white shadow-md text-amber-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
            Feeder ({{ ($groupedLogs['feeder'] ?? collect())->count() }})
        </button>
    </div>

    <!-- Table Area -->
    <div class="overflow-y-auto max-h-[600px] border-t border-gray-50 scrollbar-thin scrollbar-thumb-gray-200">
        <table class="w-full text-left border-separate border-spacing-0">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-md z-10 shadow-sm">
                <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">
                    <th class="py-4 px-6 border-b border-gray-100">Waktu Kejadian</th>
                    <th class="py-4 px-6 border-b border-gray-100">Aktuator</th>
                    <th class="py-4 px-6 border-b border-gray-100">Status</th>
                    <th class="py-4 px-6 border-b border-gray-100">Metode Kontrol</th>
                    <th class="py-4 px-6 border-b border-gray-100">Device</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-50">

                @forelse($logs as $log)
                @php
                    $logType = strtolower($log->actuator->type ?? '');
                    $tabCategory = 'other';
                    if (str_contains($logType, 'pump') || str_contains($logType, 'pompa')) $tabCategory = 'waterpump';
                    elseif (str_contains($logType, 'light') || str_contains($logType, 'grow')) $tabCategory = 'growlight';
                    elseif (str_contains($logType, 'aerator') || str_contains($logType, 'aera')) $tabCategory = 'aerator';
                    elseif (str_contains($logType, 'feeder') || str_contains($logType, 'feed')) $tabCategory = 'feeder';
                @endphp
                <tr x-show="activeTab === 'all' || activeTab === '{{ $tabCategory }}'"
                    class="hover:bg-blue-50/40 transition-colors group">

                    <td class="py-4 px-6">
                        <div class="flex flex-col">
                            <span class="text-gray-700 font-medium">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-gray-400 italic">{{ $log->created_at->format('H:i:s') }} WIB</span>
                        </div>
                    </td>

                    <td class="py-4 px-6">
                        <span class="text-xs font-semibold text-gray-700">{{ $log->actuator->name ?? '-' }}</span>
                    </td>

                    <td class="py-4 px-6">
                        @if($log->action === 'on')
                            <span class="bg-green-100 text-green-700 text-[10px] px-3 py-1 rounded-lg font-black tracking-widest">ON</span>
                        @else
                            <span class="bg-red-50 text-red-600 text-[10px] px-3 py-1 rounded-lg font-black tracking-widest">OFF</span>
                        @endif
                    </td>

                    <td class="py-4 px-6">
                        @if($log->triggered_by === 'manual')
                            <span class="text-xs text-gray-600 bg-blue-50 px-2 py-1 rounded-md font-medium">Manual</span>
                        @else
                            <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-md font-medium">Auto-Sensor</span>
                        @endif
                    </td>

                    <td class="py-4 px-6 text-gray-500 text-xs">
                        {{ $log->actuator->device->name ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 px-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-semibold">Belum ada log aktuator</p>
                        <p class="text-sm">Data akan muncul setelah aktuator dioperasikan</p>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Table Footer -->
    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
        <p class="text-[11px] text-gray-400 font-medium uppercase">Menampilkan {{ $logs->count() }} log terbaru</p>
    </div>
</div>

@endsection