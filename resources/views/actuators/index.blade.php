@extends('layouts.app')

@section('content')

<div x-data="{

    openCreate: false,
    openEdit: false,
    openDelete: false,

    editActuator: {
        id: '',
        name: '',
        device_id: '',
        type: '',
        default_state: ''
    },

    deleteActuator: {
        id: '',
        name: ''
    }

}" class="relative">

<!-- HEADER -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

    <div>
        <h1 class="text-2xl font-bold">Daftar Aktuator</h1>
        <p class="text-sm text-gray-500">
            Kelola aktuator yang terpasang pada device
        </p>
    </div>

    <div class="flex items-center gap-3">
        <button
            @click="openCreate = true"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Aktuator
        </button>

        @include('layouts.user-card', ['subtitle' => 'Actuator List'])
    </div>

</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- TABLE -->
<div class="bg-white rounded-2xl shadow overflow-x-auto">

    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 text-left">Nama</th>
                <th class="p-4 text-left">Device</th>
                <th class="p-4 text-left">Tipe</th>
                <th class="p-4 text-left">Default State</th>
                <th class="p-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($actuators as $actuator)
            <tr class="border-t hover:bg-gray-50">

                <td class="p-4 font-semibold">{{ $actuator->name }}</td>

                <td class="p-4 text-sm">
                    <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-xs">
                        {{ $actuator->device->name ?? '-' }}
                    </span>
                </td>

                <td class="p-4 text-sm">{{ $actuator->type }}</td>

                <td class="p-4">
                    @if($actuator->default_state == 'on')
                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">ON</span>
                    @else
                        <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm">OFF</span>
                    @endif
                </td>

                <td class="p-4">
                    <div class="flex gap-2">

                        <!-- EDIT -->
                        <button
                            @click="
                                openEdit = true;
                                editActuator.id = '{{ $actuator->id }}';
                                editActuator.name = '{{ addslashes($actuator->name) }}';
                                editActuator.device_id = '{{ $actuator->device_id }}';
                                editActuator.type = '{{ addslashes($actuator->type) }}';
                                editActuator.default_state = '{{ $actuator->default_state }}';
                            "
                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-sm">
                            Edit
                        </button>

                        <!-- DELETE -->
                        <button
                            type="button"
                            @click="
                                openDelete = true;
                                deleteActuator.id = '{{ $actuator->id }}';
                                deleteActuator.name = '{{ addslashes($actuator->name) }}';
                            "
                            class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-sm hover:bg-red-200 transition">
                            Hapus
                        </button>

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-400">
                    Belum ada aktuator terdaftar
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

<!-- ================= CREATE MODAL ================= -->
<div
    x-show="openCreate"
    x-transition
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Tambah Aktuator</h2>
            <button @click="openCreate = false">✕</button>
        </div>

        <form action="/actuators" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Aktuator</label>
                <input type="text" name="name" placeholder="Contoh: Pompa Air Kolam A"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Device</label>
                <select name="device_id" class="w-full border rounded-xl p-3" required>
                    <option value="">-- Pilih Device --</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->mac_address }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Tipe</label>
                <input type="text" name="type" placeholder="Contoh: pump, grow_light, feeder, aerator"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Default State</label>
                <select name="default_state" class="w-full border rounded-xl p-3">
                    <option value="off">OFF</option>
                    <option value="on">ON</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openCreate = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">Batal</button>
                <button class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl transition">Simpan</button>
            </div>
        </form>

    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div
    x-show="openEdit"
    x-transition
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Edit Aktuator</h2>
            <button @click="openEdit = false">✕</button>
        </div>

        <form :action="'/actuators/' + editActuator.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Aktuator</label>
                <input type="text" name="name" x-model="editActuator.name"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Device</label>
                <select name="device_id" x-model="editActuator.device_id"
                        class="w-full border rounded-xl p-3" required>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->mac_address }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Tipe</label>
                <input type="text" name="type" x-model="editActuator.type"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Default State</label>
                <select name="default_state" x-model="editActuator.default_state"
                        class="w-full border rounded-xl p-3">
                    <option value="off">OFF</option>
                    <option value="on">ON</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openEdit = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">Batal</button>
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">Update</button>
            </div>
        </form>

    </div>
</div>

<!-- ================= DELETE MODAL ================= -->
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
                Hapus Aktuator?
            </h2>

            <p class="text-gray-500 text-sm">
                Aktuator
                <span
                    class="font-semibold"
                    x-text="deleteActuator.name">
                </span>
                akan dihapus permanen.
            </p>

        </div>

        <!-- WARNING BOX -->
        <div class="mx-6 mb-6 p-4 bg-red-50 rounded-xl">
            <div class="flex gap-3 text-left">
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
                            • Menghapus konfigurasi aktuator tersebut
                        </li>
                        <li>
                            • Menghapus seluruh log & riwayat aktivitas aktuator
                        </li>
                        <li>
                            • Menghapus jadwal pakan & grow light yang terikat
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <form
            :action="'/actuators/' + deleteActuator.id"
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
                    class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white">
                    Hapus
                </button>
            </div>
        </form>

    </div>

</div>

</div>

@endsection
