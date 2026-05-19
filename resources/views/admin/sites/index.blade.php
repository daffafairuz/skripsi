@extends('layouts.app')

@section('content')

<div x-data="{openCreate:false}">

<!-- HEADER -->

<div class="flex justify-between items-center mb-6">

    <div>

        <h1 class="text-2xl font-bold">

            Site Monitoring

        </h1>

        <p class="text-gray-500 text-sm">

            Monitoring seluruh site pada sistem

        </p>

    </div>


    <!-- CREATE -->

    <button
        @click="openCreate=true"
        class="bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-xl">

        + Tambah Site

    </button>

</div>



<!-- LIST -->

<div class="space-y-5">

@foreach($sites as $site)

<div class="bg-white rounded-2xl shadow p-6">

<div class="flex justify-between">

<div>

<h2 class="text-xl font-bold">

{{ $site->name }}

</h2>

<p class="text-sm text-gray-500">

{{ $site->location }}

</p>

</div>

<span class="bg-green-100 text-green-600 px-3 py-1 rounded-full h-fit">

Aktif

</span>

</div>



<div class="grid grid-cols-4 gap-6 mt-6">

<div>

<p class="text-gray-400 text-sm">

Owner

</p>

<p class="font-semibold">

{{ $site->user->name }}

</p>

</div>


<div>

<p class="text-gray-400 text-sm">

Device

</p>

<p class="font-semibold">

{{ $site->devices->count() }}

</p>

</div>


<div>

<p class="text-gray-400 text-sm">

Sensor

</p>

<p class="font-semibold">

{{ $site->devices->sum(
fn($d)=>$d->sensors->count()
) }}

</p>

</div>


<div>

<p class="text-gray-400 text-sm">

Actuator

</p>

<p class="font-semibold">

{{ $site->devices->sum(
fn($d)=>$d->actuators->count()
) }}

</p>

</div>

</div>


<div class="mt-6">

<p class="font-semibold mb-3">

Device Terhubung

</p>

<div class="flex flex-wrap gap-2">

@foreach($site->devices as $device)

<span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm">

{{ $device->name }}

</span>

@endforeach

</div>

</div>


<div class="mt-6 flex justify-end">

<a
href="{{ route('sites.show',$site->id) }}"
class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl">

Lihat Monitoring

</a>

</div>

</div>

@endforeach

</div>



@include('admin.sites.create')

</div>

@endsection