<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel 11 + Tailwind 3.4.17</title>

    {{-- Load CSS melalui Vite --}}
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased">
    {{-- Container utama --}}
    <div class="min-h-screen bg-gray-800 p-8">

        {{-- Test 1: Basic Typography --}}
        <h1 class="text-4xl font-bold text-center text-blue-600 mb-8">
            🎉 Tailwind CSS 3.4.17 Berhasil!
        </h1>

        {{-- Test 2: Card dengan shadow --}}
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                Konfigurasi Laravel 11 + Tailwind
            </h2>
            <p class="text-gray-600 mb-4">
                Jika kamu melihat card ini dengan shadow dan teks berwarna,
                berarti Tailwind CSS sudah berhasil diinstall!
            </p>

            {{-- Test 3: Button dengan hover effect --}}
            <div class="flex gap-4 mt-6">
                <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Primary Button
                </button>
                <button class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Success Button
                </button>
                <button class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    Danger Button
                </button>
            </div>
        </div>

        {{-- Test 4: Grid system --}}
        <div class="max-w-4xl mx-auto mb-8">
            <h3 class="text-xl font-medium text-gray-700 mb-4">Grid Test</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-purple-500 text-white p-4 rounded text-center">Kolom 1</div>
                <div class="bg-pink-500 text-white p-4 rounded text-center">Kolom 2</div>
                <div class="bg-indigo-500 text-white p-4 rounded text-center">Kolom 3</div>
            </div>
        </div>

        {{-- Test 5: Responsive text --}}
        <div class="text-center">
            <p class="text-sm text-gray-500">
                Resize browser untuk test responsive design
            </p>
            <p class="text-lg md:text-xl text-gray-700 mt-2">
                Teks ini akan membesar di desktop
            </p>
        </div>

    </div>
</body>
</html>
