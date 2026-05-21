@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Edit Jadwal Grow Light
        </h1>
        <p class="text-sm text-gray-500">
            Ubah jadwal lampu tanam
        </p>
    </div>
    @include('layouts.user-card', ['subtitle' => 'Edit Grow Light Schedule'])
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
    <form method="POST" action="{{ route('growlight.update', $schedule->id) }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Pilih Grow Light <span class="text-red-500">*</span></label>
                <select name="actuator_id" required
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition">
                    <option value="">-- Pilih Grow Light --</option>
                    @foreach($growLights as $growLight)
                        <option value="{{ $growLight->id }}" {{ old('actuator_id', $schedule->actuator_id) == $growLight->id ? 'selected' : '' }}>
                            {{ $growLight->name }} ({{ $growLight->device->name ?? 'Tanpa Perangkat' }})
                        </option>
                    @endforeach
                </select>
                @error('actuator_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Start Time</label>
                <input type="time" name="start_time" required
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                       value="{{ old('start_time', $schedule->start_time) }}">
                @error('start_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">End Time</label>
                <input type="time" name="end_time" required
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                       value="{{ old('end_time', $schedule->end_time) }}">
                @error('end_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('growlight.schedule') }}"
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
@endsection
