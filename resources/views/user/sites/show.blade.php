@extends('layouts.app')

@section('content')

<!-- HEADER -->
<div class="flex justify-between items-center mb-8">

    <div>
        <h1 class="text-3xl font-bold">
            {{ $site->name }}
        </h1>

        <p class="text-gray-500">
            {{ $site->location }}
        </p>
    </div>

    @include('layouts.user-card', ['subtitle' => 'Site Detail'])

</div>


<!-- SENSOR -->
<div class="mb-8">

    <h2 class="text-xl font-bold mb-5">
        Monitoring Sensor
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

        @foreach($site->devices as $device)

            @foreach($device->sensors as $sensor)

                @php
                    $latest = $sensor->dataSensors->first();
                @endphp

                <div class="bg-white rounded-2xl shadow p-6">

                    <div class="flex justify-between items-start">

                        <div>

                            <p class="text-sm text-gray-400">

                                {{ $device->name }}

                            </p>

                            <h3 class="font-semibold">

                                {{ $sensor->name }}

                            </h3>

                        </div>

                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">

                            <svg class="w-5 h-5 text-blue-500"
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


                    <div class="mt-6">

                        <span class="text-4xl font-bold">

                            {{ $latest->value ?? '-' }}

                        </span>

                        <span class="text-gray-500">

                            {{ $sensor->unit }}

                        </span>

                    </div>

                </div>

            @endforeach

        @endforeach

    </div>

</div>


<!-- ACTUATOR -->
<div class="bg-white rounded-2xl shadow p-6">

    <h2 class="text-xl font-bold mb-6">

        Kontrol Aktuator

    </h2>


    <div class="space-y-5">

        @foreach($site->devices as $device)

            @foreach($device->actuators as $actuator)

                @php

                    $latestLog=$actuator
                    ->logs
                    ->first();

                    $isOn=
                    $latestLog &&
                    $latestLog->action=="on";

                @endphp


                <div class="flex justify-between items-center border-b pb-4">

                    <div>

                        <h3 class="font-semibold">

                            {{ $actuator->name }}

                        </h3>

                        <p class="text-sm text-gray-500">

                            {{ $device->name }}

                        </p>

                    </div>


                    <!-- Switch -->

                    <label class="relative inline-flex items-center">

                        <input
                        type="checkbox"
                        disabled
                        class="sr-only peer"
                        {{ $isOn ? 'checked' : '' }}>

                        <div class="w-14 h-7 rounded-full
                        transition
                        {{ $isOn
                        ? 'bg-green-500'
                        : 'bg-gray-300' }}">

                        </div>

                        <div
                        class="absolute left-1 top-1 bg-white w-5 h-5 rounded-full transition
                        {{ $isOn
                        ? 'translate-x-7'
                        : '' }}">
                        </div>

                    </label>

                </div>

            @endforeach

        @endforeach

    </div>

</div>

@endsection