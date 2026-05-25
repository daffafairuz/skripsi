@extends('layouts.app')

@section('content')

<div x-data="{

openCreate:false,
openEdit:false,
openDelete:false,

selectedDevice:{
id:'',
name:'',
mac:'',
description:'',
sensors:[],
actuators:[]
}

}">

<!-- HEADER -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

    <div>

        <h1 class="text-2xl font-bold">
            Devices
        </h1>

        <p class="text-sm text-gray-500">

            Kelola ESP, Sensor dan Aktuator

        </p>

    </div>

    <div class="flex items-center gap-3">
        <!-- BUTTON -->
        <button
            @click="openCreate=true"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center gap-2">

            <svg class="w-5 h-5"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">

                <path stroke-width="2"
                d="M12 4v16m8-8H4"/>

            </svg>

            Tambah Device

        </button>

        @include('layouts.user-card', ['subtitle' => 'Device Management'])
    </div>

</div>


<!-- TABLE -->

<div class="bg-white rounded-2xl shadow overflow-x-auto">

<table class="w-full">

<thead class="bg-gray-100">

<tr>

<th class="p-4 text-left">
Nama
</th>

<th class="p-4 text-left">
MAC
</th>

<th class="p-4 text-left">
Sensor
</th>

<th class="p-4 text-left">
Actuator
</th>

<th class="p-4 text-left">
Site
</th>

<th class="p-4 text-left">
Status
</th>

<th class="p-4 text-left">
Action
</th>

</tr>

</thead>

<tbody>

@foreach($devices as $device)

@php
    $activeSite = $device->sites->first();
@endphp

<tr class="border-t hover:bg-gray-50">

<td class="p-4">

<div>

<p class="font-semibold">

{{ $device->name }}

</p>

<p class="text-xs text-gray-500">

{{ $device->description }}

</p>

</div>

</td>

<td class="p-4 min-w-[260px]">

@if($activeSite)

<div class="space-y-2">

<div>

<p class="font-semibold text-sm">
{{ $activeSite->name }}
</p>

<p class="text-xs text-gray-500">
{{ $activeSite->user->name ?? 'Owner tidak diketahui' }}
</p>

</div>

<form
action="{{ route('sites.devices.destroy', [$activeSite->id, $device->id]) }}"
method="POST"
onsubmit="return confirm('Copot device ini dari site?')">

@csrf
@method('DELETE')

<button
class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-semibold">
Copot dari Site
</button>

</form>

</div>

@else

<form
action="{{ route('site-devices.store') }}"
method="POST"
class="flex flex-col gap-2">

@csrf

<input type="hidden" name="device_id" value="{{ $device->id }}">

<select
name="site_id"
class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-white">

<option value="">
Pilih Site
</option>

@foreach($sites as $site)

<option value="{{ $site->id }}">
{{ $site->name }} - {{ $site->user->name ?? 'Owner' }}
</option>

@endforeach

</select>

<button
class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">
Hubungkan
</button>

</form>

@endif

</td>


<td class="p-4">

{{ $device->mac_address }}

</td>


<td class="p-4">

<div class="flex flex-wrap gap-2">

<div class="flex flex-wrap gap-2">

@foreach($device->sensors as $sensor)

<span
class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs">

{{ $sensor->name }}

</span>

@endforeach


<a
href="{{ route(
'sensors.device',
$device->id
) }}"

class="
bg-green-500
hover:bg-green-600
text-white
px-3
py-1
rounded-full
text-xs
transition">

Monitoring

</a>

</div>

</div>

</td>


<td class="p-4">

<div class="flex flex-wrap gap-2">

@foreach($device->actuators as $actuator)

<span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs">

{{ $actuator->name }}

</span>

@endforeach

</div>

</td>


<td class="p-4">

@if($device->status=="available")

<span class="bg-green-100 text-green-600 px-3 py-1 rounded-full">

Available

</span>

@elseif($device->status=="assigned")

<span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full">

Assigned

</span>

@else

<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full">

Inactive

</span>

@endif

</td>



<td class="p-4">

<div class="flex gap-2">

<!-- EDIT -->

<button

@click="
openEdit=true;


selectedDevice={
id:'{{ $device->id }}',
name:'{{ $device->name }}',
mac:'{{ $device->mac_address }}',
description:`{{ $device->description }}`,
sensors:@js($device->sensors),
actuators:@js($device->actuators)
};

"

class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded">

Edit

</button>


<!-- DELETE -->

<button

@click="
openDelete=true;
selectedDevice.id='{{ $device->id }}';
selectedDevice.name='{{ $device->name }}';
"

class="bg-red-100 text-red-700 px-3 py-1 rounded">

Delete

</button>

</div>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>



{{-- CREATE MODAL --}}
@include('admin.devices.create')

{{-- EDIT MODAL --}}
@include('admin.devices.edit')

{{-- DELETE MODAL --}}
@include('admin.devices.delete')

</div>

@endsection
