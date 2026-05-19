@extends('layouts.app')

@section('content')

<!-- Header Section with Sleek Glassmorphic Styling -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Data Sensor</h1>
        <p class="text-sm text-gray-500">Log telemetri berkala (interval 5 menit) dari ESP32 Slave yang terhubung ke ESP32 Master (Site)</p>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-gray-800 leading-none">{{ auth()->user()->name ?? 'User' }}</p>
            <p class="text-[10px] text-blue-500 font-medium italic">Telemetry Hub Active</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-100">
            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
        </div>
    </div>
</div>

<!-- Main Telemetry Grid -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

    <!-- Toolbar & Summary Stats -->
    <div class="p-5 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 border-b border-gray-100">
        <div class="text-sm text-gray-500 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Total <span class="font-bold text-gray-700 font-mono">{{ $pivotedData->count() }}</span> baris log terintegrasi (interval 5 menit)
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 bg-white border border-gray-200 px-3 py-1.5 rounded-xl font-medium shadow-sm">
                Status: Synchronized
            </span>
        </div>
    </div>

    <!-- Interactive Time-series pivoted table -->
    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-200">
        <table class="w-full text-left border-separate border-spacing-0 min-w-[1200px]">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-md z-10 shadow-sm">
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                    <th class="py-4 px-4 border-b border-gray-100">#</th>
                    <th class="py-4 px-4 border-b border-gray-100">Waktu</th>
                    <th class="py-4 px-5 border-b border-gray-100">Slave Device / Site (Master)</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">Suhu</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">Kelembapan</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">pH Air</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">TDS</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">Water Level</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">DO</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">EC</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">Soil Moist.</th>
                    <th class="py-4 px-3 border-b border-gray-100 text-center">Cahaya</th>
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-gray-50">

                @forelse($pivotedData as $index => $row)
                <tr class="hover:bg-blue-50/20 transition-colors group">

                    <!-- Row Number -->
                    <td class="py-3 px-4 text-gray-400 font-mono text-[11px] border-b border-gray-100/50">
                        {{ $loop->iteration }}
                    </td>

                    <!-- Timestamp Group -->
                    <td class="py-3 px-4 border-b border-gray-100/50">
                        @php
                            $dt = \Carbon\Carbon::parse($row['waktu']);
                        @endphp
                        <div class="flex flex-col">
                            <span class="text-gray-700 font-semibold font-mono text-[11px]">{{ $dt->format('d M Y') }}</span>
                            <span class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $dt->format('H:i') }} WIB</span>
                        </div>
                    </td>

                    <!-- Slave Device and Site (Master) Relationship -->
                    <td class="py-3 px-5 border-b border-gray-100/50">
                        <div class="flex flex-col">
                            <span class="text-gray-800 font-semibold text-[11px]">{{ $row['device_name'] }}</span>
                            <span class="text-[10px] text-gray-400 flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $row['site_name'] }}
                            </span>
                        </div>
                    </td>

                    <!-- Suhu (Temperature) -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['temperature'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-orange-50 border border-orange-100 text-orange-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['temperature'], 1) }} <span class="text-[9px] ml-0.5">°C</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- Kelembapan (Humidity) -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['humidity'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-blue-50 border border-blue-100 text-blue-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['humidity'], 1) }} <span class="text-[9px] ml-0.5">%</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- pH Air -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['ph'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['ph'], 2) }}
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- TDS -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['tds'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-purple-50 border border-purple-100 text-purple-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['tds'], 0) }} <span class="text-[8px] ml-0.5">ppm</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- Water Level -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['water_level'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-cyan-50 border border-cyan-100 text-cyan-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['water_level'], 1) }} <span class="text-[8px] ml-0.5">cm</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- Dissolved Oxygen (DO) -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['dissolved_oxygen'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-teal-50 border border-teal-100 text-teal-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['dissolved_oxygen'], 1) }} <span class="text-[8px] ml-0.5">mg/L</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- EC -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['ec'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['ec'], 2) }} <span class="text-[8px] ml-0.5">mS</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- Soil Moisture -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['soil_moisture'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-rose-50 border border-rose-100 text-rose-600 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['soil_moisture'], 1) }} <span class="text-[8px] ml-0.5">%</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                    <!-- Light (Cahaya) -->
                    <td class="py-3 px-3 text-center border-b border-gray-100/50">
                        @if($row['values']['light'] !== null)
                            <span class="inline-flex items-center px-2 py-0.5 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                {{ number_format($row['values']['light'], 0) }} <span class="text-[8px] ml-0.5">lux</span>
                            </span>
                        @else
                            <span class="text-gray-300 font-mono">-</span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="12" class="py-12 px-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="font-semibold text-gray-600 text-sm">Belum ada data sensor</p>
                        <p class="text-xs text-gray-400">Pastikan Anda telah menjalankan seeder database</p>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-[10px] text-gray-400 font-bold uppercase tracking-wider">
        <span>Menampilkan {{ $pivotedData->count() }} baris pengukuran berkala</span>
        <span>Sistem Master-Slave IoT Akuaponik & Hidroponik</span>
    </div>
</div>

@endsection