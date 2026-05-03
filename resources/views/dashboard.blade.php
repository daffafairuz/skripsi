@extends('layouts.app')

@section('content')

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Dashboard</h1>
    <div class="flex items-center gap-3">
        <!-- Notification -->
        <div class="relative">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-width="2"
                      d="M15 17h5l-1.405-1.405M19 13V9a7 7 0 10-14 0v4l-1.405 1.405H9m6 0v1a3 3 0 11-6 0v-1"/>
            </svg>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1 rounded-full">0</span>
        </div>

        <span>{{ auth()->user()->name ?? 'User' }}</span>
    </div>
</div>

{{-- ================= USER ================= --}}
@if(auth()->user()->role == 'user')

    {{-- USER BELUM ADA SITE --}}
    @if(!$hasSite)

    <div class="bg-white rounded-xl shadow p-6 mb-6 flex items-center justify-between">

        <div>
            <h2 class="text-lg font-bold mb-2">Belum Ada Site</h2>
            <p class="text-gray-500 mb-4">
                Kamu belum menambahkan site. Tambahkan site terlebih dahulu untuk mulai monitoring sistem akuaponik.
            </p>

            <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center gap-2">
                <!-- plus icon -->
                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Site
            </button>
        </div>

        <!-- Illustration -->
        <div class="hidden md:block">
            <svg class="w-40 h-40 text-gray-300" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-width="1.5"
                      d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>
            </svg>
        </div>

    </div>

    <!-- STATS -->
    <div class="grid grid-cols-4 gap-4 mb-6">

        <!-- Card -->
        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-sm text-gray-500">Total Site</p>
            <p class="text-2xl font-bold">0</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-sm text-gray-500">Total Device</p>
            <p class="text-2xl font-bold">0</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-sm text-gray-500">Data Sensor Terakhir</p>
            <p class="text-xl font-bold">-</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow">
            <p class="text-sm text-gray-500">Notifikasi</p>
            <p class="text-2xl font-bold">0</p>
        </div>

    </div>

    <!-- EMPTY SITE -->
    <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
        Anda belum memiliki site
    </div>

    @else

    {{-- USER SUDAH ADA SITE --}}
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="font-bold mb-4">Monitoring</h2>
        <p class="text-gray-500">Data akan tampil di sini...</p>
    </div>

    @endif

{{-- ================= ADMIN ================= --}}
@else

<div class="grid grid-cols-5 gap-4 mb-6">

    <div class="bg-white p-4 rounded-xl shadow">
        <p class="text-sm text-gray-500">Total Site</p>
        <p class="text-2xl font-bold">0</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow">
        <p class="text-sm text-gray-500">Total User</p>
        <p class="text-2xl font-bold">0</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow">
        <p class="text-sm text-gray-500">Total Device</p>
        <p class="text-2xl font-bold">0</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow">
        <p class="text-sm text-gray-500">Total Data Sensor</p>
        <p class="text-2xl font-bold">0</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow">
        <p class="text-sm text-gray-500">Notifikasi</p>
        <p class="text-2xl font-bold">0</p>
    </div>

</div>

<!-- EMPTY STATE ADMIN -->
<div class="grid grid-cols-2 gap-4">

    <div class="bg-white p-6 rounded-xl shadow text-center">
        <h3 class="font-bold mb-2">Belum ada site di sistem</h3>
        <p class="text-gray-500 mb-4">
            Silakan tambahkan site pertama untuk memulai.
        </p>

        <button class="bg-green-500 text-white px-4 py-2 rounded">
            + Tambah Site
        </button>
    </div>

    <div class="bg-white p-6 rounded-xl shadow text-center">
        <h3 class="font-bold mb-2">Belum ada aktivitas</h3>
        <p class="text-gray-500">
            Aktivitas sistem akan muncul di sini.
        </p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow mt-6">

        <h3 class="font-bold mb-4">Monitoring</h3>

        <div class="relative">

            <canvas id="chart"></canvas>

            <!-- LEFT -->
            <button onclick="prevChart()"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-full transition">

                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- RIGHT -->
            <button onclick="nextChart()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-full transition">

                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

        </div>

    </div>

</div>

@endif

@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart;
let chartIndex = 0;

const chartTypes = [
    { key: 'temperature', label: 'Suhu (°C)' },
    { key: 'ph', label: 'pH Air' }
];

function loadChart() {
    fetch('/chart-data')
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('chart').getContext('2d');

            if (chart) chart.destroy();

            const current = chartTypes[chartIndex];

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: current.label,
                        data: data[current.key],
                        borderWidth: 2,
                        tension: 0.4,
                        spanGaps: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    }
                }
            });

        });
}

function nextChart() {
    chartIndex = (chartIndex + 1) % chartTypes.length;
    loadChart();
}

function prevChart() {
    chartIndex = (chartIndex - 1 + chartTypes.length) % chartTypes.length;
    loadChart();
}

document.addEventListener("DOMContentLoaded", function () {
    loadChart();
});
</script>