@extends('layouts.app')

@section('content')

<div x-data="{
    openCreate: false,
    openDelete: false,
    deleteSite: {
        id: '',
        name: ''
    }
}">

<!-- HEADER -->

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

    <div>

        <h1 class="text-2xl font-bold">

            Site Monitoring

        </h1>

        <p class="text-gray-500 text-sm">

            Monitoring seluruh site pada sistem

        </p>

    </div>


    <div class="flex items-center gap-3">
        <!-- CREATE -->

        <button
            @click="openCreate=true"
            class="bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-xl">

            + Tambah Site

        </button>

        @include('layouts.user-card', ['subtitle' => 'Site Monitoring'])
    </div>

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

{{ $site->location }} @if($site->mac_address) | MAC Master: <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded text-gray-600">{{ $site->mac_address }}</span> @endif

</p>

</div>

<span class="bg-green-100 text-green-600 px-3 py-1 rounded-full h-fit">

Aktif

</span>

</div>



<div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">

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

{{ $site->activeDevices->count() }}

</p>

</div>


<div>

<p class="text-gray-400 text-sm">

Sensor

</p>

<p class="font-semibold">

{{ $site->activeDevices->sum(
fn($d)=>$d->sensors->count()
) }}

</p>

</div>


<div>

<p class="text-gray-400 text-sm">

Actuator

</p>

<p class="font-semibold">

{{ $site->activeDevices->sum(
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

@foreach($site->activeDevices as $device)

<span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm">

{{ $device->name }}

</span>

@endforeach

</div>

</div>


<div class="mt-6 flex justify-end gap-3">

<button
    type="button"
    @click="
        openDelete = true;
        deleteSite.id = '{{ $site->id }}';
        deleteSite.name = '{{ addslashes($site->name) }}';
    "
    class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-xl transition">
    Hapus Site
</button>

<a
href="{{ route('sites.edit',$site->id) }}"
class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2 rounded-xl transition">

Edit Site

</a>

<a
href="{{ route('sites.show',$site->id) }}"
class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">

Lihat Monitoring

</a>

</div>

</div>

@endforeach

</div>



@include('admin.sites.create')

<!-- DELETE SITE MODAL -->
<div
    x-show="openDelete"
    x-transition
    class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
    style="display:none">

    <div
        @click.away="openDelete=false"
        class="bg-white rounded-2xl shadow-xl w-full max-w-md">

        <!-- HEADER -->
        <div class="p-6 text-center">

            <!-- Warning Icon -->
            <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                <svg
                    class="w-8 h-8 text-red-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>

            <h2 class="text-xl font-bold mb-2">
                Hapus Site?
            </h2>

            <p class="text-gray-500 text-sm">
                Site
                <span
                    class="font-semibold"
                    x-text="deleteSite.name">
                </span>
                akan dihapus secara permanen.
            </p>

        </div>

        <!-- WARNING BOX -->
        <div class="mx-6 mb-6 p-4 bg-red-50 rounded-xl text-left">
            <div class="flex gap-3">
                <svg
                    class="w-5 h-5 text-red-600 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/>
                </svg>
                <div>
                    <p class="font-medium text-red-700">
                        Tindakan ini akan:
                    </p>
                    <ul class="text-sm text-red-600 mt-2 space-y-1">
                        <li>
                            • Menghapus Site ini secara permanen
                        </li>
                        <li>
                            • Mencopot semua device yang terhubung ke Site ini
                        </li>
                        <li>
                            • Menghapus seluruh jadwal grow light, jadwal pakan, & notifikasi terkait Site ini
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <form
            :action="'/sites/' + deleteSite.id"
            method="POST"
            class="p-6 border-t">
            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    @click="openDelete=false"
                    class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">
                    Batal
                </button>
                <button
                    class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-semibold">
                    Hapus
                </button>
            </div>
        </form>

    </div>

</div>

</div>

@endsection