@extends('layouts.app')

@section('content')

<!-- User Dashboard Container -->
<div class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- HEADER & SITE SELECTOR -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
    
    <!-- Kolom Kiri: Judul dan Selector (Jika ada Site) -->
    <div class="space-y-5 w-full md:w-auto">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Dashboard Pemantauan</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau kondisi ekosistem akuaponik Anda secara real-time</p>
        </div>

        @if($hasSite)
            <form action="{{ url('/dashboard') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <label for="site-select" class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">Pilih Site:</label>
                <div class="relative w-full md:w-64">
                    <select id="site-select" name="site_id" onchange="this.form.submit()" 
                            class="w-full pl-4 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                        @foreach($sites as $s)
                            <option value="{{ $s->id }}" {{ $site && $site->id === $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </form>
        @endif
    </div>

    <!-- Kolom Kanan: User Card -->
    <div class="w-full md:w-auto flex justify-end">
        @if($hasSite)
            @include('layouts.user-card', ['subtitle' => 'Dashboard'])
        @else
            @include('layouts.user-card', ['subtitle' => 'No Site Connected'])
        @endif
    </div>
</div>

    @if(!$hasSite)
        <!-- EMPTY STATE (NO SITES) -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 text-center max-w-2xl mx-auto my-12">
            <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Site Terhubung</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Anda belum memiliki site pemantauan yang aktif. Hubungkan site baru untuk mulai memantau pH, suhu, dan parameter akuaponik lainnya.
            </p>
            <a href="{{ route('sites.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-semibold px-6 py-3 rounded-2xl shadow-md hover:shadow-lg transition-all transform active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Site Baru
            </a>
        </div>
    @else

        <!-- STATS KPI GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- STATS 1: DEVICES -->
            <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
                <div class="p-3 bg-blue-50 text-blue-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perangkat</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $userStats['total_devices'] }}</p>
                </div>
            </div>

            <!-- STATS 2: SENSORS -->
            <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
                <div class="p-3 bg-indigo-50 text-indigo-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Sensor</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $userStats['total_sensors'] }}</p>
                </div>
            </div>

            <!-- STATS 3: ACTIVE ACTUATORS -->
            <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
                <div class="p-3 bg-amber-50 text-amber-500 rounded-2xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 113.536 0V21h2v-2.243a5 5 0 013.536 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Aktuator ON</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $userStats['active_actuators'] }}</p>
                </div>
            </div>

            <!-- STATS 4: WARNINGS / OUT OF BOUNDS -->
            <div class="p-5 rounded-3xl border shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300
                {{ $userStats['warnings'] > 0 ? 'bg-rose-50 border-rose-100 text-rose-800' : 'bg-white border-gray-100/80' }}">
                <div class="p-3 rounded-2xl {{ $userStats['warnings'] > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-500' }}">
                    @if($userStats['warnings'] > 0)
                        <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider {{ $userStats['warnings'] > 0 ? 'text-rose-500' : 'text-gray-400' }}">Alert / Peringatan</p>
                    <p class="text-2xl font-bold mt-0.5 {{ $userStats['warnings'] > 0 ? 'text-rose-700' : 'text-gray-900' }}">
                        {{ $userStats['warnings'] }}
                    </p>
                </div>
            </div>

        </div>

        <!-- MAIN GRID: SENSOR TELEMETRY & CHARTS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT/MIDDLE (2 COLUMNS): REAL-TIME METRICS & CHARTS -->
            <div class="lg:col-span-2 space-y-6">

                <!-- SENSOR LIST -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Pembacaan Sensor Terkini</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Nilai parameter aktual pada site {{ $site->name }}</p>
                        </div>
                        <span class="text-[11px] bg-emerald-50 text-emerald-600 font-bold px-3 py-1 rounded-full border border-emerald-100 animate-pulse">
                            Real-time
                        </span>
                    </div>

                    @if(empty($sensorsList))
                        <div class="text-center py-10 border border-dashed rounded-2xl text-gray-400">
                            Tidak ada sensor yang terpasang pada site ini.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($sensorsList as $sensor)
                                <div class="relative overflow-hidden bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded-2xl p-5 transition duration-200">
                                    
                                    <!-- Sensor Top Row: Icon & Status -->
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="p-2.5 rounded-xl bg-white border border-gray-100 text-gray-600 shadow-sm">
                                            @if($sensor['type'] === 'temperature')
                                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z"/>
                                                </svg>
                                            @elseif($sensor['type'] === 'ph')
                                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 5H9l-1-5z"/>
                                                </svg>
                                            @elseif($sensor['type'] === 'humidity')
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                            @elseif($sensor['type'] === 'tds')
                                                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 5H9l-1-5z"/>
                                                </svg>
                                            @elseif($sensor['type'] === 'water_level')
                                                <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </div>

                                        @if($sensor['latest_value'] === null)
                                            <span class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-md uppercase tracking-wider">OFFLINE</span>
                                        @elseif($sensor['status'] === 'normal')
                                            <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md uppercase tracking-wider">Normal</span>
                                        @else
                                            <span class="text-[10px] font-bold bg-rose-100 text-rose-700 px-2 py-0.5 rounded-md uppercase tracking-wider animate-pulse">Luar Batas</span>
                                        @endif
                                    </div>

                                    <!-- Sensor Info -->
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-600 truncate">{{ $sensor['name'] }}</h3>
                                        <div class="flex items-baseline gap-1.5 mt-1">
                                            <span class="text-3xl font-extrabold text-gray-900 tracking-tight">
                                                {{ $sensor['latest_value'] !== null ? $sensor['latest_value'] : '-' }}
                                            </span>
                                            <span class="text-sm font-semibold text-gray-500">
                                                {{ $sensor['unit'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Threshold details -->
                                    <div class="mt-4 pt-3 border-t border-gray-100 text-[11px] text-gray-400 flex justify-between items-center">
                                        <span>Batas: {{ $sensor['min_threshold'] ?? 0 }} - {{ $sensor['max_threshold'] ?? 0 }} {{ $sensor['unit'] }}</span>
                                        <span class="text-[10px]">
                                            @if($sensor['updated_at'])
                                                {{ $sensor['updated_at']->diffForHumans() }}
                                            @else
                                                Belum ada data
                                            @endif
                                        </span>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- CHART COMPONENT -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Grafik Pemantauan Historis</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Tren data sensor selama 24 jam terakhir</p>
                        </div>
                        <div class="p-1 bg-gray-50 border border-gray-100 rounded-xl flex gap-1">
                            <button onclick="refreshChart()" class="p-1.5 hover:bg-white rounded-lg text-gray-500 hover:text-gray-900 shadow-sm hover:shadow transition-all" title="Segarkan Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- CHART CANVAS -->
                    <div class="relative h-80 w-full">
                        <canvas id="site-telemetry-chart"></canvas>
                    </div>
                </div>

            </div>

            <!-- RIGHT (1 COLUMN): SITE DETAILS, ACTUATOR STATUS & NOTIFICATIONS -->
            <div class="space-y-6">

                <!-- SITE DETAILS CARD -->
                <div class="bg-gradient-to-br from-emerald-600 to-teal-800 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
                    <!-- Deco Circle -->
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>

                    <div class="relative">
                        <span class="text-[10px] uppercase font-bold tracking-widest bg-white/10 px-3 py-1 rounded-full">Site Info</span>
                        
                        <h2 class="text-xl font-extrabold mt-4 mb-2 truncate">{{ $site->name }}</h2>
                        <p class="text-xs text-emerald-100/80 leading-relaxed min-h-[40px] line-clamp-2">{{ $site->description ?? 'Tidak ada deskripsi untuk site ini.' }}</p>

                        <div class="mt-6 pt-5 border-t border-white/10 space-y-3 text-xs">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span class="truncate text-emerald-100/90" title="{{ $site->location }}">{{ $site->location }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-mono text-emerald-100/90">{{ $site->mac_address ?? 'MAC: -' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTUATOR CONTROLS PREVIEW -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                    <div class="flex justify-between items-center mb-5">
                        <div>
                            <h2 class="text-sm font-bold text-gray-800">Kontrol Aktuator</h2>
                            <p class="text-[11px] text-gray-400">Status kendali perangkat keras</p>
                        </div>
                        <a href="{{ route('actuator-control') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                            Kelola Semua &rarr;
                        </a>
                    </div>

                    @php
                        $actuators = collect();
                        foreach($site->devices as $d) {
                            foreach($d->actuators as $act) {
                                $lastLog = $act->logs->first();
                                $act->current_state = $lastLog ? $lastLog->action : $act->default_state;
                                $actuators->push($act);
                            }
                        }
                    @endphp

                    @if($actuators->isEmpty())
                        <div class="text-center py-6 border border-dashed rounded-2xl text-xs text-gray-400">
                            Tidak ada aktuator terpasang pada site ini.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($actuators->take(3) as $actuator)
                                <div class="flex items-center justify-between p-3 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded-2xl transition">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-xl {{ $actuator->current_state === 'on' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                            @if(str_contains(strtolower($actuator->type), 'pump') || str_contains(strtolower($actuator->name), 'pompa'))
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M9.172 15.828a4 4 0 010-5.656m5.656 0a4 4 0 010 5.656M12 12h.01"/>
                                                </svg>
                                            @elseif(str_contains(strtolower($actuator->type), 'light'))
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-700 truncate max-w-[130px]" title="{{ $actuator->name }}">{{ $actuator->name }}</p>
                                            <p class="text-[10px] text-gray-400 capitalize">{{ $actuator->type }}</p>
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('actuator-control.toggle', $actuator->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3 py-1 rounded-xl text-[10px] font-bold tracking-wide transition-all
                                                {{ $actuator->current_state === 'on' ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-gray-200 text-gray-600 hover:bg-gray-300' }}">
                                            {{ strtoupper($actuator->current_state) }}
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- NOTIFICATION FEED -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                    <div class="flex justify-between items-center mb-5">
                        <div>
                            <h2 class="text-sm font-bold text-gray-800">Aktivitas & Alert Terbaru</h2>
                            <p class="text-[11px] text-gray-400">Notifikasi sistem log terbaru</p>
                        </div>
                        <a href="{{ route('notifications') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                            Semua &rarr;
                        </a>
                    </div>

                    @if($notifications->isEmpty())
                        <div class="text-center py-6 text-xs text-gray-400">
                            Belum ada aktivitas di site ini.
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($notifications as $n)
                                <div class="flex gap-3">
                                    <div class="w-1.5 h-1.5 mt-1.5 rounded-full flex-shrink-0
                                        {{ $n->type === 'alert' ? 'bg-red-500' : ($n->type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500') }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-600 leading-snug break-words">
                                            {{ $n->message }}
                                        </p>
                                        <span class="text-[9px] text-gray-400 mt-1 block">
                                            {{ $n->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

        </div>

    @endif

</div>

@endsection

@if($hasSite && $site)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let telemetryChart = null;

    function renderTelemetryChart(labels, datasets) {
        const ctx = document.getElementById('site-telemetry-chart').getContext('2d');
        
        if (telemetryChart) {
            telemetryChart.destroy();
        }

        // Apply dynamic theme enhancements
        const styledDatasets = datasets.map(ds => {
            // Create nice area gradient fill if supported
            let bgGradient = ds.backgroundColor;
            try {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                // Extract color components from ds.borderColor (e.g. rgba(239, 68, 68, 1))
                const rgb = ds.borderColor.match(/\d+/g);
                if (rgb && rgb.length >= 3) {
                    gradient.addColorStop(0, `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, 0.15)`);
                    gradient.addColorStop(1, `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, 0.0)`);
                    bgGradient = gradient;
                }
            } catch(e) {
                console.error("Gradient creation error", e);
            }

            return {
                ...ds,
                backgroundColor: bgGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 5,
                pointBackgroundColor: ds.borderColor,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 1.5,
            };
        });

        telemetryChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: styledDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 8,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 10,
                                weight: '600',
                                family: "'Inter', sans-serif"
                            },
                            color: '#6B7280'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: {
                            size: 11,
                            weight: '700',
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Inter', sans-serif"
                        },
                        displayColors: true,
                        boxWidth: 6,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    // Strip unit from label if we format it
                                    label = label.split(' (')[0];
                                }
                                if (context.parsed.y !== null) {
                                    const unit = context.dataset.unit || '';
                                    label += ': ' + context.parsed.y + ' ' + unit;
                                }
                                return ' ' + label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                size: 10,
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F3F4F6',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                size: 10,
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                }
            }
        });
    }

    function refreshChart() {
        const siteId = "{{ $site->id }}";
        fetch(`/chart-data?site_id=${siteId}`)
            .then(res => res.json())
            .then(data => {
                if (data.labels && data.datasets) {
                    renderTelemetryChart(data.labels, data.datasets);
                }
            })
            .catch(err => console.error("Telemetry chart fetch failed:", err));
    }

    document.addEventListener("DOMContentLoaded", function () {
        refreshChart();
        // Auto refresh every 30 seconds
        setInterval(refreshChart, 30000);
    });
</script>
@endif