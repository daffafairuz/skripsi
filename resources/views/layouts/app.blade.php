<!DOCTYPE html>
<html>
<head>
    <title>AquaPakcoy</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">

<div x-data="{ openSidebar: true, openHistory: false }" class="flex h-screen">

    <!-- SIDEBAR -->
    <div :class="openSidebar ? 'w-64' : 'w-20'"
         class="bg-white shadow transition-all duration-300 flex flex-col">

        <!-- Logo -->
        <div class="flex items-center justify-between p-4">
            <span x-show="openSidebar" class="font-bold text-lg">AquaPakcoy</span>

            <!-- Toggle -->
            <button @click="openSidebar = !openSidebar">
                ☰
            </button>
        </div>

        <!-- Menu -->
        <ul class="space-y-2 px-2">

            <!-- Dashboard -->
            <li>
                <a href="/dashboard"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('dashboard') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <!-- SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M3 12l2-2 4 4 8-8 4 4"/>
                    </svg>

                    <span x-show="openSidebar">Dashboard</span>
                </a>
            </li>

            <!-- Kontrol -->
            <li>
                <a href="/device"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('device') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15"/>
                    </svg>

                    <span x-show="openSidebar">Kontrol</span>
                </a>
            </li>

            <!-- Riwayat (Dropdown) -->
            <li>
                <button @click="openHistory = !openHistory"
                        class="flex items-center gap-3 w-full p-2 rounded hover:bg-gray-100">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4l3 3"/>
                    </svg>

                    <span x-show="openSidebar">Riwayat</span>

                    <span x-show="openSidebar" class="ml-auto">⌄</span>
                </button>

                <!-- Dropdown -->
                <div x-show="openHistory"
                     x-transition
                     class="ml-8 mt-1 space-y-1 text-sm">

                    <a href="/history/suhu" class="block hover:text-green-600">Suhu</a>
                    <a href="/history/kelembapan" class="block hover:text-green-600">Kelembapan</a>
                    <a href="/history/ph" class="block hover:text-green-600">pH</a>
                    <a href="/history/debit" class="block hover:text-green-600">Debit</a>

                </div>
            </li>

            <!-- Account -->
            <li>
                <a href="/account"
                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-100">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M5.121 17.804A9 9 0 1112 21"/>
                    </svg>

                    <span x-show="openSidebar">Akun</span>
                </a>
            </li>

            <!-- Admin -->
            @if(auth()->user()->role == 'admin')
            <li>
                <a href="/users"
                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-100">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M17 20h5v-2a4 4 0 00-5-4"/>
                    </svg>

                    <span x-show="openSidebar">Pengguna</span>
                </a>
            </li>
            @endif

            <!-- Notifikasi -->
            <li>
                <a href="/notifications"
                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-100">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M15 17h5l-1.405-1.405"/>
                    </svg>

                    <span x-show="openSidebar">Notifikasi</span>
                </a>
            </li>

            <!-- Logout -->
            <li>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="flex items-center gap-3 p-2 w-full hover:bg-red-100 rounded">
                        ⎋
                        <span x-show="openSidebar">Logout</span>
                    </button>
                </form>
            </li>

        </ul>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-6">
        @yield('content')
    </div>

</div>

<!-- AlpineJS -->
<script src="https://unpkg.com/alpinejs" defer></script>

</body>
</html>