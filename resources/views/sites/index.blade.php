@extends('layouts.app')

@section('content')

<div x-data="{
    openCreate: false,
    openEdit: false,
    editSite: {
        id: '',
        name: '',
        location: '',
        description: '',
        mac_address: '',
        user_id: ''
    }
}" class="relative">

<!-- HEADER -->
<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold">Sites</h1>
        <p class="text-gray-500 text-sm">
            Daftar site aquaponik yang terdaftar
        </p>
    </div>

    @if(auth()->user()->role == 'admin')
    <button
        @click="openCreate = true"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">

        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>

        Tambah Site
    </button>
    @endif

</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

<!-- Error Messages -->
@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ================= ADMIN ================= --}}
@if(auth()->user()->role == 'admin')

<div class="bg-white rounded-xl shadow p-6">

    <!-- TITLE -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-bold text-lg">Semua Site Terdaftar</h2>
            <p class="text-sm text-gray-500">Kelola semua site aquaponik</p>
        </div>
        <span class="text-sm text-gray-400">
            Total {{ count($sites) }} Site
        </span>
    </div>

    <!-- LIST -->
    <div class="space-y-4">

        @forelse($sites as $site)

        <div class="border rounded-xl p-5 hover:bg-gray-50 transition">
            <div class="flex items-center justify-between">

                <!-- LEFT -->
                <div class="flex items-center gap-4">

                    <!-- ICON -->
                    <div class="bg-green-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>
                        </svg>
                    </div>

                    <!-- INFO -->
                    <div>
                        <h3 class="font-semibold text-lg">{{ $site->name ?? $site->location }}</h3>
                        <div class="text-sm text-gray-500 space-y-1">
                            <p>Owner : {{ $site->user->name ?? '-' }}</p>
                            <p>Lokasi : {{ $site->location }}</p>
                            <p>MAC : {{ $site->mac_address }}</p>
                            <p>Total Device : {{ $site->devices->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="text-right flex items-center gap-2">

                    <!-- EDIT -->
                    <button
                        @click="
                            openEdit = true;
                            editSite.id = '{{ $site->id }}';
                            editSite.name = '{{ addslashes($site->name) }}';
                            editSite.location = '{{ addslashes($site->location) }}';
                            editSite.description = '{{ addslashes($site->description) }}';
                            editSite.mac_address = '{{ $site->mac_address }}';
                            editSite.user_id = '{{ $site->user_id }}';
                        "
                        class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-sm">
                        Edit
                    </button>

                    <!-- DELETE -->
                    <form action="{{ route('sites.destroy', $site->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus site ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-sm">
                            Hapus
                        </button>
                    </form>

                </div>

            </div>
        </div>

        @empty
        <div class="text-center py-8 text-gray-400">
            <p>Belum ada site terdaftar</p>
        </div>
        @endforelse

    </div>

</div>

{{-- ================= USER ================= --}}
@else

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    @forelse($sites as $site)

    <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition">

        <!-- HEADER -->
        <div class="flex items-start justify-between mb-4">
            <div class="bg-green-100 p-3 rounded-xl">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>
                </svg>
            </div>

            <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                Aktif
            </span>
        </div>

        <!-- CONTENT -->
        <div class="mb-5">
            <h2 class="font-bold text-xl mb-2">{{ $site->name ?? $site->location }}</h2>
            <div class="text-sm text-gray-500 space-y-1">
                <p>Lokasi : {{ $site->location }}</p>
                <p>Total Device : {{ $site->devices->count() }}</p>
            </div>
        </div>

        <!-- ACTION -->
        <div class="flex items-center gap-3">
            <a href="/dashboard" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition text-center">
                Monitoring
            </a>
        </div>
    </div>

    @empty
    <div class="col-span-2 text-center py-8 text-gray-400">
        <p>Anda belum memiliki site</p>
    </div>
    @endforelse

</div>

@endif

<!-- ================= CREATE MODAL ================= -->
<div
    x-show="openCreate"
    x-transition
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Tambah Site</h2>
            <button @click="openCreate = false">✕</button>
        </div>

        <form action="{{ route('sites.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Site</label>
                <input type="text" name="name" placeholder="Contoh: Kolam Lele A"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Lokasi</label>
                <input type="text" name="location" placeholder="Contoh: Wonosobo"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Deskripsi</label>
                <textarea name="description" placeholder="Deskripsi site (opsional)"
                          class="w-full border rounded-xl p-3" rows="2"></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">MAC Address</label>
                <input type="text" name="mac_address" placeholder="Contoh: AA:BB:CC:DD:EE:FF"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Owner (User)</label>
                <select name="user_id" class="w-full border rounded-xl p-3" required>
                    <option value="">-- Pilih User --</option>
                    @php
                        $users = \App\Models\User::where('role', 'user')->get();
                    @endphp
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openCreate = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">
                    Batal
                </button>
                <button class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl transition">
                    Simpan
                </button>
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
            <h2 class="text-xl font-bold">Edit Site</h2>
            <button @click="openEdit = false">✕</button>
        </div>

        <form
            :action="'/sites/' + editSite.id"
            method="POST"
            class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 text-sm font-medium">Nama Site</label>
                <input type="text" name="name" x-model="editSite.name"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Lokasi</label>
                <input type="text" name="location" x-model="editSite.location"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Deskripsi</label>
                <textarea name="description" x-model="editSite.description"
                          class="w-full border rounded-xl p-3" rows="2"></textarea>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">MAC Address</label>
                <input type="text" name="mac_address" x-model="editSite.mac_address"
                       class="w-full border rounded-xl p-3" required>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Owner (User)</label>
                <select name="user_id" x-model="editSite.user_id"
                        class="w-full border rounded-xl p-3" required>
                    @php
                        $usersEdit = \App\Models\User::where('role', 'user')->get();
                    @endphp
                    @foreach($usersEdit as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" @click="openEdit = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">
                    Batal
                </button>
                <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">
                    Update
                </button>
            </div>
        </form>

    </div>
</div>

</div>

@endsection