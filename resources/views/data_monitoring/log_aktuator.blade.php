@extends('layouts.app')

@section('content')

<!-- Header Section -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Actuator History Logs</h1>
        <p class="text-sm text-gray-500">Menampilkan riwayat aktivitas untuk setiap perangkat yang terhubung</p>
    </div>
    
    @include('layouts.user-card', ['subtitle' => 'Online'])
</div>

<!-- Filter Section -->
@if(isset($sites) && !$sites->isEmpty())
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm mb-8 transition duration-300 hover:shadow-md">
        <form action="{{ route('actuator-log') }}" method="GET" class="flex flex-col md:flex-row items-end gap-5">
            <!-- Filter Site -->
            <div class="flex-1 w-full">
                <label for="site-select" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filter Site</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    <select id="site-select" name="site_id" onchange="document.getElementById('device-select').value = ''; this.form.submit();"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none cursor-pointer hover:bg-gray-50">
                        <option value="">Semua Site</option>
                        @foreach($sites as $s)
                            <option value="{{ $s->id }}" {{ $selectedSiteId == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filter Device -->
            <div class="flex-1 w-full">
                <label for="device-select" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Filter Device</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <select id="device-select" name="device_id" onchange="this.form.submit();"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none cursor-pointer hover:bg-gray-50">
                        <option value="">Semua Device</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->id }}" {{ $selectedDeviceId == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tampilkan Baris -->
            <div class="w-full md:w-36">
                <label for="per-page-select" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tampilkan</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </span>
                    <select id="per-page-select" name="per_page" onchange="this.form.submit();"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50/50 border border-gray-200/80 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all appearance-none cursor-pointer hover:bg-gray-50">
                        @foreach([10, 30, 50, 100] as $opt)
                            <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }} Baris</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Keep active tab parameter in the form -->
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            @if($selectedSiteId || $selectedDeviceId || $perPage != 10)
                <div class="w-full md:w-auto">
                    <a href="{{ route('actuator-log') }}"
                       class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl text-sm font-bold transition-all shadow-sm hover:shadow active:scale-95">
                        Reset
                    </a>
                </div>
            @endif
        </form>
    </div>
@endif

<!-- Main Container -->
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    
    <!-- Tab Navigation Bar -->
    <div class="flex flex-wrap border-b border-gray-100 bg-gray-50/50 p-3 gap-2">
        <a href="{{ route('actuator-log', array_merge(request()->query(), ['tab' => 'all', 'page' => 1])) }}" 
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3 {{ $activeTab === 'all' ? 'bg-white shadow-md text-gray-800 scale-105' : 'text-gray-500 hover:bg-gray-100' }}">
            <span class="w-3 h-3 rounded-full bg-gray-500 shadow-[0_0_8px_rgba(107,114,128,0.5)]"></span>
            Semua ({{ isset($groupedLogs) ? collect($groupedLogs)->flatten(1)->count() : 0 }})
        </a>

        @foreach($availableTabs as $tabKey => $tabInfo)
        <a href="{{ route('actuator-log', array_merge(request()->query(), ['tab' => $tabKey, 'page' => 1])) }}" 
            class="flex-1 min-w-[120px] py-3 px-4 rounded-2xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-3 {{ $activeTab === $tabKey ? 'bg-white shadow-md ' . $tabInfo['text_color'] . ' scale-105' : 'text-gray-500 hover:bg-gray-100' }}">
            <span class="w-3.5 h-3.5 rounded-full {{ $tabInfo['bg_dot'] }}"></span>
            {{ $tabInfo['label'] }} ({{ ($groupedLogs[$tabKey] ?? collect())->count() }})
        </a>
        @endforeach
    </div>

    <!-- Table Area -->
    <div class="overflow-y-auto max-h-[600px] border-t border-gray-50 scrollbar-thin scrollbar-thumb-gray-200">
        <table class="w-full text-left border-separate border-spacing-0">
            <thead class="sticky top-0 bg-white/95 backdrop-blur-md z-10 shadow-sm">
                <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-[0.1em]">
                    <th class="py-4 px-6 border-b border-gray-100">Waktu Kejadian</th>
                    <th class="py-4 px-6 border-b border-gray-100">Aktuator</th>
                    <th class="py-4 px-6 border-b border-gray-100">Status</th>
                    <th class="py-4 px-6 border-b border-gray-100">Metode Kontrol</th>
                    <th class="py-4 px-6 border-b border-gray-100">Device</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-50">

                @forelse($logs as $log)
                <tr class="hover:bg-blue-50/40 transition-colors group">

                    <td class="py-4 px-6">
                        <div class="flex flex-col">
                            <span class="text-gray-700 font-medium">{{ $log->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-gray-400 italic">{{ $log->created_at->format('H:i:s') }} WIB</span>
                        </div>
                    </td>

                    <td class="py-4 px-6">
                        <span class="text-xs font-semibold text-gray-700">{{ $log->actuator->name ?? '-' }}</span>
                    </td>

                    <td class="py-4 px-6">
                        @if($log->action === 'on')
                            <span class="bg-green-100 text-green-700 text-[10px] px-3 py-1 rounded-lg font-black tracking-widest">ON</span>
                        @else
                            <span class="bg-red-50 text-red-600 text-[10px] px-3 py-1 rounded-lg font-black tracking-widest">OFF</span>
                        @endif
                    </td>

                    <td class="py-4 px-6">
                        @if($log->triggered_by === 'manual')
                            <span class="text-xs text-gray-600 bg-blue-50 px-2 py-1 rounded-md font-medium">Manual</span>
                        @else
                            <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-md font-medium">Auto-Sensor</span>
                        @endif
                    </td>

                    <td class="py-4 px-6 text-gray-500 text-xs">
                        {{ $log->actuator->device->name ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 px-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-semibold">Belum ada log aktuator</p>
                        <p class="text-sm">Data akan muncul setelah aktuator dioperasikan</p>
                    </td>
                </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Table Footer with Pagination links -->
    <div class="p-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">
            Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log terbaru
        </p>
        <div class="px-2">
            {{ $logs->links() }}
        </div>
    </div>
</div>

@endsection