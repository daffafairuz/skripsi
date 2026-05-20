@extends('layouts.app')

@section('content')

<div x-data="{

    openCreate: false,
    openEdit: false,

    editDevice: {
        id: '',
        name: '',
        mac_address: '',
        description: '',
        status: ''
    }

}" class="relative">

<!-- HEADER -->
<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold">Devices</h1>
        <p class="text-sm text-gray-500">
            Monitoring dan kelola device sistem aquaponik
        </p>
    </div>

    <div class="flex items-center gap-3">
        @if(auth()->user()->role == 'admin')
        <button
            @click="openCreate = true"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Device
        </button>
        @endif

        @include('layouts.user-card', ['subtitle' => 'Devices'])
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
<div class="bg-white rounded-2xl shadow overflow-hidden">

    <table class="w-full">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-4 text-left">Nama</th>
                <th class="p-4 text-left">MAC Address</th>
                <th class="p-4 text-left">Deskripsi</th>
                <th class="p-4 text-left">Status</th>
                <th class="p-4 text-left">Sensor</th>
                <th class="p-4 text-left">Aktuator</th>
                @if(auth()->user()->role == 'admin')
                <th class="p-4 text-left">Aksi</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse($devices as $device)
            <tr class="border-t hover:bg-gray-50">

                <td class="p-4 font-semibold">{{ $device->name }}</td>

                <td class="p-4">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded font-mono">{{ $device->mac_address }}</span>
                </td>

                <td class="p-4 text-sm text-gray-500">{{ $device->description ?? '-' }}</td>

                <td class="p-4">
                    @if($device->status == 'available')
                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">Available</span>
                    @elseif($device->status == 'assigned')
                        <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm">Assigned</span>
                    @else
                        <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm">Inactive</span>
                    @endif
                </td>

                <td class="p-4 text-sm">{{ $device->sensors->count() }}</td>

                <td class="p-4 text-sm">{{ $device->actuators->count() }}</td>

                @if(auth()->user()->role == 'admin')
                <td class="p-4">
                    <div class="flex gap-2">

                        <!-- EDIT -->
                        <button
                            @click="
                                openEdit = true;
                                editDevice.id = '{{ $device->id }}';
                                editDevice.name = '{{ addslashes($device->name) }}';
                                editDevice.mac_address = '{{ $device->mac_address }}';
                                editDevice.description = '{{ addslashes($device->description) }}';
                                editDevice.status = '{{ $device->status }}';
                            "
                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-sm">
                            Edit
                        </button>

                        <!-- DELETE -->
                        <form action="/devices/{{ $device->id }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus device ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-sm">
                                Hapus
                            </button>
                        </form>

                    </div>
                </td>
                @endif

            </tr>

            @empty
            <tr>
                <td colspan="7" class="p-8 text-center text-gray-400">
                    Belum ada device terdaftar
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
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Tambah Device</h2>
            <button @click="openCreate = false">✕</button>
        </div>

        <form action="/devices" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Device</label>
                <input type="text" name="name" placeholder="Contoh: ESP32 Kolam A"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">MAC Address</label>
                <input type="text" name="mac_address" placeholder="Contoh: AA:BB:CC:DD:EE:FF"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Deskripsi</label>
                <textarea name="description" placeholder="Deskripsi device (opsional)"
                          class="w-full border rounded-xl p-3" rows="2"></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Status</label>
                <select name="status" class="w-full border rounded-xl p-3">
                    <option value="available">Available</option>
                    <option value="assigned">Assigned</option>
                    <option value="inactive">Inactive</option>
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
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Edit Device</h2>
            <button @click="openEdit = false">✕</button>
        </div>

        <form :action="'/devices/' + editDevice.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Device</label>
                <input type="text" name="name" x-model="editDevice.name"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">MAC Address</label>
                <input type="text" name="mac_address" x-model="editDevice.mac_address"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Deskripsi</label>
                <textarea name="description" x-model="editDevice.description"
                          class="w-full border rounded-xl p-3" rows="2"></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Status</label>
                <select name="status" x-model="editDevice.status"
                        class="w-full border rounded-xl p-3">
                    <option value="available">Available</option>
                    <option value="assigned">Assigned</option>
                    <option value="inactive">Inactive</option>
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

</div>

@endsection