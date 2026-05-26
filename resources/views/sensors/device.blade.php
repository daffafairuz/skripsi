@extends('layouts.app')

@section('content')

<div>

    <!-- HEADER -->

    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-2xl font-bold">
                {{ $device->name }}
            </h1>

            <p class="text-gray-500">
                {{ $device->mac_address }}
            </p>
        </div>

        @include('layouts.user-card', ['subtitle' => 'Device Telemetry'])

    </div>


    <!-- SENSOR CARDS CAROUSEL -->

    <div class="flex overflow-x-auto gap-4 mb-6 pb-4 scroll-smooth scrollbar-thin scrollbar-thumb-gray-200 select-none">

        @foreach($device->sensors as $sensor)

        @php
        $latest=$sensor->dataSensors->first();
        @endphp

        <div

        data-sensor="{{ $sensor->id }}"

        class="sensor-card
        flex-shrink-0
        w-64
        md:w-72
        bg-white
        rounded-2xl
        shadow-sm
        p-5
        cursor-pointer
        hover:ring-2
        hover:ring-green-500
        transition-all duration-200
        whitespace-normal">

            <p class="text-sm text-gray-400 font-semibold uppercase tracking-wider">

                {{ $sensor->name }}

            </p>

            <p class="text-3xl font-bold mt-3 text-gray-800">

                {{ $latest->value ?? '-' }}

            </p>

            <p class="text-sm text-gray-500">

                {{ $sensor->unit }}

            </p>

            @if(auth()->user()->role === 'admin')
            <div class="mt-4 border-t pt-3" onclick="event.stopPropagation()">
                <form action="{{ route('sensors.update-threshold', $sensor->id) }}" method="POST" class="space-y-2">
                    @csrf
                    @method('PUT')
                    <div class="flex gap-2">
                        <div class="w-1/2">
                            <label class="text-[10px] text-gray-400 block font-semibold">MIN</label>
                            <input type="number" step="any" name="min_threshold" value="{{ $sensor->min_threshold }}" class="w-full border border-gray-200 rounded px-2 py-1 text-xs" placeholder="Min">
                        </div>
                        <div class="w-1/2">
                            <label class="text-[10px] text-gray-400 block font-semibold">MAX</label>
                            <input type="number" step="any" name="max_threshold" value="{{ $sensor->max_threshold }}" class="w-full border border-gray-200 rounded px-2 py-1 text-xs" placeholder="Max">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white text-[10px] py-1.5 rounded font-semibold transition">
                        Simpan Threshold
                    </button>
                </form>
            </div>
            @else
            <div class="mt-2 text-xs text-gray-400 border-t pt-2">
                Threshold: <span class="font-semibold">{{ $sensor->min_threshold ?? '-' }}</span> - <span class="font-semibold">{{ $sensor->max_threshold ?? '-' }}</span>
            </div>
            @endif

        </div>

        @endforeach

    </div>


    <!-- CHART -->

    <div class="bg-white rounded-xl shadow p-6">

        <!-- FILTERS -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 border-b pb-4">
            <h3 id="chartTitle" class="font-bold text-gray-800 text-lg">
                Riwayat Sensor
            </h3>
            
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="flex items-center gap-2">
                    <label for="startDate" class="text-xs text-gray-500 font-semibold">Mulai:</label>
                    <input type="date" id="startDate" class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex items-center gap-2">
                    <label for="endDate" class="text-xs text-gray-500 font-semibold">Selesai:</label>
                    <input type="date" id="endDate" class="border border-gray-200 rounded-xl px-3 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <button id="btnFilter" class="bg-green-500 hover:bg-green-600 text-white px-4 py-1.5 rounded-xl text-xs font-semibold transition duration-150">
                    Filter
                </button>
                <button id="btnReset" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-1.5 rounded-xl text-xs font-semibold transition duration-150">
                    Reset
                </button>
            </div>
        </div>

        <div style="height:400px">

            <canvas id="sensorChart"></canvas>

        </div>

    </div>

</div>

@endsection


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let chart=null;
let currentSensorId=null;

document.addEventListener('DOMContentLoaded', ()=>{

    const cards = document.querySelectorAll('.sensor-card');

    function loadChart(id, startDate = '', endDate = '') {
        currentSensorId = id;
        
        let url = `/sensor/${id}/chart`;
        const params = [];
        if (startDate) params.push(`start_date=${startDate}`);
        if (endDate) params.push(`end_date=${endDate}`);
        if (params.length) url += `?${params.join('&')}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                console.log(data);

                // Set batasan tanggal untuk input
                if (data.min_date && data.max_date) {
                    const startInput = document.getElementById('startDate');
                    const endInput = document.getElementById('endDate');
                    if (startInput && endInput) {
                        startInput.min = data.min_date;
                        startInput.max = data.max_date;
                        endInput.min = data.min_date;
                        endInput.max = data.max_date;
                    }
                }

                if (data.values.length === 0) {
                    data.labels = ['Tidak ada data'];
                    data.values = [0];
                }

                document.getElementById('chartTitle').innerText = 'Riwayat ' + data.sensor;

                const ctx = document.getElementById('sensorChart');

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: data.sensor + ' (' + data.unit + ')',
                            data: data.values,
                            borderWidth: 2,
                            borderColor: '#10B981', // Emerald Green
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                grid: {
                                    color: '#E5E7EB',
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
    }

    // Handler klik kartu sensor
    cards.forEach(card => {
        card.addEventListener('click', () => {
            // Reset input tanggal saat ganti kartu
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';

            // Hapus kelas aktif dari kartu lain
            cards.forEach(c => c.classList.remove('ring-2', 'ring-green-500'));
            // Tambahkan kelas aktif ke kartu terpilih
            card.classList.add('ring-2', 'ring-green-500');

            loadChart(card.dataset.sensor);
        });
    });

    // Handler tombol filter
    document.getElementById('btnFilter').addEventListener('click', () => {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        if (currentSensorId) {
            loadChart(currentSensorId, start, end);
        }
    });

    // Handler tombol reset
    document.getElementById('btnReset').addEventListener('click', () => {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        if (currentSensorId) {
            loadChart(currentSensorId);
        }
    });

    // Muat kartu pertama sebagai default
    if (cards.length) {
        cards[0].classList.add('ring-2', 'ring-green-500');
        loadChart(cards[0].dataset.sensor);
    }
});

</script>