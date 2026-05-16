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
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ activeTab: 'pump' }">
    
    <!-- Tab Navigation Bar -->
    <div class="flex flex-wrap border-b border-gray-100 bg-gray-50/50 p-3 gap-2">
        <button @click="activeTab = 'waterpump'" 
            :class="activeTab === 'waterpump' ? 'bg-white shadow-md text-blue-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[140px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
            Pompa Air
        </button>
        
        <button @click="activeTab = 'growlight'" 
            :class="activeTab === 'growlight' ? 'bg-white shadow-md text-purple-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[140px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]"></span>
            Grow Light
        </button>

        <button @click="activeTab = 'aerator'" 
            :class="activeTab === 'aerator' ? 'bg-white shadow-md text-cyan-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[140px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.5)]"></span>
            Aerator
        </button>

        <button @click="activeTab = 'feeder'" 
            :class="activeTab === 'feeder' ? 'bg-white shadow-md text-amber-600 scale-105' : 'text-gray-500 hover:bg-gray-100'"
            class="flex-1 min-w-[140px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3">
            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
            Feeder
        </button>
    </div>

    <!-- Table Toolbar -->
    <div class="p-5 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
        <div class="relative w-full sm:w-72">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </span>
            <input type="text" placeholder="Cari berdasarkan waktu atau admin..." 
                class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20 transition">
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none px-4 py-2 bg-gray-800 hover:bg-black text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition shadow-lg shadow-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Table Area (Optimized for 200+ rows) -->
    <div class="overflow-y-auto max-h-[600px] border-t border-gray-50 scrollbar-thin scrollbar-thumb-gray-200">
        <table class="w-full text-left border-separate border-spacing-0">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-md z-10 shadow-sm">
                <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">
                    <th class="py-4 px-6 border-b border-gray-100">Waktu Kejadian</th>
                    <th class="py-4 px-6 border-b border-gray-100">Status Baru</th>
                    <th class="py-4 px-6 border-b border-gray-100">Metode Kontrol</th>
                    <th class="py-4 px-6 border-b border-gray-100">Pelaksana</th>
                    <th class="py-4 px-6 border-b border-gray-100">Durasi Sesi</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-50">
                {{-- Data akan berubah sesuai tab. Di bawah adalah struktur baris log --}}
                @for ($i = 1; $i <= 20; $i++)
                <tr class="hover:bg-blue-50/40 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="flex flex-col">
                            <span class="text-gray-700 font-medium">16 Mei 2026</span>
                            <span class="text-[10px] text-gray-400 italic">14:22:10 WIB</span>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <template x-if="$i % 2 == 0">
                            <span class="bg-green-100 text-green-700 text-[10px] px-3 py-1 rounded-lg font-black tracking-widest">ON</span>
                        </template>
                        <span class="bg-red-50 text-red-600 text-[10px] px-3 py-1 rounded-lg font-black tracking-widest">OFF</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-md font-medium">Auto-Sensor</span>
                    </td>
                    <td class="py-4 px-6 text-gray-500 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-600">S</div>
                            System Engine
                        </div>
                    </td>
                    <td class="py-4 px-6 text-gray-400 text-xs font-mono">00:15:22</td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Table Footer / Pagination Info -->
    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
        <p class="text-[11px] text-gray-400 font-medium uppercase">Menampilkan 200 log terbaru</p>
        <div class="flex gap-1">
            <button class="p-2 hover:bg-white rounded-lg transition"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <button class="p-2 hover:bg-white rounded-lg transition"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
        </div>
    </div>
</div>

@endsection