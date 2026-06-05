@extends('layouts.app')

@section('content')

<div x-data="{

    openCreate: false,
    openEdit: false,
    openDelete: false,

    editSensor: {
        id: '',
        name: '',
        device_id: '',
        type: '',
        unit: '',
        min_threshold: '',
        max_threshold: ''
    },

    deleteSensor: {
        id: '',
        name: ''
    }

}" class="relative">

<!-- HEADER -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

    <div>
        <h1 class="text-2xl font-bold">Daftar Sensor</h1>
        <p class="text-sm text-gray-500">
            Kelola sensor yang terpasang pada device
        </p>
    </div>

    <div class="flex items-center gap-3">
        <button
            @click="openCreate = true"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Sensor
        </button>

        @include('layouts.user-card', ['subtitle' => 'Sensor List'])
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
                <th class="p-4 text-left">Satuan</th>
                <th class="p-4 text-left">Min Threshold</th>
                <th class="p-4 text-left">Max Threshold</th>
                <th class="p-4 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($sensors as $sensor)
            <tr class="border-t hover:bg-gray-50">

                <td class="p-4 font-semibold">{{ $sensor->name }}</td>

                <td class="p-4 text-sm">
                    <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded text-xs">
                        {{ $sensor->device->name ?? '-' }}
                    </span>
                </td>

                <td class="p-4 text-sm">{{ $sensor->type }}</td>

                <td class="p-4 text-sm">{{ $sensor->unit }}</td>

                <td class="p-4 text-sm">{{ $sensor->min_threshold ?? '-' }}</td>

                <td class="p-4 text-sm">{{ $sensor->max_threshold ?? '-' }}</td>

                <td class="p-4">
                    <div class="flex gap-2">

                        <!-- EDIT -->
                        <button
                            @click="
                                openEdit = true;
                                editSensor.id = '{{ $sensor->id }}';
                                editSensor.name = '{{ addslashes($sensor->name) }}';
                                editSensor.device_id = '{{ $sensor->device_id }}';
                                editSensor.type = '{{ addslashes($sensor->type) }}';
                                editSensor.unit = '{{ addslashes($sensor->unit) }}';
                                editSensor.min_threshold = '{{ $sensor->min_threshold }}';
                                editSensor.max_threshold = '{{ $sensor->max_threshold }}';
                            "
                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-sm">
                            Edit
                        </button>

                        <!-- DELETE -->
                        <button
                            type="button"
                            @click="
                                openDelete = true;
                                deleteSensor.id = '{{ $sensor->id }}';
                                deleteSensor.name = '{{ addslashes($sensor->name) }}';
                            "
                            class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-sm hover:bg-red-200 transition">
                            Hapus
                        </button>

                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-400">
                    Belum ada sensor terdaftar
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
            <h2 class="text-xl font-bold">Tambah Sensor</h2>
            <button @click="openCreate = false">✕</button>
        </div>

        <form action="/sensors" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Sensor</label>
                <input type="text" name="name" placeholder="Contoh: Sensor pH Kolam"
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

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">Tipe</label>
                    <input type="text" name="type" placeholder="Contoh: pH, Suhu, Kelembapan"
                           class="w-full border rounded-xl p-3" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Satuan</label>
                    <input type="text" name="unit" placeholder="Contoh: °C, %, pH"
                           class="w-full border rounded-xl p-3" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">Min Threshold</label>
                    <input type="number" step="0.01" name="min_threshold" placeholder="Opsional"
                           class="w-full border rounded-xl p-3">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Max Threshold</label>
                    <input type="number" step="0.01" name="max_threshold" placeholder="Opsional"
                           class="w-full border rounded-xl p-3">
                </div>
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
            <h2 class="text-xl font-bold">Edit Sensor</h2>
            <button @click="openEdit = false">✕</button>
        </div>

        <form :action="'/sensors/' + editSensor.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Sensor</label>
                <input type="text" name="name" x-model="editSensor.name"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Device</label>
                <select name="device_id" x-model="editSensor.device_id"
                        class="w-full border rounded-xl p-3" required>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->mac_address }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">Tipe</label>
                    <input type="text" name="type" x-model="editSensor.type"
                           class="w-full border rounded-xl p-3" required>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Satuan</label>
                    <input type="text" name="unit" x-model="editSensor.unit"
                           class="w-full border rounded-xl p-3" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">Min Threshold</label>
                    <input type="number" step="0.01" name="min_threshold" x-model="editSensor.min_threshold"
                           class="w-full border rounded-xl p-3">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium">Max Threshold</label>
                    <input type="number" step="0.01" name="max_threshold" x-model="editSensor.max_threshold"
                           class="w-full border rounded-xl p-3">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openEdit = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">Batal</button>
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">Update</button>
            </div>
        </form>

    </div>
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
                Hapus Sensor?
            </h2>

            <p class="text-gray-500 text-sm">
                Sensor
                <span
                    class="font-semibold"
                    x-text="deleteSensor.name">
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
                            • Menghapus konfigurasi sensor tersebut
                        </li>
                        <li>
                            • Menghapus seluruh riwayat data pembacaan sensor
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <form
            :action="'/sensors/' + deleteSensor.id"
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
