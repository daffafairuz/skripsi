@extends('layouts.app')

@section('content')

@php
    $role = auth()->user()->id;
    $schedules = \App\Models\FeedSchedule::where('site_id', $role)->orderBy('time')->get();
@endphp

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Jadwal Pakan
        </h1>
        <p class="text-sm text-gray-500">
            Daftar jadwal otomatis untuk pakan ikan Anda
        </p>
    </div>
    <a href="{{ route('jadwal-pakan.create') }}"
       class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-medium text-sm shadow-sm shadow-green-200 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Jadwal
    </a>
</div>

<!-- Tabel Daftar Jadwal -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pakan (gram)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Aktif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($schedules as $index => $schedule)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($schedule->amount) }} gram</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $schedule->last_time_active ? \Carbon\Carbon::parse($schedule->last_time_active)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('jadwal-pakan.edit', $schedule->id) }}"
                           class="text-blue-600 hover:text-blue-900">Edit</a>

                        <form action="{{ route('jadwal-pakan.destroy', $schedule->id) }}"
                              method="POST"
                              class="inline-block"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Belum ada jadwal pakan. Silakan tambah jadwal baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
