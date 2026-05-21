@extends('layouts.app')

@section('content')

<!-- Admin Dashboard Container -->
<div x-data="{ selectedDevice: null, selectedSiteId: '{{ $selectedSite ? $selectedSite->id : '' }}' }" class="space-y-6 max-w-7xl mx-auto pb-12">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Dashboard Admin</h1>
            <p class="text-sm text-gray-500 mt-1">Pemantauan dan administrasi sistem utama</p>
        </div>
        @include('layouts.user-card', ['subtitle' => 'System Admin'])
    </div>

    <!-- STATS KPI GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        
        <!-- KPI 1: SITES -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Site</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $adminStats['total_sites'] }}</p>
            </div>
        </div>

        <!-- KPI 2: USERS -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
            <div class="p-3 bg-blue-50 text-blue-500 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengguna</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $adminStats['total_users'] }}</p>
            </div>
        </div>

        <!-- KPI 3: DEVICES -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
            <div class="p-3 bg-indigo-50 text-indigo-500 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15M6 14h12M8 18h8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Perangkat</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $adminStats['total_devices'] }}</p>
            </div>
        </div>

        <!-- KPI 4: SENSOR DATA POINTS -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
            <div class="p-3 bg-purple-50 text-purple-500 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Log Telemetri</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($adminStats['total_sensor_data']) }}</p>
            </div>
        </div>

        <!-- KPI 5: SYSTEM NOTIFICATIONS -->
        <div class="bg-white p-5 rounded-3xl border border-gray-100/80 shadow-sm flex items-center gap-4 hover:shadow-md transition duration-300">
            <div class="p-3 bg-rose-50 text-rose-500 rounded-2xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Notifikasi</p>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $adminStats['total_notifications'] }}</p>
            </div>
        </div>

    </div>

    <!-- MAIN TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- COLUMN 1: SITES & SYSTEM LOGS -->
        <div class="space-y-6">

            <!-- SITES LIST -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Site Terdaftar</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Daftar site aktif dalam sistem</p>
                    </div>
                    <a href="/sites" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                        Kelola Site &rarr;
                    </a>
                </div>

                <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                    @forelse($sites->take(5) as $site)
                        <div class="flex items-center justify-between p-4 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded-2xl transition">
                            <div class="min-w-0">
                                <h4 class="font-bold text-sm text-gray-800 truncate">{{ $site->name }}</h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] text-gray-400">Pemilik:</span>
                                    <span class="text-[11px] font-medium text-gray-600 truncate">{{ $site->user->name ?? 'Guest' }}</span>
                                </div>
                            </div>
                            <span class="bg-emerald-50 border border-emerald-100 text-emerald-600 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                Aktif
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 border border-dashed rounded-2xl text-sm">
                            Belum ada site di sistem.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- SYSTEM LOGS / ACTIVITIES -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Aktivitas Sistem Terbaru</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Log notifikasi yang terekam di server</p>
                    </div>
                    <a href="{{ route('notifications') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                        Semua &rarr;
                    </a>
                </div>

                <div class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                    @forelse($activities as $activity)
                        <div class="flex gap-3 items-start">
                            <div class="w-2 h-2 mt-1.5 rounded-full flex-shrink-0
                                {{ $activity->type === 'alert' ? 'bg-red-500' : ($activity->type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500') }}">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-600 leading-snug break-words">
                                    {{ $activity->message }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-semibold text-gray-400">{{ $activity->site->name ?? 'System' }}</span>
                                    <span class="text-[9px] text-gray-300">&bull;</span>
                                    <span class="text-[9px] text-gray-400">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 border border-dashed rounded-2xl text-sm">
                            Belum ada aktivitas terekam.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- COLUMN 2: SENSOR CHART PREVIEW & COLLAPSIBLE DEVICE LIST -->
        <div class="space-y-6">

            <!-- CHART PREVIEW -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Preview Monitoring</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Grafik telemetri untuk site terpilih</p>
                    </div>

                    @if($sites->isNotEmpty())
                        <div class="relative w-full sm:w-48">
                            <select id="admin-site-select" x-model="selectedSiteId" @change="refreshAdminChart($event.target.value)"
                                    class="w-full pl-3 pr-8 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                                @foreach($sites as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="relative h-64 w-full">
                    <canvas id="admin-telemetry-chart"></canvas>
                </div>
            </div>

            <!-- COLLAPSIBLE DEVICE LIST -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100/80 shadow-sm">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Daftar Perangkat Utama</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Sensors & Actuators yang terdaftar</p>
                    </div>
                    <a href="/devices" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                        Kelola Device &rarr;
                    </a>
                </div>

                <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                    @foreach($devices as $device)
                        <div class="border border-gray-100 rounded-2xl overflow-hidden transition duration-200">
                            
                            <!-- Trigger Row -->
                            <div @click="selectedDevice === {{ $device->id }} ? selectedDevice = null : selectedDevice = {{ $device->id }}"
                                 class="p-4 bg-gray-50/50 hover:bg-gray-50 cursor-pointer flex justify-between items-center transition duration-150">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-800">{{ $device->name }}</h4>
                                    <p class="font-mono text-[10px] text-gray-400 mt-0.5">{{ $device->mac_address }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if($device->status === 'assigned')
                                        <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Assigned</span>
                                    @else
                                        <span class="bg-gray-200 text-gray-600 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">{{ $device->status }}</span>
                                    @endif

                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" 
                                         :class="selectedDevice === {{ $device->id }} ? 'rotate-180' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Collapse Area -->
                            <div x-show="selectedDevice === {{ $device->id }}" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-96"
                                 class="border-t border-gray-100 bg-white p-4 space-y-4">
                                
                                <!-- Sensors section -->
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Sensors</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($device->sensors as $sensor)
                                            <span class="bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-semibold px-2.5 py-1 rounded-lg">
                                                {{ $sensor->name }} ({{ $sensor->type }})
                                            </span>
                                        @empty
                                            <span class="text-[11px] text-gray-400 italic">Tidak ada sensor terpasang</span>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Actuators section -->
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Actuators</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($device->actuators as $actuator)
                                            <span class="bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-semibold px-2.5 py-1 rounded-lg">
                                                {{ $actuator->name }} ({{ $actuator->type }})
                                            </span>
                                        @empty
                                            <span class="text-[11px] text-gray-400 italic">Tidak ada aktuator terpasang</span>
                                        @endforelse
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $devices->links() }}
                </div>
            </div>

        </div>

    </div>

</div>

@endsection

@if($selectedSite)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let adminChartInstance = null;

    function renderAdminChart(labels, datasets) {
        const ctx = document.getElementById('admin-telemetry-chart').getContext('2d');
        
        if (adminChartInstance) {
            adminChartInstance.destroy();
        }

        const styledDatasets = datasets.map(ds => {
            let bgGradient = ds.backgroundColor;
            try {
                const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                const rgb = ds.borderColor.match(/\d+/g);
                if (rgb && rgb.length >= 3) {
                    gradient.addColorStop(0, `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, 0.1)`);
                    gradient.addColorStop(1, `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, 0.0)`);
                    bgGradient = gradient;
                }
            } catch(e) {
                console.error("Gradient creation error", e);
            }

            return {
                ...ds,
                backgroundColor: bgGradient,
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 1.5,
                pointHoverRadius: 4,
                pointBackgroundColor: ds.borderColor,
                pointBorderColor: '#ffffff',
                pointBorderWidth: 1,
            };
        });

        adminChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: styledDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 6,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 9,
                                weight: '600',
                                family: "'Inter', sans-serif"
                            },
                            color: '#6B7280'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 10,
                        titleFont: {
                            size: 10,
                            weight: '700',
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 11,
                            family: "'Inter', sans-serif"
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label = label.split(' (')[0];
                                }
                                if (context.parsed.y !== null) {
                                    const unit = context.dataset.unit || '';
                                    label += ': ' + context.parsed.y + ' ' + unit;
                                }
                                return ' ' + label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                size: 9,
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#F3F4F6',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                size: 9,
                                family: "'Inter', sans-serif"
                            }
                        }
                    }
                }
            }
        });
    }

    function refreshAdminChart(siteId) {
        if (!siteId) return;
        fetch(`/chart-data?site_id=${siteId}`)
            .then(res => res.json())
            .then(data => {
                if (data.labels && data.datasets) {
                    renderAdminChart(data.labels, data.datasets);
                }
            })
            .catch(err => console.error("Admin telemetry chart fetch failed:", err));
    }

    document.addEventListener("DOMContentLoaded", function () {
        const initialSiteId = document.getElementById('admin-site-select')?.value;
        if (initialSiteId) {
            refreshAdminChart(initialSiteId);
        }
    });
</script>
@endif