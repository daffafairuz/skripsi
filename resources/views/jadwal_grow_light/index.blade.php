@extends('layouts.app')

@section('content')

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Jadwal Grow Light
        </h1>
        <p class="text-sm text-gray-500">
            Atur jadwal otomatis untuk lampu tanam Anda
        </p>
    </div>

    <!-- Device Card -->
    <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-gray-800 leading-none">
                Grow Light System
            </p>
            <p class="text-[10px] text-green-500 font-medium italic">
                Automatic Schedule
            </p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold shadow-lg shadow-green-100">
            🌱
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
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

<!-- Main Form -->
<form method="POST" action="" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Schedule Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Morning Schedule -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white">
                    🌅
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">
                        Jadwal Pagi
                    </h2>
                    <p class="text-xs text-gray-400">
                        Waktu menyalakan grow light
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Enable/Disable -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <label class="text-sm font-medium text-gray-600">
                        Aktifkan Jadwal Pagi
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="morning_enabled" 
                            value="1"
                            {{ isset($schedule['morning_enabled']) && $schedule['morning_enabled'] ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <!-- Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Jam Menyala
                    </label>
                    <input 
                        type="time" 
                        name="morning_on_time"
                        value="{{ $schedule['morning_on_time'] ?? '06:00' }}"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                    >
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Durasi Menyala (Jam)
                    </label>
                    <select 
                        name="morning_duration"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                    >
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ isset($schedule['morning_duration']) && $schedule['morning_duration'] == $i ? 'selected' : '' }}>
                                {{ $i }} Jam {{ $i > 1 ? '' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Evening Schedule -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                    🌙
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">
                        Jadwal Malam
                    </h2>
                    <p class="text-xs text-gray-400">
                        Waktu menyalakan grow light
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Enable/Disable -->
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <label class="text-sm font-medium text-gray-600">
                        Aktifkan Jadwal Malam
                    </label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="evening_enabled"
                            value="1"
                            {{ isset($schedule['evening_enabled']) && $schedule['evening_enabled'] ? 'checked' : '' }}
                            class="sr-only peer"
                        >
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <!-- Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Jam Menyala
                    </label>
                    <input 
                        type="time" 
                        name="evening_on_time"
                        value="{{ $schedule['evening_on_time'] ?? '18:00' }}"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                    >
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">
                        Durasi Menyala (Jam)
                    </label>
                    <select 
                        name="evening_duration"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                    >
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ isset($schedule['evening_duration']) && $schedule['evening_duration'] == $i ? 'selected' : '' }}>
                                {{ $i }} Jam {{ $i > 1 ? '' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Settings -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white">
                ⚙️
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-700">
                    Pengaturan Lanjutan
                </h2>
                <p class="text-xs text-gray-400">
                    Atur intensitas dan mode operasi
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Intensity -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">
                    Intensitas Cahaya (%)
                </label>
                <div class="flex items-center gap-4">
                    <input 
                        type="range" 
                        name="intensity"
                        min="0" 
                        max="100" 
                        value="{{ $schedule['intensity'] ?? 80 }}"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                        oninput="this.nextElementSibling.value = this.value"
                    >
                    <output class="text-sm font-semibold text-gray-700 min-w-[40px]">
                        {{ $schedule['intensity'] ?? 80 }}%
                    </output>
                </div>
                <p class="text-xs text-gray-400 mt-1">
                    Intensitas cahaya saat grow light menyala
                </p>
            </div>

            <!-- Schedule Type -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Tipe Jadwal
                </label>
                <select 
                    name="schedule_type"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition"
                >
                    <option value="daily" {{ isset($schedule['schedule_type']) && $schedule['schedule_type'] == 'daily' ? 'selected' : '' }}>
                        Harian (Setiap Hari)
                    </option>
                    <option value="weekday" {{ isset($schedule['schedule_type']) && $schedule['schedule_type'] == 'weekday' ? 'selected' : '' }}>
                        Weekday (Senin - Jumat)
                    </option>
                    <option value="weekend" {{ isset($schedule['schedule_type']) && $schedule['schedule_type'] == 'weekend' ? 'selected' : '' }}>
                        Weekend (Sabtu - Minggu)
                    </option>
                </select>
            </div>
        </div>
    </div>

    <!-- Preview Schedule -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-3xl border border-green-100 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center text-white text-sm">
                📅
            </div>
            <h3 class="text-sm font-semibold text-gray-700">
                Preview Jadwal Hari Ini
            </h3>
        </div>
        <div class="space-y-2 text-sm" id="schedulePreview">
            <p class="text-gray-600">🔆 Jadwal akan tampil di sini</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3">
        <a 
            href=""
            class="px-6 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition font-medium">
            Batal
        </a>
        <button 
            type="submit"
            class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium shadow-sm shadow-green-200">
            Simpan Jadwal
        </button>
    </div>
</form>

<!-- Manual Control Card -->
<div class="mt-6 bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-500 to-amber-600 flex items-center justify-center text-white">
                💡
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-700">
                    Kontrol Manual
                </h3>
                <p class="text-xs text-gray-500 mt-1">
                    Nyalakan/matikan grow light secara manual
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <form method="POST" action="" class="inline">
                @csrf
                <button 
                    type="submit"
                    class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-xl hover:bg-yellow-600 transition">
                    Nyalakan Manual
                </button>
            </form>
            
            <form method="POST" action="" class="inline">
                @csrf
                <button 
                    type="submit"
                    class="px-4 py-2 bg-gray-500 text-white text-sm rounded-xl hover:bg-gray-600 transition">
                    Matikan Manual
                </button>
            </form>
        </div>
    </div>

    <!-- Current Status -->
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full {{ isset($currentStatus) && $currentStatus ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></div>
            <span class="text-xs text-gray-600">
                Status saat ini: 
                <strong class="{{ isset($currentStatus) && $currentStatus ? 'text-green-600' : 'text-gray-600' }}">
                    {{ isset($currentStatus) && $currentStatus ? 'Menyala' : 'Mati' }}
                </strong>
            </span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Live preview untuk jadwal
    function updatePreview() {
        const morningEnabled = document.querySelector('input[name="morning_enabled"]').checked;
        const morningTime = document.querySelector('input[name="morning_on_time"]').value;
        const morningDuration = document.querySelector('select[name="morning_duration"]').value;
        
        const eveningEnabled = document.querySelector('input[name="evening_enabled"]').checked;
        const eveningTime = document.querySelector('input[name="evening_on_time"]').value;
        const eveningDuration = document.querySelector('select[name="evening_duration"]').value;
        
        const scheduleType = document.querySelector('select[name="schedule_type"]').value;
        
        let previewHtml = '';
        
        if (morningEnabled) {
            previewHtml += `<p class="text-gray-700">🌅 Pagi: ${morningTime} - Menyala selama ${morningDuration} jam</p>`;
        }
        
        if (eveningEnabled) {
            previewHtml += `<p class="text-gray-700">🌙 Malam: ${eveningTime} - Menyala selama ${eveningDuration} jam</p>`;
        }
        
        if (!morningEnabled && !eveningEnabled) {
            previewHtml = '<p class="text-gray-500">⚠️ Tidak ada jadwal yang aktif</p>';
        }
        
        let typeText = '';
        if (scheduleType === 'daily') typeText = 'Setiap hari';
        else if (scheduleType === 'weekday') typeText = 'Senin - Jumat';
        else typeText = 'Sabtu - Minggu';
        
        previewHtml += `<p class="text-xs text-gray-500 mt-2">📌 Jadwal berlaku: ${typeText}</p>`;
        
        document.getElementById('schedulePreview').innerHTML = previewHtml;
    }
    
    // Event listeners untuk live preview
    document.querySelectorAll('input[name="morning_enabled"], input[name="evening_enabled"], input[name="morning_on_time"], input[name="evening_on_time"], select[name="morning_duration"], select[name="evening_duration"], select[name="schedule_type"]').forEach(el => {
        el.addEventListener('change', updatePreview);
        el.addEventListener('input', updatePreview);
    });
    
    // Initial preview
    updatePreview();
</script>
@endpush