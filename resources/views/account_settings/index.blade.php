@extends('layouts.app')

@section('content')

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">

    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Pengaturan Akun
        </h1>

        <p class="text-sm text-gray-500">
            Kelola informasi akun dan preferensi Anda
        </p>
    </div>

    <!-- User Card -->
    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">

        <div class="text-right hidden sm:block">

            <p class="text-xs font-bold text-gray-800 leading-none">
                {{ $user->name }}
            </p>

            <p class="text-[10px] text-blue-500 font-medium italic">
                Account Settings
            </p>

        </div>

        <!-- Avatar -->
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-100">

            {{ substr($user->name, 0, 1) }}

        </div>

    </div>

</div>

<!-- Success Message -->
@if(session('success'))

    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">

        {{ session('success') }}

    </div>

@endif

<!-- Form -->
<form
    method="POST"
    action="{{ route('account.update') }}"
    class="space-y-6">

    @csrf
    @method('PUT')

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">

        <h2 class="text-lg font-semibold text-gray-700 mb-6">
            Informasi Akun
        </h2>

        <div class="space-y-5">

            <!-- Name -->
            <div>

                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full px-4 py-2 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition
                    @error('name') border-red-500
                    @else border-gray-200
                    @enderror"
                >

                @error('name')

                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>

            <!-- Email -->
            <div>

                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition
                    @error('email') border-red-500
                    @else border-gray-200
                    @enderror"
                >

                @error('email')

                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>

            <!-- Phone -->
            <div>

                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Nomor Telepon
                </label>

                <input
                    type="text"
                    name="phone_number"
                    value="{{ old('phone_number', $user->phone_number) }}"
                    class="w-full px-4 py-2 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition
                    @error('phone_number') border-red-500
                    @else border-gray-200
                    @enderror"
                >

                @error('phone_number')

                    <p class="text-xs text-red-500 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>

            <!-- Password -->
            <div class="border-t border-gray-100 pt-5">

                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    Ubah Password (Opsional)
                </h3>

                <div class="space-y-4">

                    <!-- Password -->
                    <div>

                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Password Baru
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="w-full px-4 py-2 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition
                            @error('password') border-red-500
                            @else border-gray-200
                            @enderror"
                        >

                        <p class="text-xs text-gray-400 mt-1">
                            Kosongkan jika tidak ingin mengubah password
                        </p>

                        @error('password')

                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <!-- Password Confirmation -->
                    <div>

                        <label class="block text-sm font-medium text-gray-600 mb-1">
                            Konfirmasi Password Baru
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        >

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Buttons -->
    <div class="flex justify-end gap-3">

        <a
            href="{{ url()->previous() }}"
            class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">

            Batal

        </a>

        <button
            type="submit"
            class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium shadow-sm shadow-blue-200">

            Simpan Perubahan

        </button>

    </div>

</form>

<!-- Delete Account -->
<div class="mt-6 bg-red-50/30 rounded-3xl border border-red-100 p-6">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">

        <div>

            <h3 class="text-sm font-semibold text-red-600">
                Hapus Akun
            </h3>

            <p class="text-xs text-gray-500 mt-1">
                Setelah akun dihapus, semua data akan hilang permanen
            </p>

        </div>

        <!-- Delete Form -->
        <form
            method="POST"
            action="{{ route('account.destroy') }}"
            onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan!')">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="px-4 py-2 bg-red-500 text-white text-sm rounded-xl hover:bg-red-600 transition">

                Hapus Akun

            </button>

        </form>

    </div>

</div>

@endsection