@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

<div>

<h1 class="text-2xl font-bold">

Tambah Device

</h1>

<p class="text-gray-500">

Pilih device yang tersedia

</p>

</div>

@include('layouts.user-card', ['subtitle' => 'Add Device'])

</div>


<form
action="{{ route(
'sites.devices.store',
$site->id
) }}"
method="POST">

@csrf


<div class="space-y-4">

@forelse($devices as $device)

<label
class="bg-white rounded-xl shadow p-5 flex justify-between items-center cursor-pointer">

<div>

<h2 class="font-bold">

{{ $device->name }}

</h2>

<p class="text-sm text-gray-500">

{{ $device->mac_address }}

</p>


<div class="flex gap-2 mt-3">

@foreach($device->sensors as $sensor)

<span
class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs">

{{ $sensor->name }}

</span>

@empty

<div class="bg-white rounded-xl shadow p-8 text-center">

<p class="font-semibold text-gray-700">
Tidak ada device tersedia
</p>

<p class="text-sm text-gray-500 mt-2">
Semua device sudah terhubung atau belum ada device yang siap dipakai.
</p>

</div>

@endforelse

</div>


<div class="flex gap-2 mt-2">

@foreach($device->actuators as $actuator)

<span
class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs">

{{ $actuator->name }}

</span>

@endforeach

</div>

</div>


<input
type="radio"
name="device_id"
value="{{ $device->id }}"
class="w-5 h-5">

</label>

@endforeach

</div>


<div class="flex justify-end mt-6">

<button
@if($devices->isEmpty()) disabled @endif
class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl">

Hubungkan Device

</button>

</div>

</form>

</div>

@endsection
