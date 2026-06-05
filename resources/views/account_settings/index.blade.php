@extends('layouts.app')

@section('content')

<div x-data="{ openDelete: false }">

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
    @include('layouts.user-card', ['subtitle' => 'Account Settings'])

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

            <!-- Preferensi Notifikasi -->
            <div class="border-t border-gray-100 pt-5">

                <h3 class="text-sm font-semibold text-gray-700 mb-4">
                    Preferensi Notifikasi
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email Notification Switch -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-blue-100 transition duration-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="email_notification" class="block text-sm font-semibold text-gray-700 cursor-pointer">
                                    Notifikasi Email
                                </label>
                                <p class="text-xs text-gray-400">
                                    Kirim alert sensor ke email Anda
                                </p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notification" id="email_notification" value="1" class="sr-only peer" {{ old('email_notification', $user->email_notification) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- WhatsApp Notification Switch -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-green-100 transition duration-200">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-50 text-green-600 rounded-xl">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <label for="whatsapp_notification" class="block text-sm font-semibold text-gray-700 cursor-pointer">
                                    Notifikasi WhatsApp
                                </label>
                                <p class="text-xs text-gray-400">
                                    Kirim alert sensor ke WhatsApp Anda
                                </p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="whatsapp_notification" id="whatsapp_notification" value="1" class="sr-only peer" {{ old('whatsapp_notification', $user->whatsapp_notification) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

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

        <!-- Delete Form Trigger -->
        <button
            type="button"
            @click="openDelete = true"
            class="px-4 py-2 bg-red-500 text-white text-sm rounded-xl hover:bg-red-600 transition">
            Hapus Akun
        </button>

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
                Hapus Akun?
            </h2>

            <p class="text-gray-500 text-sm">
                Akun Anda
                <span class="font-semibold">{{ $user->name }}</span>
                akan dihapus permanen beserta seluruh data terkait.
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
                    <p class="font-medium text-red-700 text-sm">
                        Tindakan ini tidak dapat dibatalkan!
                    </p>
                    <p class="text-xs text-red-600 mt-1">
                        Seluruh data site, jadwal otomatis, log aktivitas, dan data sensor Anda akan ikut terhapus dari sistem.
                    </p>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <form
            action="{{ route('account.destroy') }}"
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
                    Hapus Akun Saya
                </button>
            </div>
        </form>

    </div>

</div>

</div>

@endsection