<!DOCTYPE html>
<html>
<head>
    <title>AquaPakcoy</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">

<div x-data="{ openSidebar: true }" class="flex h-screen">

    <!-- SIDEBAR -->
    <div :class="openSidebar ? 'w-64' : 'w-20'"
         class="bg-white shadow transition-all duration-300 flex flex-col">

        <!-- Logo -->
        <div class="flex items-center justify-between p-4">
            <span x-show="openSidebar" class="font-bold text-lg">AquaPakcoy</span>

            <button @click="openSidebar = !openSidebar"
                    class="text-gray-500 hover:text-black">
                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- MENU -->
        <ul class="space-y-2 px-2">

            <!-- Dashboard -->
            <li>
                <a href="/dashboard"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('dashboard') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M3 13h8V3H3v10zM13 21h8v-6h-8v6zM13 3v6h8V3h-8zM3 21h8v-4H3v4z"/>
                    </svg>

                    <span x-show="openSidebar">Dashboard</span>
                </a>
            </li>

            <!-- Sites -->
            <li>
                <a href="/sites"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('sites*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M3 10h18M5 10v10h14V10M9 10V6h6v4"/>
                    </svg>

                    <span x-show="openSidebar">Sites</span>
                </a>
            </li>

            <!-- Devices -->
            <li>
                <a href="/devices"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('devices*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15M6 14h12M8 18h8"/>
                    </svg>

                    <span x-show="openSidebar">Devices</span>
                </a>
            </li>

            <!-- Data Sensor -->
            <li>
                <a href="/sensor"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('sensor*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
                              d="M12 8v4l3 3M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                    </svg>

                    <span x-show="openSidebar">Data Sensor</span>
                </a>
            </li>

            <!-- Jadwal Pakan -->
            <li>
                <a href="/feeding"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('feeding*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2" d="M12 6v6l4 2"/>
                    </svg>

                    <span x-show="openSidebar">Jadwal Pakan</span>
                </a>
            </li>

            <!-- Log Aktuator -->
            <li>
                <a href="/actuator"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('actuator*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>

                    <span x-show="openSidebar">Log Aktuator</span>
                </a>
            </li>

            <!-- Account -->
            <li>
                <a href="/account"
                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-100">

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
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

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
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

                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-width="2"
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

                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                        </svg>

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

<script src="https://unpkg.com/alpinejs" defer></script>

</body>
</html>