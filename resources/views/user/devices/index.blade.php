@extends('layouts.app')

@section('content')

<!-- HEADER -->

<div class="flex justify-between items-center mb-6">

    <div>

        <h1 class="text-2xl font-bold">

            Devices

        </h1>

        <p class="text-gray-500 text-sm">

            Daftar device yang terhubung ke site

        </p>

    </div>

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

    <p class="text-gray-500">

        Belum ada device
        yang terhubung ke site Anda.

    </p>

</div>

@else

<!-- DEVICE LIST -->

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

@foreach($devices as $device)

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

</div>

@endforeach

</div>

@endif

@endif

@endsection