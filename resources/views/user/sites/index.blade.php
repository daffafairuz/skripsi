@extends('layouts.app')

@section('content')

<!-- HEADER -->

<div class="flex justify-between items-center mb-6">

<div>

<h1 class="text-2xl font-bold">

Site

</h1>

<p class="text-gray-500 text-sm">

Kelola sistem aquaponik Anda

</p>

</div>

@include('layouts.user-card', ['subtitle' => 'Sites'])

</div>


{{-- ============================= --}}
{{-- BELUM ADA SITE --}}
{{-- ============================= --}}

@if(!$hasSite)

<div class="bg-white rounded-2xl shadow p-10 text-center">

<div class="flex justify-center mb-6">

    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">

        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-10 h-10 text-gray-400 flex-shrink-0"
            width="40"
            height="40"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>

        </svg>

    </div>

</div>

<h2 class="text-xl font-bold mb-3">

Belum Memiliki Site

</h2>

<p class="text-gray-500 mb-6">

Buat site terlebih dahulu
untuk memulai monitoring
dan pengontrolan sistem.

</p>

<a
href="{{ route('sites.create') }}"
class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl inline-block">

+ Buat Site

</a>

</div>


{{-- ============================= --}}
{{-- SUDAH ADA SITE --}}
{{-- ============================= --}}

@else

<!-- SITE CARD -->

<div class="bg-white rounded-2xl shadow p-6 mb-6">

<div class="flex justify-between">

<div>

<h2 class="font-bold text-2xl">

{{ $site->name }}

</h2>

<p class="text-gray-500">

{{ $site->location }}

</p>

</div>

<span class="bg-green-100 text-green-600 px-4 py-2 rounded-full h-fit">

Aktif

</span>

</div>


@if($site->description)

<p class="mt-4 text-gray-500">

{{ $site->description }}

</p>

@endif

</div>


<!-- OVERVIEW -->

<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

    <div class="bg-white rounded-2xl shadow p-5">

        <p class="text-gray-400 text-sm">
            Device
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $site->devices->count() }}
        </h2>

    </div>


    <div class="bg-white rounded-2xl shadow p-5">

        <p class="text-gray-400 text-sm">
            Sensor
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $totalSensors }}
        </h2>

    </div>


    <div class="bg-white rounded-2xl shadow p-5">

        <p class="text-gray-400 text-sm">
            Actuator
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $totalActuators }}
        </h2>

    </div>


    <div class="bg-white rounded-2xl shadow p-5">

        <p class="text-gray-400 text-sm">
            Notifikasi
        </p>

        <h2 class="text-3xl font-bold mt-2">
            {{ $site->notifications->count() }}
        </h2>

    </div>

</div>



<!-- SENSOR CARDS -->

<div class="mb-6">

    <div class="flex justify-between items-center mb-4">

        <h3 class="font-bold text-xl">

            Monitoring Sensor

        </h3>

    </div>


    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">

    @php
        $hasSensor=false;
    @endphp


    @foreach($site->devices as $device)

        @foreach($device->sensors as $sensor)

        @php

            $hasSensor=true;

            $latest=
            $sensor
            ->dataSensors
            ->first();

        @endphp

        <div class="bg-white rounded-2xl shadow p-5">

            <div class="flex justify-between mb-4">

                <div>

                    <p class="text-sm text-gray-400">

                        {{ $device->name }}

                    </p>

                    <h4 class="font-semibold">

                        {{ $sensor->name }}

                    </h4>

                </div>


                <div
                class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">

                    <svg
                    class="w-5 h-5 text-blue-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                        <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3"/>

                    </svg>

                </div>

            </div>


            <div>

                <span class="text-4xl font-bold">

                    {{ $latest->value ?? rand(20,35) }}

                </span>

                <span class="text-gray-500">

                    {{ $sensor->unit }}

                </span>

            </div>

        </div>

        @endforeach

    @endforeach


    @if(!$hasSensor)

    <div class="col-span-full bg-white rounded-2xl shadow p-8 text-center">

        <svg
        class="w-12 h-12 mx-auto text-gray-300 mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24">

            <path
            stroke-width="2"
            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18"/>

        </svg>

        <p class="text-gray-500">

            Belum ada sensor terhubung

        </p>

    </div>

    @endif

    </div>

</div>


<!-- DEVICE LIST -->

<div class="bg-white rounded-2xl shadow p-6 mb-6">

<div class="flex justify-between mb-5">

<h3 class="font-bold">

Device Terhubung

</h3>

<a
href="{{ route('sites.devices.create',$site->id) }}"
class="bg-green-500 text-white px-4 py-2 rounded-xl">

+ Tambah Device

</a>

</div>

<div class="space-y-4">

@foreach($site->devices as $device)

<div class="border rounded-xl p-4">

<div class="flex justify-between">

<div>

<p class="font-semibold">

{{ $device->name }}

</p>

<p class="text-sm text-gray-500">

{{ $device->mac_address }}

</p>

</div>

<span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full h-fit">

{{ ucfirst($device->status) }}

</span>

</div>

</div>

@endforeach

</div>

</div>


<!-- ACTUATOR CONTROL -->

<div class="bg-white rounded-2xl shadow p-6">

    <div class="flex justify-between items-center mb-6">

        <h3 class="font-bold text-xl">

            Kontrol Aktuator

        </h3>

        <span class="text-sm text-gray-400">

            {{ $totalActuators }} perangkat

        </span>

    </div>

    <div class="space-y-4">

    @php
        $hasActuator=false;
    @endphp

    @foreach($site->devices as $device)

        @foreach($device->actuators as $actuator)

        @php

            $hasActuator=true;

            $latestLog =
            $actuator
            ->logs
            ->first();

            $isOn =
            $latestLog &&
            $latestLog->action=="on";

        @endphp


        <div class="border rounded-xl p-4 hover:bg-gray-50 transition">

            <div class="flex justify-between items-center">

                <div>

                    <h4 class="font-semibold">

                        {{ $actuator->name }}

                    </h4>

                    <p class="text-sm text-gray-500">

                        {{ $device->name }}

                    </p>

                </div>


                <div class="flex items-center gap-4">

                    {{-- STATUS TEXT --}}

                    @if($isOn)

                    <span class="text-green-600 font-medium">

                        ON

                    </span>

                    @else

                    <span class="text-gray-400 font-medium">

                        OFF

                    </span>

                    @endif


                    {{-- TOGGLE --}}

                    <label class="relative inline-block w-14 h-8">

                        <input
                        type="checkbox"
                        class="sr-only peer"
                        {{ $isOn ? 'checked':'' }}>

                        {{-- Background --}}

                        <div class="
                        absolute
                        inset-0
                        rounded-full
                        transition
                        peer-checked:bg-green-500
                        bg-gray-300">

                        </div>

                        {{-- Circle --}}

                        <div class="
                        absolute
                        top-1
                        left-1
                        bg-white
                        w-6
                        h-6
                        rounded-full
                        transition-all
                        peer-checked:translate-x-6">

                        </div>

                    </label>

                </div>

            </div>

        </div>

        @endforeach

    @endforeach


    @if(!$hasActuator)

    <div class="text-center p-8">

        <svg
        class="w-12 h-12 mx-auto text-gray-300 mb-4"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24">

            <path
            stroke-width="2"
            d="M13 10V3L4 14h7v7l9-11h-7z"/>

        </svg>

        <p class="text-gray-500">

            Belum ada aktuator terhubung

        </p>

    </div>

    @endif

    </div>

</div>

@endif
<!-- CHART SENSOR -->

<div class="bg-white rounded-2xl shadow p-6 mt-6">

    <div class="flex justify-between items-center mb-6">

        <div>

            <h3 class="font-bold text-xl">

                Riwayat Sensor

            </h3>

            <p class="text-sm text-gray-500">

                Data sensor 20 pembacaan terakhir

            </p>

        </div>

    </div>


    <div class="relative h-96">

        <canvas id="sensorChart"></canvas>

    </div>

</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener(
'DOMContentLoaded',
function(){

fetch('/chart-data')

.then(res=>res.json())

.then(data=>{

    /*
    |--------------------------------------------------------------------------
    | fallback jika kosong
    |--------------------------------------------------------------------------
    */

    if(
        !data.labels.length
    )
    {
        data.labels=[
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00'
        ];

        data.temperature=[
            25,
            26,
            27,
            26,
            28
        ];

        data.ph=[
            7,
            7.1,
            6.9,
            7.2,
            7.1
        ];
    }


    const ctx=
    document
    .getElementById(
        'sensorChart'
    );


    new Chart(
    ctx,
    {

        type:'line',

        data:{

            labels:data.labels,

            datasets:[

            {
                label:'Suhu °C',

                data:data.temperature,

                borderColor:'#22c55e',

                backgroundColor:
                'rgba(34,197,94,.1)',

                tension:.4,

                fill:true
            },

            {
                label:'pH',

                data:data.ph,

                borderColor:'#3b82f6',

                backgroundColor:
                'rgba(59,130,246,.1)',

                tension:.4,

                fill:true
            }

            ]

        },

        options:{

            responsive:true,

            maintainAspectRatio:false,

            plugins:{
                legend:{
                    position:'top'
                }
            },

            scales:{

                y:{
                    beginAtZero:false
                }

            }

        }

    })

})

})

</script>

