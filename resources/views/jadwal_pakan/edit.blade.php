@extends('layouts.app')

@section('content')
<div class="mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
                Edit Jadwal Pakan
            </h1>
            <p class="text-sm text-gray-500">
                Ubah jadwal pemberian pakan otomatis
            </p>
        </div>
        @include('layouts.user-card', ['subtitle' => 'Edit Feed Schedule'])
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('jadwal-pakan.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="actuator_id" class="block text-sm font-medium text-gray-600 mb-1">
                    Pilih Feeder <span class="text-red-500">*</span>
                </label>
                <select name="actuator_id" id="actuator_id"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition @error('actuator_id') border-red-500 @enderror"
                        required>
                    <option value="">-- Pilih Feeder --</option>
                    @foreach($feeders as $feeder)
                        <option value="{{ $feeder->id }}" {{ old('actuator_id', $schedule->actuator_id) == $feeder->id ? 'selected' : '' }}>
                            {{ $feeder->name }} ({{ $feeder->device->name ?? 'Tanpa Perangkat' }})
                        </option>
                    @endforeach
                </select>
                @error('actuator_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="time" class="block text-sm font-medium text-gray-600 mb-1">
                    Waktu Pemberian Pakan <span class="text-red-500">*</span>
                </label>
                <input type="time" name="time" id="time"
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition @error('time') border-red-500 @enderror"
                       value="{{ old('time', \Carbon\Carbon::parse($schedule->time)->format('H:i')) }}"
                       required>
                @error('time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="duration" class="block text-sm font-medium text-gray-600 mb-1">
                    Durasi Nyala (menit) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="duration" id="duration"
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition @error('duration') border-red-500 @enderror"
                       placeholder="Contoh: 10"
                       min="1"
                       max="60"
                       value="{{ old('duration', $schedule->duration) }}"
                       required>
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Berapa menit feeder akan menyala. Minimal 1 menit, maksimal 60 menit.</p>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('jadwal-pakan.index') }}"
                   class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium shadow-sm shadow-green-200">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
