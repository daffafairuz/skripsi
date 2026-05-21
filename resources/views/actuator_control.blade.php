@extends('layouts.app')

@section('content')

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Kontrol Perangkat</h1>
        <p class="text-sm text-gray-500">
            Kendalikan aktuator pada site
            <span class="font-semibold text-green-600">{{ $siteName ?? '-' }}</span>
        </p>
    </div>

    @include('layouts.user-card', ['subtitle' => 'Online'])
</div>

<!-- Success/Error Message -->
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

<!-- Filter Section -->
@if(isset($sites) && !$sites->isEmpty())
    <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm mb-8 transition duration-300 hover:shadow-md">
        <form action="{{ route('actuator-control') }}" method="GET" class="flex flex-col md:flex-row items-end gap-5">
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
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-455">
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
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-455">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            @if($selectedSiteId || $selectedDeviceId)
                <div class="w-full md:w-auto">
                    <a href="{{ route('actuator-control') }}"
                       class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl text-sm font-bold transition-all shadow-sm hover:shadow active:scale-95">
                        Reset
                    </a>
                </div>
            @endif
        </form>
    </div>
@endif

<!-- Actuator Cards -->
@if($actuators->isEmpty())

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-500 mb-1">Belum Ada Aktuator</h3>
        <p class="text-sm text-gray-400">Anda belum memiliki site atau belum ada perangkat aktuator terpasang.</p>
    </div>

@else

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($actuators as $actuator)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">

            <!-- Top -->
            <div class="flex items-start justify-between mb-5">

                <!-- Icon -->
                <div class="p-3 rounded-xl
                    {{ $actuator->current_state === 'on' ? 'bg-green-100' : 'bg-gray-100' }}">
                    @if(str_contains(strtolower($actuator->type), 'pump') || str_contains(strtolower($actuator->name), 'pompa'))
                        <svg class="w-6 h-6 {{ $actuator->current_state === 'on' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M9.172 15.828a4 4 0 010-5.656m5.656 0a4 4 0 010 5.656M12 12h.01"/>
                        </svg>
                    @elseif(str_contains(strtolower($actuator->type), 'light') || str_contains(strtolower($actuator->name), 'light'))
                        <svg class="w-6 h-6 {{ $actuator->current_state === 'on' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    @elseif(str_contains(strtolower($actuator->type), 'feeder') || str_contains(strtolower($actuator->name), 'feeder'))
                        <svg class="w-6 h-6 {{ $actuator->current_state === 'on' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @else
                        <svg class="w-6 h-6 {{ $actuator->current_state === 'on' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    @endif
                </div>

                <!-- Status Badge -->
                @if($actuator->current_state === 'on')
                    <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-bold tracking-wide">ON</span>
                @else
                    <span class="bg-gray-200 text-gray-600 text-xs px-3 py-1 rounded-full font-bold tracking-wide">OFF</span>
                @endif
            </div>

            <!-- Info -->
            <div class="mb-5">
                <h3 class="font-bold text-lg text-gray-800 mb-1">{{ $actuator->name }}</h3>
                <div class="text-sm text-gray-500 space-y-1">
                    <p>Tipe : {{ $actuator->type }}</p>
                    <p>Device : {{ $actuator->device->name ?? '-' }}</p>
                    <p>Terakhir :
                        @if($actuator->last_triggered)
                            {{ $actuator->last_triggered->diffForHumans() }}
                            <span class="text-xs text-gray-400">({{ $actuator->last_triggered_by }})</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            <!-- Toggle Button -->
            <form action="{{ route('actuator-control.toggle', $actuator->id) }}" method="POST">
                @csrf

                @if($actuator->current_state === 'on')
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl transition font-semibold flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Matikan
                    </button>
                @else
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl transition font-semibold flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Nyalakan
                    </button>
                @endif
            </form>

        </div>
        @endforeach

    </div>

@endif

@endsection
