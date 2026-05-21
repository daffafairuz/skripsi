@extends('layouts.app')

@section('content')

<!-- Header Section with Sleek Glassmorphic Styling -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Data Sensor</h1>
        <p class="text-sm text-gray-500">Log telemetri berkala (interval 5 menit) dari ESP32 Slave yang terhubung ke ESP32 Master (Site)</p>
    </div>
    
    @include('layouts.user-card', ['subtitle' => 'Telemetry Hub Active'])
</div>

<!-- Filter Section -->
@if(isset($sites) && !$sites->isEmpty())
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-8 transition duration-300 hover:shadow-md">
        <form action="{{ route('data-sensor') }}" method="GET" class="flex flex-col md:flex-row items-end gap-5">
            <!-- Filter Site -->
            <div class="flex-1 w-full">
                <label for="site-select" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filter Site</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <select id="site-select" name="site_id" onchange="document.getElementById('device-select').value = ''; this.form.submit();"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none cursor-pointer hover:bg-gray-50">
                        <option value="">Semua Site</option>
                        @foreach($sites as $s)
                            <option value="{{ $s->id }}" {{ $selectedSiteId == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filter Device -->
            <div class="flex-1 w-full">
                <label for="device-select" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filter Device</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <select id="device-select" name="device_id" onchange="this.form.submit();"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none cursor-pointer hover:bg-gray-50">
                        <option value="">Semua Device</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}" {{ $selectedDeviceId == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tampilkan Baris -->
            <div class="w-full md:w-36">
                <label for="per-page-select" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tampilkan</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </span>
                    <select id="per-page-select" name="per_page" onchange="this.form.submit();"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none cursor-pointer hover:bg-gray-50">
                        @foreach([10, 30, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }} Baris</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            @if($selectedSiteId || $selectedDeviceId || $perPage != 10)
                <div class="w-full md:w-auto">
                    <a href="{{ route('data-sensor') }}"
                       class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl text-sm font-bold transition-all shadow-sm hover:shadow active:scale-95">
                        Reset
                    </a>
                </div>
            @endif
        </form>
    </div>
@endif

<!-- Main Telemetry Grid -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

    <!-- Toolbar & Summary Stats -->
    <div class="p-5 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gray-50/50 border-b border-gray-100">
        <div class="text-sm text-gray-500 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Total <span class="font-bold text-gray-700 font-mono">{{ $pivotedData->total() }}</span> baris log terintegrasi (interval 5 menit)
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
                    @foreach($activeSensorColumns as $colKey => $colInfo)
                        <th class="py-4 px-3 border-b border-gray-100 text-center">
                            {{ $colInfo['label'] }} @if($colInfo['unit'])<span class="text-[8px] font-normal">({{ $colInfo['unit'] }})</span>@endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-xs divide-y divide-gray-50">

                @forelse($pivotedData as $index => $row)
                <tr class="hover:bg-blue-50/20 transition-colors group">

                    <!-- Row Number -->
                    <td class="py-3 px-4 text-gray-400 font-mono text-[11px] border-b border-gray-100/50">
                        {{ $pivotedData->firstItem() + $index }}
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

                    <!-- Dynamic Sensor columns -->
                    @foreach($activeSensorColumns as $colKey => $colInfo)
                        <td class="py-3 px-3 text-center border-b border-gray-100/50">
                            @if($row['values'][$colKey] !== null)
                                <span class="inline-flex items-center px-2 py-0.5 {{ $colInfo['bg_color'] }} rounded-md font-semibold text-[11px] font-mono shadow-sm">
                                    {{ sprintf($colInfo['format'], $row['values'][$colKey]) }}
                                    @if($colInfo['unit'])
                                        <span class="text-[8px] ml-0.5 font-normal">{{ $colInfo['unit'] }}</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-300 font-mono">-</span>
                            @endif
                        </td>
                    @endforeach

                </tr>
                @empty
                <tr>
                    <td colspan="{{ 3 + count($activeSensorColumns) }}" class="py-12 px-6 text-center text-gray-400">
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
    <div class="p-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
        <span>Menampilkan {{ $pivotedData->firstItem() ?? 0 }} - {{ $pivotedData->lastItem() ?? 0 }} dari {{ $pivotedData->total() }} baris pengukuran berkala</span>
        <div class="px-2 normal-case">
            {{ $pivotedData->links() }}
        </div>
    </div>
</div>

@endsection