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

    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-gray-800 leading-none">{{ auth()->user()->name }}</p>
            <p class="text-[10px] text-green-500 font-medium">Online</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold shadow-lg shadow-green-100">
            {{ substr(auth()->user()->name, 0, 1) }}
        </div>
    </div>
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
