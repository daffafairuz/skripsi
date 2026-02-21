<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring ESP32 - Temperature & pH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Auto refresh setiap 10 detik
        setTimeout(() => location.reload(), 10000);
    </script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">
            🌡️  Monitoring ESP32: Temperature & pH  🧪
        </h1>

        <!-- Data Terkini -->
        @if($stats['latest'])
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-lg p-6 mb-8 text-white">
            <h2 class="text-xl font-semibold mb-4">📊 Data Terkini</h2>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-5xl mb-2">🌡️</div>
                    <div class="text-sm opacity-90">Temperature</div>
                    <div class="text-4xl font-bold">{{ number_format($stats['latest']->temperature, 1) }}°C</div>
                </div>
                <div class="text-center">
                    <div class="text-5xl mb-2">🧪</div>
                    <div class="text-sm opacity-90">pH</div>
                    <div class="text-4xl font-bold">{{ number_format($stats['latest']->ph, 2) }}</div>
                </div>
            </div>
            <div class="text-center mt-4 text-sm opacity-75">
                {{ $stats['latest']->created_at->format('d/m/Y H:i:s') }}
            </div>
        </div>
        @endif

        <!-- Statistik Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total']) }}</div>
                <div class="text-sm text-gray-600">Total Data</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($stats['avg_temp'] ?? 0, 1) }}°C</div>
                <div class="text-sm text-gray-600">Rata-rata Temp</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['avg_ph'] ?? 0, 2) }}</div>
                <div class="text-sm text-gray-600">Rata-rata pH</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 text-center">
                <div class="text-sm">
                    <span class="font-bold text-red-600">{{ number_format($stats['max_temp'] ?? 0, 1) }}°C</span>
                    <span class="mx-1">/</span>
                    <span class="font-bold text-blue-600">{{ number_format($stats['min_temp'] ?? 0, 1) }}°C</span>
                </div>
                <div class="text-sm text-gray-600">Max/Min Temp</div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h2 class="text-lg font-semibold">📋 Riwayat Data ESP32</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Temperature</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">pH</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($data as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-mono text-sm">#{{ $item->id }}</td>
                            <td class="px-6 py-3">
                                <span class="font-semibold {{ $item->temperature > 30 ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ number_format($item->temperature, 1) }} °C
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="font-semibold {{ $item->ph < 6 ? 'text-red-600' : ($item->ph > 8 ? 'text-purple-600' : 'text-green-600') }}">
                                    {{ number_format($item->ph, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">
                                {{ $item->created_at->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                Belum ada data. Jalankan listener MQTT.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t text-sm text-gray-600">
                Update terakhir: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>
</body>
</html>
