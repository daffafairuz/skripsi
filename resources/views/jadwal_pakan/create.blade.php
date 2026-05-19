@extends('layouts.app')

@section('content')
<div class="mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Tambah Jadwal Pakan
        </h1>
        <p class="text-sm text-gray-500">
            Tambahkan jadwal pemberian pakan otomatis
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('jadwal-pakan.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="time" class="block text-sm font-medium text-gray-700 mb-1">
                    Waktu Pemberian Pakan <span class="text-red-500">*</span>
                </label>
                <input type="time" name="time" id="time"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('time') border-red-500 @enderror"
                       value="{{ old('time') }}"
                       required>
                @error('time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Contoh: 08:00, 14:30</p>
            </div>

            <div class="mb-4">
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                    Durasi Nyala (menit) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="duration" id="duration"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 @error('duration') border-red-500 @enderror"
                       placeholder="Contoh: 10"
                       min="1"
                       max="60"
                       value="{{ old('duration', 5) }}"
                       required>
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Berapa menit feeder akan menyala. Minimal 1 menit, maksimal 60 menit.</p>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    Simpan
                </button>
                <a href="{{ route('jadwal-pakan.index') }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
