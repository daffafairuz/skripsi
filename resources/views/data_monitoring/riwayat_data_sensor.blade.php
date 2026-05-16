@extends('layouts.app')

@section('content')

<!-- Header Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Data Sensor</h1>
        <p class="text-sm text-gray-500">Log pembacaan parameter lingkungan secara real-time</p>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-gray-800 leading-none">{{ auth()->user()->name ?? 'Administrator' }}</p>
            <p class="text-[10px] text-blue-500 font-medium italic">Monitoring Active</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-100">
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        </div>
    </div>
</div>

<!-- Main Container dengan Alpine.js untuk Tab -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden" x-data="{ activeTab: 'all' }">

    <!-- Toolbar: Search & Export -->
    <div class="p-5 flex flex-col sm:flex-row justify-between items-center gap-4 bg-white">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </span>
            <input type="text" placeholder="Cari berdasarkan tanggal..." 
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs focus:ring-2 focus:ring-blue-500/20 transition">
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition shadow-lg shadow-green-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Table Area -->
    <div class="overflow-y-auto max-h-[600px] scrollbar-thin scrollbar-thumb-gray-200">
        <table class="w-full text-left border-separate border-spacing-0">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-md z-10 shadow-sm">
                <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">
                    <th class="py-4 px-6 border-b border-gray-100">ID Log</th>
                    <th class="py-4 px-6 border-b border-gray-100">Waktu</th>
                    <!-- Kolom Parameter akan beradaptasi secara visual -->
                    <th class="py-4 px-6 border-b border-gray-100 text-center">Suhu</th>
                    <th class="py-4 px-6 border-b border-gray-100 text-center">Kelembapan</th>
                    <th class="py-4 px-6 border-b border-gray-100 text-center">Level Air</th>
                    <th class="py-4 px-6 border-b border-gray-100 text-center">pH Air</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-50">
                @for ($i = 100; $i >= 80; $i--)
                <tr class="hover:bg-blue-50/40 transition-colors group">
                    <td class="py-4 px-6 text-gray-400 font-mono text-xs">#SR-{{ $i }}</td>
                    <td class="py-4 px-6">
                        <div class="flex flex-col">
                            <span class="text-gray-700 font-semibold text-xs">2026-05-16</span>
                            <span class="text-[10px] text-gray-400">10:{{ $i - 50 }}:00 WIB</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="px-2.5 py-1 bg-orange-50 text-orange-600 rounded-lg font-bold text-xs">28.{{ $i }}°C</span>
                    </td>
                    <td class="py-4 px-6 text-center text-gray-600 font-medium">75%</td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-1.5 h-4 bg-blue-100 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-1/2 w-full"></div>
                            </div>
                            <span class="text-gray-600">15 cm</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="px-2 py-0.5 border border-green-200 text-green-600 rounded text-xs font-bold">6.8</span>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Pagination / Summary Footer -->
    {{-- <div class="p-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <span class="text-[11px] text-gray-400 font-bold uppercase tracking-tighter">
            Menampilkan log terakhir (Updated 1 min ago)
        </span>
        <div class="flex items-center gap-2">
            <nav class="flex gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-xs shadow-md shadow-blue-100">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-xs transition">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </nav>
        </div>
    </div> --}}
</div>

@endsection