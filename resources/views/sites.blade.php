@extends('layouts.app')

@section('content')

<!-- HEADER -->
<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold">Sites</h1>
        <p class="text-gray-500 text-sm">
            Daftar site aquaponik yang terdaftar
        </p>
    </div>

    {{-- USER ONLY --}}
    @if(auth()->user()->role == 'user')

    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">

        <svg class="w-5 h-5"
             fill="none"
             stroke="currentColor"
             viewBox="0 0 24 24">

            <path stroke-width="2"
                  d="M12 4v16m8-8H4"/>

        </svg>

        Tambah Site

    </button>

    @endif

</div>

{{-- ================= ADMIN ================= --}}
@if(auth()->user()->role == 'admin')

<div class="bg-white rounded-xl shadow p-6">

    <!-- TITLE -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h2 class="font-bold text-lg">
                Semua Site Terdaftar
            </h2>

            <p class="text-sm text-gray-500">
                Admin hanya dapat melihat monitoring site
            </p>
        </div>

        <span class="text-sm text-gray-400">
            Total {{ count($sites) }} Site
        </span>

    </div>

    <!-- LIST -->
    <div class="space-y-4">

        @foreach($sites as $site)

        <div class="border rounded-xl p-5 hover:bg-gray-50 transition">

            <div class="flex items-center justify-between">

                <!-- LEFT -->
                <div class="flex items-center gap-4">

                    <!-- ICON -->
                    <div class="bg-green-100 p-3 rounded-xl">

                        <svg class="w-6 h-6 text-green-600"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-width="2"
                                  d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>

                        </svg>

                    </div>

                    <!-- INFO -->
                    <div>

                        <h3 class="font-semibold text-lg">
                            {{ $site['name'] }}
                        </h3>

                        <div class="text-sm text-gray-500 space-y-1">

                            <p>
                                Owner :
                                {{ $site['owner'] }}
                            </p>

                            <p>
                                Lokasi :
                                {{ $site['location'] }}
                            </p>

                            <p>
                                Total Device :
                                {{ $site['devices'] }}
                            </p>

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="text-right">

                    {{-- STATUS --}}
                    @if($site['status'] == 'active')

                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                        Aktif
                    </span>

                    @else

                    <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm">
                        Nonaktif
                    </span>

                    @endif

                    <div class="mt-4">

                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Lihat Monitoring
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

    @foreach($sites as $site)

    <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">

        <!-- HEADER -->
        <div class="flex items-start justify-between mb-4">

            <div class="bg-green-100 p-3 rounded-xl">

                <svg class="w-6 h-6 text-green-600"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-width="2"
                          d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>

                </svg>

            </div>

            {{-- STATUS --}}
            @if($site['status'] == 'active')

            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                Aktif
            </span>

            @else

            <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm">
                Nonaktif
            </span>

            @endif

        </div>

        <!-- CONTENT -->
        <div class="mb-5">

            <h2 class="font-bold text-xl mb-2">
                {{ $site['name'] }}
            </h2>

            <div class="text-sm text-gray-500 space-y-1">

                <p>
                    Lokasi :
                    {{ $site['location'] }}
                </p>

                <p>
                    Total Device :
                    {{ $site['devices'] }}
                </p>

            </div>

        </div>

        <!-- ACTION -->
        <div class="flex items-center gap-3">

            <button class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition">
                Monitoring
            </button>

            <button class="flex-1 bg-gray-100 hover:bg-gray-200 py-2 rounded-lg transition">
                Kelola
            </button>

        </div>

    </div>

    @endforeach

</div>

@endif

@endsection