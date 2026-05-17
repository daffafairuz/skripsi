@extends('layouts.app')

@section('content')

@php
    $role = auth()->user()->role;
@endphp

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">

    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            Notifikasi
        </h1>

        <p class="text-sm text-gray-500">
            Pemberitahuan penting terkait kondisi lingkungan
        </p>
    </div>

</div>

<!-- Container -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="overflow-y-auto max-h-[600px]">

        <table class="w-full text-left">

            <thead class="bg-gray-50">

                <tr class="text-xs text-gray-500 uppercase">

                    <th class="py-4 px-6">#</th>
                    <th class="py-4 px-6">Waktu</th>

                    @if($role === 'admin')
                        <th class="py-4 px-6">Pengirim</th>
                    @endif

                    <th class="py-4 px-6">Pesan</th>
                    <th class="py-4 px-6 text-center">Status</th>

                </tr>

            </thead>

            <tbody>

                @forelse($notifications as $notification)

                    <tr class="border-t border-gray-100 hover:bg-gray-50">

                        <!-- ID -->
                        <td class="py-4 px-6 text-xs text-gray-400">
                            #NOT-{{ $notification->id }}
                        </td>

                        <!-- Date -->
                        <td class="py-4 px-6">

                            <div class="flex flex-col">

                                <span class="text-sm font-medium">
                                    {{ $notification->created_at->format('Y-m-d') }}
                                </span>

                                <span class="text-xs text-gray-400">
                                    {{ $notification->created_at->format('H:i:s') }} WIB
                                </span>

                            </div>

                        </td>

                        <!-- Sender -->
                        @if($role === 'admin')

                            <td class="py-4 px-6 text-sm">
                                {{ $notification->site->user->name ?? 'Unknown' }}
                            </td>

                        @endif

                        <!-- Message -->
                        <td class="py-4 px-6">

                            <div class="flex flex-col gap-1">

                                <span class="text-sm text-gray-700">
                                    {{ $notification->message }}
                                </span>

                                @if($notification->type === 'alert')

                                    <span class="text-xs text-red-500 font-medium">
                                        ⚠️ Perlu Tindakan Segera
                                    </span>

                                @endif

                            </div>

                        </td>
                        <!-- Status -->
                        <td class="py-4 px-6 text-center">

                            @if($notification->is_read)

                                <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs">
                                    ✓ Dibaca
                                </span>

                            @else

                                <span class="px-2 py-1 bg-blue-100 text-blue-600 rounded-lg text-xs">
                                    ● Baru
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="{{ $role === 'admin' ? 5 : 4 }}"
                            class="py-12 text-center text-gray-400">

                            Tidak ada notifikasi

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection