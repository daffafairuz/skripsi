@extends('layouts.app')

@section('content')

<div x-data="{ selectedDevice:null }">

<!-- HEADER -->

<div class="flex justify-between items-center mb-6">

    <div>

        <h1 class="text-2xl font-bold">
            Dashboard Admin
        </h1>

        <p class="text-gray-500">
            Monitoring seluruh sistem
        </p>

    </div>

</div>


<!-- STATS -->

<div class="grid grid-cols-5 gap-4 mb-6">

    <div class="bg-white p-5 rounded-xl shadow">

        <p class="text-sm text-gray-500">
            Total Site
        </p>

        <h2 class="text-3xl font-bold">
            {{ $adminStats['total_sites'] }}
        </h2>

    </div>

    <div class="bg-white p-5 rounded-xl shadow">

        <p class="text-sm text-gray-500">
            Total User
        </p>

        <h2 class="text-3xl font-bold">
            {{ $adminStats['total_users'] }}
        </h2>

    </div>

    <div class="bg-white p-5 rounded-xl shadow">

        <p class="text-sm text-gray-500">
            Total Device
        </p>

        <h2 class="text-3xl font-bold">
            {{ $adminStats['total_devices'] }}
        </h2>

    </div>

    <div class="bg-white p-5 rounded-xl shadow">

        <p class="text-sm text-gray-500">
            Data Sensor
        </p>

        <h2 class="text-3xl font-bold">
            {{ $adminStats['total_sensor_data'] }}
        </h2>

    </div>

    <div class="bg-white p-5 rounded-xl shadow">

        <p class="text-sm text-gray-500">
            Notifikasi
        </p>

        <h2 class="text-3xl font-bold">
            {{ $adminStats['total_notifications'] }}
        </h2>

    </div>

</div>



<div class="grid grid-cols-2 gap-6 mb-6">


<!-- SITE SYSTEM -->

<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between mb-5">

        <h3 class="font-bold">

            Site Dalam Sistem

        </h3>

        <span class="text-sm text-gray-400">

            {{ $sites->count() }}

        </span>

    </div>


    <div class="space-y-3">

        @foreach($sites as $site)

        <div class="border rounded-xl p-4">

            <div class="flex justify-between">

                <div>

                    <h4 class="font-semibold">

                        {{ $site->name }}

                    </h4>

                    <p class="text-sm text-gray-500">

                        {{ $site->user->name }}

                    </p>

                </div>

                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full">

                    Aktif

                </span>

            </div>

        </div>

        @endforeach

    </div>

</div>



<!-- ACTIVITY -->

<div class="bg-white rounded-xl shadow p-6">

<h3 class="font-bold mb-5">

Aktivitas Terbaru

</h3>

<div class="space-y-4">

@forelse($activities as $activity)

<div class="flex gap-3">

<div
class="w-2 h-2 mt-2 rounded-full bg-green-500">
</div>

<div>

<p class="font-medium">

{{ $activity->title }}

</p>

<p class="text-xs text-gray-500">

{{ $activity->created_at->diffForHumans() }}

</p>

</div>

</div>

@empty

<div class="text-center text-gray-500">

Belum ada aktivitas

</div>

@endforelse

</div>

</div>

</div>



<div class="grid grid-cols-2 gap-6">


<!-- CHART -->

<div class="bg-white rounded-xl shadow p-6">

<h3 class="font-bold mb-4">

Monitoring Sensor

</h3>

<canvas id="chart"></canvas>

</div>




<!-- DEVICE -->

<div class="bg-white rounded-xl shadow p-6">

<div class="flex justify-between mb-5">

<h3 class="font-bold">

Daftar Device

</h3>

<span class="text-sm">

{{ $devices->total() }}

device

</span>

</div>


<div class="space-y-3 max-h-[500px] overflow-y-auto">


@foreach($devices as $device)

<div
class="border rounded-xl overflow-hidden">

<!-- HEADER -->

<div
@click="selectedDevice==={{$device->id}}
? selectedDevice=null
: selectedDevice={{$device->id}}"

class="p-4 cursor-pointer hover:bg-gray-50 flex justify-between items-center">

<div>

<h4 class="font-semibold">

{{ $device->name }}

</h4>

<p class="text-sm text-gray-500">

{{ $device->mac_address }}

</p>

</div>

<div class="flex gap-2 items-center">

@if($device->status=="assigned")

<span
class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">

Aktif

</span>

@else

<span
class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">

{{ ucfirst($device->status) }}

</span>

@endif


<svg
class="w-5 h-5"

fill="none"

stroke="currentColor"

viewBox="0 0 24 24">

<path stroke-width="2"
d="M19 9l-7 7-7-7"/>

</svg>

</div>

</div>


<!-- DROPDOWN -->

<div
x-show="selectedDevice=={{$device->id}}"
x-transition
class="border-t bg-gray-50 p-4">


<!-- SENSOR -->

<div class="mb-4">

<p class="font-medium mb-2">

Sensor

</p>

<div class="flex flex-wrap gap-2">

@foreach($device->sensors as $sensor)

<span
class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm">

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

<div class="flex flex-wrap gap-2">

@foreach($device->actuators as $actuator)

<span
class="bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-sm">

{{ $actuator->name }}

</span>

@endforeach

</div>

</div>

</div>

</div>

@endforeach

</div>

<div class="mt-4">

{{ $devices->links() }}

</div>

</div>

</div>

</div>

@endsection



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

fetch('/chart-data')

.then(res=>res.json())

.then(data=>{

new Chart(

document.getElementById('chart'),

{

type:'line',

data:{

labels:data.labels,

datasets:[

{

label:'Suhu',

data:data.temperature,

borderWidth:2,

tension:.4

},

{

label:'pH',

data:data.ph,

borderWidth:2,

tension:.4

}

]

}

})

})

</script>