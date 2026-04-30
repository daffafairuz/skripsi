@extends('layouts.app')

@section('content')

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Dashboard</h2>
    <div>Admin</div>
</div>

<!-- Cards -->
<div class="grid grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-4 rounded shadow">
        <h3>Kelembapan</h3>
        <p class="text-2xl font-bold">65%</p>
        <p class="text-green-500">Normal</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3>Suhu</h3>
        <p class="text-2xl font-bold">28.4°C</p>
        <p class="text-green-500">Normal</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3>pH Air</h3>
        <p class="text-2xl font-bold">6.8</p>
        <p class="text-green-500">Ideal</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3>Debit Air</h3>
        <p class="text-2xl font-bold">1700 ml/min</p>
        <p class="text-green-500">Stabil</p>
    </div>

</div>

<!-- Middle Section -->
<div class="grid grid-cols-3 gap-4 mb-6">

    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-bold mb-2">Status Sistem</h3>
        <p>Pompa: <span class="text-green-500">ON</span></p>
        <p>Aerator: <span class="text-green-500">ON</span></p>
        <p>Feeder: <span class="text-gray-500">Standby</span></p>
    </div>

   <div class="bg-white p-4 rounded shadow relative">

        <h3 class="font-bold mb-2">Grafik Monitoring</h3>

        <!-- Chart -->
        <div class="relative">
            <canvas id="chart"></canvas>

            <!-- LEFT ARROW -->
            <button onclick="prevChart()"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-full transition">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>

            </button>

            <!-- RIGHT ARROW -->
            <button onclick="nextChart()"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/30 hover:bg-black/50 p-2 rounded-full transition">

                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>

            </button>
        </div>

    </div>
</div>

<!-- Bottom -->
<div class="grid grid-cols-3 gap-4">

    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-bold mb-2">Jadwal Pakan</h3>
        <p>08:00 & 17:00</p>
        <p>Terakhir: <span class="text-green-500">Berhasil</span></p>
        <p>Jumlah: 50 gram</p>
    </div>

    <div class="bg-white p-4 rounded shadow flex justify-around items-center">
        <button class="bg-green-500 text-white px-4 py-2 rounded">Pompa</button>
        <button class="bg-yellow-500 text-white px-4 py-2 rounded">Feed</button>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Reset</button>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <h3 class="font-bold mb-2">Notifikasi</h3>
        <ul class="text-sm space-y-2">
            <li>⚠️ Kelembapan tinggi</li>
            <li>ℹ️ Pompa aktif otomatis</li>
            <li>✅ Pakan berhasil</li>
        </ul>
    </div>

</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chart;
let chartIndex = 0;

const chartTypes = [
    { key: 'temperature', label: 'Suhu (°C)', color: 'rgb(34,197,94)' },
    { key: 'ph', label: 'pH', color: 'rgb(59,130,246)' }
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
                        borderColor: current.color,
                        backgroundColor: 'rgba(0,0,0,0.05)',
                        tension: 0.4,
                        spanGaps: true
                    }]
                },
                options: {
                    responsive: true
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