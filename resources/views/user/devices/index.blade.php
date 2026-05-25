@extends('layouts.app')

@section('content')

<!-- HEADER -->

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

    <div>

        <h1 class="text-2xl font-bold">

            Devices

        </h1>

        <p class="text-gray-500 text-sm">

            Daftar device yang terhubung ke site

        </p>

    </div>

    @include('layouts.user-card', ['subtitle' => 'Devices'])

</div>


{{-- ========================= --}}
{{-- BELUM ADA SITE --}}
{{-- ========================= --}}

@if(!$hasSite)

<div class="bg-white rounded-2xl shadow p-10 text-center">

    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-5">

        <svg
            class="w-12 h-12 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>

        </svg>

    </div>

    <h2 class="text-xl font-bold mb-3">

        Belum Memiliki Site

    </h2>

    <p class="text-gray-500 mb-6">

        Anda belum memiliki site.
        Tambahkan site terlebih dahulu
        untuk menghubungkan device.

    </p>

    <a
        href="/sites"
        class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl inline-flex items-center gap-2">

        <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-width="2"
                d="M12 4v16m8-8H4"/>

        </svg>

        Tambah Site

    </a>

</div>


{{-- ========================= --}}
{{-- ADA SITE --}}
{{-- ========================= --}}

@else

@php
    $selectedSiteForAction = null;
    if ($selectedSiteId) {
        $selectedSiteForAction = $sites->firstWhere('id', (int) $selectedSiteId);
    } elseif ($sites->count() === 1) {
        $selectedSiteForAction = $sites->first();
    }
@endphp

@if($hasSite && $sites->count() > 1)
<div class="flex items-center gap-3 mb-6 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
    <form action="{{ route('devices.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
        <label for="site-select" class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap hidden sm:inline text-gray-500">Pilih Site:</label>
        <div class="relative w-full md:w-64">
            <select id="site-select" name="site_id" onchange="this.form.submit()" 
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                <option value="">Semua Site</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ $selectedSiteId == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </form>
</div>
@endif

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h2 class="text-lg font-bold text-gray-800">
            Device Terhubung
        </h2>
        <p class="text-sm text-gray-500">
            Hubungkan atau copot device pada site Anda
        </p>
    </div>

    @if($selectedSiteForAction)
        <a
            href="{{ route('sites.devices.create', $selectedSiteForAction->id) }}"
            class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-xl font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Device
        </a>
    @else
        <div class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-gray-100 text-gray-500 text-sm font-semibold">
            Pilih site untuk tambah device
        </div>
    @endif
</div>

@if($devices->count()==0)

<div class="bg-white rounded-2xl shadow p-10 text-center">

    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-5">

        <svg
            class="w-12 h-12 text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-width="2"
                d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15"/>

        </svg>

    </div>

    <h2 class="text-xl font-bold mb-3">

        Belum Ada Device

    </h2>

    <p class="text-gray-500 mb-6">

        Belum ada device
        yang terhubung ke site Anda.

    </p>

    @if($selectedSiteForAction)
        <a
            href="{{ route('sites.devices.create', $selectedSiteForAction->id) }}"
            class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white px-5 py-2.5 rounded-xl font-semibold">
            Tambah Device
        </a>
    @endif

</div>

@else

<!-- DEVICE LIST -->

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

@foreach($devices as $device)

@php
    $activeSite = $device->sites->first();
@endphp

<div class="bg-white rounded-2xl shadow p-6">

    <!-- TOP -->

    <div class="flex justify-between mb-4">

        <div>

            <h2 class="font-bold">

                {{ $device->name }}

            </h2>

            <p class="text-xs text-gray-500">

                {{ $device->mac_address }}

            </p>

        </div>

        <span class="px-3 py-1 rounded-full text-sm

        @if($device->status=="available")
            bg-green-100 text-green-600
        @elseif($device->status=="assigned")
            bg-blue-100 text-blue-600
        @else
            bg-gray-100 text-gray-600
        @endif">

            {{ ucfirst($device->status) }}

        </span>

    </div>


    <!-- SENSOR -->

    <div class="mb-4">

        <p class="font-medium mb-2">

            Sensor

        </p>

        <div class="flex flex-wrap gap-2">

            @foreach($device->sensors as $sensor)

            <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs">

                {{ $sensor->name }}

            </span>

            @endforeach

        </div>

    </div>


    <!-- ACTUATOR -->

    <div>

        <p class="font-medium mb-2">

            Actuator

        </p>

        <div class="space-y-2">

            @foreach($device->actuators as $actuator)

            <div class="flex justify-between items-center">

                <span>

                    {{ $actuator->name }}

                </span>

                <button
                    class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm">

                    Kontrol

                </button>

            </div>

            @endforeach

        </div>

    </div>

    @if($activeSite)
        <form
            action="{{ route('sites.devices.destroy', [$activeSite->id, $device->id]) }}"
            method="POST"
            class="mt-5 pt-5 border-t border-gray-100"
            onsubmit="return confirm('Copot device ini dari site?')">

            @csrf
            @method('DELETE')

            <button
                class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-sm font-semibold">
                Copot Device
            </button>

        </form>
    @endif

</div>

@endforeach

</div>

@endif

@endif

@endsection
