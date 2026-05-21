@extends('layouts.app')

@section('content')



<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Jadwal Grow Light
        </h1>
        <p class="text-sm text-gray-500">
            Daftar jadwal otomatis untuk lampu tanam Anda
        </p>
    </div>

    <div class="flex items-center gap-3">
        <!-- Tombol Add Schedule -->
        <a href="{{ route('growlight.create') }}"
           class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium text-sm shadow-sm shadow-green-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Jadwal
        </a>

        @include('layouts.user-card', ['subtitle' => 'Grow Light Schedule'])
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

@if(isset($sites) && $sites->count() > 1)
<div class="flex items-center gap-3 mb-6 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
    <form action="{{ route('growlight.schedule') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
        <label for="site-select" class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap hidden sm:inline text-gray-500">Pilih Site:</label>
        <div class="relative w-full md:w-64">
            <select id="site-select" name="site_id" onchange="this.form.submit()" 
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                <option value="">Semua Site</option>
                @foreach($sites as $s)
                    <option value="{{ $s->id }}" {{ $selectedSiteId == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </form>
</div>
@endif

<!-- Container -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto max-h-[600px]">
        <table class="w-full text-left">
            <thead class="bg-gray-50 sticky top-0">
                <tr class="text-xs text-gray-500 uppercase">
                    <th class="py-4 px-6">#</th>
                    <th class="py-4 px-6">Lampu (Perangkat)</th>
                    <th class="py-4 px-6">Start Time</th>
                    <th class="py-4 px-6">End Time</th>
                    <th class="py-4 px-6 text-center">Status</th>
                    <th class="py-4 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $index => $schedule)
                    <tr class="border-t border-gray-100 hover:bg-gray-50">
                        <!-- No -->
                        <td class="py-4 px-6 text-xs text-gray-400">
                            #{{ $index + 1 }}
                        </td>

                        <!-- Lampu (Perangkat) -->
                        <td class="py-4 px-6 text-sm font-medium">
                            <span class="text-gray-800">{{ $schedule->actuator->name ?? '-' }}</span>
                            <span class="text-xs text-gray-400 block">{{ $schedule->actuator->device->name ?? '-' }}</span>
                        </td>

                        <!-- Start Time -->
                        <td class="py-4 px-6 text-sm">
                            {{ $schedule->start_time }}
                        </td>

                        <!-- End Time -->
                        <td class="py-4 px-6 text-sm">
                            {{ $schedule->end_time }}
                        </td>



                        <!-- Status -->
                        <td class="py-4 px-6 text-center">
                            @php
                                $now = now();
                                $start = \Carbon\Carbon::parse($schedule->start_time);
                                $end = \Carbon\Carbon::parse($schedule->end_time);
                                $isActive = $now->between($start, $end);
                            @endphp

                            @if($isActive)
                                <span class="px-2 py-1 bg-green-100 text-green-600 rounded-lg text-xs">
                                    ● Menyala
                                </span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs">
                                    ○ Mati
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Edit Button -->
                                <a href="{{ route('growlight.edit', $schedule->id) }}"
                                   class="p-1.5 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>

                                <!-- Delete Button -->
                                <form method="POST" action="{{ route('growlight.destroy', $schedule->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            Belum ada jadwal grow light. Klik tombol "Tambah Jadwal" untuk membuat jadwal baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
