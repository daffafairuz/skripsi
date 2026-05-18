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
            <span x-show="openSidebar" class="font-bold text-lg">
                AquaPakcoy
            </span>

            <button @click="openSidebar = !openSidebar"
                    class="text-gray-500 hover:text-black">

                <svg class="w-5 h-5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        @php
            $role = auth()->user()->role;
        @endphp

        <!-- MENU -->
        <ul class="space-y-2 px-2">

            <!-- DASHBOARD -->
            <li>
                <a href="/dashboard"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('dashboard') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-width="2"
                              d="M3 13h8V3H3v10zM13 21h8v-6h-8v6zM13 3v6h8V3h-8zM3 21h8v-4H3v4z"/>
                    </svg>

                    <span x-show="openSidebar">Dashboard</span>
                </a>
            </li>

            <!-- ========================= -->
            <!-- MENU KHUSUS USER -->
            <!-- ========================= -->
            @if($role == 'user')

            <!-- CONTROL PERANGKAT -->
            <li>
                <a href="/feeding"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('feeding*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <!-- Icon Control/Settings -->
                        <path stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>

                    <span x-show="openSidebar">Kontrol Perangkat</span>
                </a>
            </li>

            <!-- KELOLA DEVICE -->
            <li>
                <a href="/feeding"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('feeding*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Device/Monitor -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>

                    <span x-show="openSidebar">Kelola Device</span>
                </a>
            </li>

            <!-- JADWAL PAKAN -->
            <li>
                <a href="{{ route('jadwal-pakan.index') }}"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('jadwal-pakan*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Schedule/Calendar for Feeding -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 13h.01M8 13h.01M16 13h.01M12 17h.01M8 17h.01M16 17h.01"/>
                    </svg>

                    <span x-show="openSidebar">Jadwal Pakan</span>
                </a>
            </li>

            <!-- JADWAL GROW LIGHT -->
            <li>
                <a href="{{ route('growlight.schedule') }}"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('growlight*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Light/Lamp -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>

                    <span x-show="openSidebar">Jadwal Grow Light</span>
                </a>
            </li>

            <!-- DROPDOWN SENSOR -->
            <li x-data="{ openSensorMenu: false }">

                <!-- BUTTON DROPDOWN -->
                <button @click="openSensorMenu = !openSensorMenu"
                        class="w-full flex items-center justify-between gap-3 p-2 rounded transition hover:bg-gray-100">

                    <div class="flex items-center gap-3">

                        <svg class="w-5 h-5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <!-- Icon Sensor/Monitoring -->
                            <path stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>

                        <span x-show="openSidebar">Data Monitoring</span>
                    </div>

                    <!-- ICON PANAH -->
                    <svg x-show="openSidebar"
                        :class="openSensorMenu ? 'rotate-180' : ''"
                        class="w-4 h-4 transition-transform duration-200"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path stroke-width="2"
                            d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- ISI DROPDOWN -->
                <ul x-show="openSensorMenu"
                    x-transition
                    class="ml-6 mt-2 space-y-2">

                    <!-- LOG AKTUATOR -->
                    <li>
                        <a href="{{ route('actuator-log') }}"
                        class="flex items-center gap-3 p-2 rounded transition
                        {{ request()->is('actuator*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                            <svg class="w-4 h-4 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <!-- Icon Actuator/Engine -->
                                <path stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 5H9l-1-5z"/>
                                <path stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 20h16a2 2 0 002-2V8a2 2 0 00-2-2h-2"/>
                            </svg>

                            <span x-show="openSidebar">
                                Log Aktuator
                            </span>
                        </a>
                    </li>

                    <!-- RIWAYAT DATA SENSOR -->
                    <li>
                        <a href="{{ route('data-sensor') }}"
                        class="flex items-center gap-3 p-2 rounded transition
                        {{ request()->is('data-sensor') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                            <svg class="w-4 h-4 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <!-- Icon History/Data -->
                                <path stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>

                            <span x-show="openSidebar">
                                Riwayat Data Sensor
                            </span>
                        </a>
                    </li>

                </ul>
            </li>

            @endif

            <!-- ========================= -->
            <!-- MENU KHUSUS ADMIN -->
            <!-- ========================= -->
            @if($role == 'admin')

            <!-- SITES -->
            <li>
                <a href="/sites"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('sites*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Sites/Location -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>

                    <span x-show="openSidebar">Sites</span>
                </a>
            </li>

            <!-- DAFTAR SENSOR -->
            <li>
                <a href="/sensors"
                class="flex items-center gap-3 p-2 rounded transition
                {{ request()->is('sensors*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <!-- Icon Sensor -->
                        <path stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>

                    <span x-show="openSidebar">Daftar Sensor</span>
                </a>
            </li>

            <!-- DAFTAR AKTUATOR -->
            <li>
                <a href="/actuators"
                class="flex items-center gap-3 p-2 rounded transition
                {{ request()->is('actuators*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <!-- Icon Aktuator -->
                        <path stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 5H9l-1-5z"/>
                        <path stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 20h16a2 2 0 002-2V8a2 2 0 00-2-2h-2"/>
                    </svg>

                    <span x-show="openSidebar">Daftar Aktuator</span>
                </a>
            </li>

            <!-- DEVICES -->
            <li>
                <a href="/devices"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('devices*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Devices -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M9.75 3v2.25M14.25 3v2.25M4.5 9.75h15M6 14h12M8 18h8"/>
                    </svg>

                    <span x-show="openSidebar">Devices</span>
                </a>
            </li>

            <!-- PENGGUNA -->
            <li>
                <a href="/users"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('users*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Users -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>

                    <span x-show="openSidebar">Pengguna</span>
                </a>
            </li>

            @endif

            <!-- NOTIFIKASI -->
            <li>
                <a href="{{ route('notifications') }}"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('notifications*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Notification/Bell -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>

                    <span x-show="openSidebar">Notifikasi</span>
                </a>
            </li>

            <!-- ACCOUNT -->
            <li>
                <a href="{{ route('account-setting') }}"
                   class="flex items-center gap-3 p-2 rounded transition
                   {{ request()->is('account*') ? 'bg-green-500 text-white' : 'hover:bg-gray-100' }}">

                    <svg class="w-5 h-5 flex-shrink-0"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <!-- Icon Account/Profile -->
                        <path stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>

                    <span x-show="openSidebar">Akun</span>
                </a>
            </li>

            <!-- LOGOUT -->
            <li>
                <form method="POST" action="/logout">
                    @csrf

                    <button type="submit"
                            class="flex items-center gap-3 p-2 w-full rounded hover:bg-red-100">

                        <svg class="w-5 h-5 flex-shrink-0"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <!-- Icon Logout -->
                            <path stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>

                        <span x-show="openSidebar">Logout</span>
                    </button>
                </form>
            </li>

        </ul>
    </div>

    <!-- CONTENT -->
    <div class="flex-1 p-6 overflow-y-auto">
        @yield('content')
    </div>

</div>

<script src="https://unpkg.com/alpinejs" defer></script>

</body>
</html>
