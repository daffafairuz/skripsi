@extends('layouts.app')

@section('content')

<!-- HEADER -->
<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold">Devices</h1>

        <p class="text-sm text-gray-500">
            Monitoring dan informasi device sistem aquaponik
        </p>
    </div>

    {{-- USER ONLY --}}
    @if(auth()->user()->role == 'user')

    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">

        <svg class="w-5 h-5"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">

            <path stroke-width="2"
                  d="M12 4v16m8-8H4"/>

        </svg>

        Tambah Device

    </button>

    @endif

</div>

{{-- ================= ADMIN ================= --}}
@if(auth()->user()->role == 'admin')

<div class="bg-white rounded-2xl shadow p-6">

    <!-- TITLE -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h2 class="font-bold text-lg">
                Seluruh Device Sistem
            </h2>

            <p class="text-sm text-gray-500">
                Admin hanya dapat memonitor device
            </p>
        </div>

        <span class="text-sm text-gray-400">
            {{ count($devices) }} Device
        </span>

    </div>

    <!-- LIST -->
    <div class="space-y-4 max-h-[650px] overflow-y-auto pr-2">

        @foreach($devices as $device)

        <div class="border rounded-2xl p-5 hover:bg-gray-50 transition">

            <div class="flex items-center justify-between">

                <!-- LEFT -->
                <div class="flex items-center gap-4">

                    <!-- ICON -->
                    <div class="
                        @if($device['category'] == 'Sensor')
                            bg-blue-100
                        @elseif($device['category'] == 'Actuator')
                            bg-yellow-100
                        @else
                            bg-green-100
                        @endif
                        p-3 rounded-xl
                    ">

                        {{-- SENSOR --}}
                        @if($device['category'] == 'Sensor')

                        <svg class="w-6 h-6 text-blue-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-width="2"
                                  d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z"/>

                        </svg>

                        {{-- ACTUATOR --}}
                        @elseif($device['category'] == 'Actuator')

                        <svg class="w-6 h-6 text-yellow-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-width="2"
                                  d="M5 13l4 4L19 7"/>

                        </svg>

                        {{-- ESP --}}
                        @else

                        <svg class="w-6 h-6 text-green-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-width="2"
                                  d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15"/>

                        </svg>

                        @endif

                    </div>

                    <!-- INFO -->
                    <div>

                        <h3 class="font-semibold text-lg">
                            {{ $device['name'] }}
                        </h3>

                        <div class="text-sm text-gray-500 space-y-1">

                            <p>
                                Tipe :
                                {{ $device['type'] }}
                            </p>

                            <p>
                                Kategori :
                                {{ $device['category'] }}
                            </p>

                            <p>
                                Site :
                                {{ $device['site'] }}
                            </p>

                            <p>
                                Owner :
                                {{ $device['owner'] }}
                            </p>

                            <p>
                                Update :
                                {{ $device['last_update'] }}
                            </p>

                        </div>

                    </div>

                </div>

                <!-- STATUS -->
                <div class="text-right">

                    @if($device['status'] == 'online')

                    <span class="bg-green-100 text-green-600 px-4 py-1 rounded-full text-sm">
                        Online
                    </span>

                    @else

                    <span class="bg-gray-200 text-gray-600 px-4 py-1 rounded-full text-sm">
                        Offline
                    </span>

                    @endif

                    <div class="mt-4">

                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl text-sm transition">
                            Detail
                        </button>

                    </div>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

{{-- ================= USER ================= --}}
@else

<div class="grid grid-cols-2 gap-6">

    @foreach($devices as $device)

    <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">

        <!-- TOP -->
        <div class="flex items-start justify-between mb-5">

            <!-- ICON -->
            <div class="
                @if($device['category'] == 'Sensor')
                    bg-blue-100
                @elseif($device['category'] == 'Actuator')
                    bg-yellow-100
                @else
                    bg-green-100
                @endif
                p-3 rounded-xl
            ">

                {{-- SENSOR --}}
                @if($device['category'] == 'Sensor')

                <svg class="w-6 h-6 text-blue-600"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-width="2"
                          d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z"/>

                </svg>

                {{-- ACTUATOR --}}
                @elseif($device['category'] == 'Actuator')

                <svg class="w-6 h-6 text-yellow-600"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-width="2"
                          d="M5 13l4 4L19 7"/>

                </svg>

                {{-- ESP --}}
                @else

                <svg class="w-6 h-6 text-green-600"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-width="2"
                          d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15"/>

                </svg>

                @endif

            </div>

            {{-- STATUS --}}
            @if($device['status'] == 'online')

            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                Online
            </span>

            @else

            <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm">
                Offline
            </span>

            @endif

        </div>

        <!-- CONTENT -->
        <div class="mb-5">

            <h2 class="font-bold text-xl mb-2">
                {{ $device['name'] }}
            </h2>

            <div class="text-sm text-gray-500 space-y-1">

                <p>
                    Tipe :
                    {{ $device['type'] }}
                </p>

                <p>
                    Kategori :
                    {{ $device['category'] }}
                </p>

                <p>
                    Site :
                    {{ $device['site'] }}
                </p>

                <p>
                    Update :
                    {{ $device['last_update'] }}
                </p>

            </div>

        </div>

        <!-- ACTION -->
        <div class="flex gap-3">

            {{-- SENSOR --}}
            @if($device['category'] == 'Sensor')

            <button class="flex-1 bg-gray-100 py-2 rounded-xl">
                Monitoring
            </button>

            {{-- ACTUATOR --}}
            @elseif($device['category'] == 'Actuator')

            <button class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl transition">
                Kontrol
            </button>

            {{-- ESP --}}
            @else

            <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-xl transition">
                Detail
            </button>

            @endif

        </div>

    </div>

    @endforeach

</div>

@endif

@endsection