@extends('layouts.app')

@section('content')

<div x-data="{

    openCreate: false,
    openEdit: false,

    editUser: {
        id: '',
        name: '',
        email: '',
        phone_number: '',
        role: '',
        status: ''
    }

}"

class="relative">

<!-- HEADER -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

    <div>
        <h1 class="text-2xl font-bold">
            Pengguna
        </h1>

        <p class="text-sm text-gray-500">
            Kelola akun pengguna sistem
        </p>
    </div>

    <div class="flex items-center gap-3">
        <!-- BUTTON -->
        <button
            @click="openCreate = true"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl transition">

            + Tambah User

        </button>

        @include('layouts.user-card', ['subtitle' => 'User Management'])
    </div>

</div>

<!-- TABLE -->
<div class="bg-white rounded-2xl shadow overflow-x-auto">

    <table class="w-full">

        <thead class="bg-gray-100">

            <tr>

                <th class="p-4 text-left">Nama</th>
                <th class="p-4 text-left">Email</th>
                <th class="p-4 text-left">Role</th>
                <th class="p-4 text-left">Status</th>
                <th class="p-4 text-left">Aksi</th>

            </tr>

        </thead>

        <tbody>

            @foreach($users as $user)

            <tr class="border-t hover:bg-gray-50">

                <td class="p-4">
                    {{ $user->name }}
                </td>

                <td class="p-4">
                    {{ $user->email }}
                </td>

                <td class="p-4">

                    @if($user->role == 'admin')

                    <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm">
                        Admin
                    </span>

                    @else

                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                        User
                    </span>

                    @endif

                </td>

                <td class="p-4">

                    @if($user->status == 'active')

                    <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                        Aktif
                    </span>

                    @else

                    <span class="bg-gray-200 text-gray-600 px-3 py-1 rounded-full text-sm">
                        Nonaktif
                    </span>

                    @endif

                </td>

                <!-- ACTION -->
                <td class="p-4">

                    <div class="flex gap-2">

                        <!-- EDIT -->
                        <button

                            @click="
                                openEdit = true;

                                editUser.id = '{{ $user->id }}';
                                editUser.name = '{{ $user->name }}';
                                editUser.email = '{{ $user->email }}';
                                editUser.phone_number = '{{ $user->phone_number }}';
                                editUser.role = '{{ $user->role }}';
                                editUser.status = '{{ $user->status }}';
                            "

                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-sm">

                            Edit

                        </button>

                        <!-- DELETE -->
                        <form action="/users/{{ $user->id }}"
                              method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                            @csrf
                            @method('DELETE')

                            <button class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-sm">

                                Hapus

                            </button>

                        </form>

                    </div>

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

<!-- ================= CREATE MODAL ================= -->
<div
    x-show="openCreate"
    x-transition
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between mb-6">

            <h2 class="text-xl font-bold">
                Tambah User
            </h2>

            <button @click="openCreate = false">
                ✕
            </button>

        </div>

        <form action="/users" method="POST" class="space-y-4">

            @csrf

            <input type="text"
                   name="name"
                   placeholder="Nama"
                   class="w-full border rounded-xl p-3">

            <input type="email"
                   name="email"
                   placeholder="Email"
                   class="w-full border rounded-xl p-3">

            <input type="text"
                   name="phone_number"
                   placeholder="Nomor HP"
                   class="w-full border rounded-xl p-3">

            <select name="role"
                    class="w-full border rounded-xl p-3">

                <option value="user">
                    User
                </option>

                <option value="admin">
                    Admin
                </option>

            </select>

            <input type="password"
                   name="password"
                   placeholder="Password"
                   class="w-full border rounded-xl p-3">

            <div class="flex justify-end gap-3 pt-4">

                <button type="button"
                        @click="openCreate = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">

                    Batal

                </button>

                <button class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl transition">

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

<!-- ================= EDIT MODAL ================= -->
<div
    x-show="openEdit"
    x-transition
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-2xl p-6 w-full max-w-xl max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between mb-6">

            <h2 class="text-xl font-bold">
                Edit User
            </h2>

            <button @click="openEdit = false">
                ✕
            </button>

        </div>

        <form
            :action="'/users/' + editUser.id"
            method="POST"
            class="space-y-4">

            @csrf
            @method('PUT')

            <input type="text"
                   name="name"
                   x-model="editUser.name"
                   class="w-full border rounded-xl p-3">

            <input type="email"
                   name="email"
                   x-model="editUser.email"
                   class="w-full border rounded-xl p-3">

            <input type="text"
                   name="phone_number"
                   x-model="editUser.phone_number"
                   class="w-full border rounded-xl p-3">

            <select name="role"
                    x-model="editUser.role"
                    class="w-full border rounded-xl p-3">

                <option value="user">User</option>
                <option value="admin">Admin</option>

            </select>

            <select name="status"
                    x-model="editUser.status"
                    class="w-full border rounded-xl p-3">

                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>

            </select>

            <div class="flex justify-end gap-3 pt-4">

                <button type="button"
                        @click="openEdit = false"
                        class="px-4 py-2 rounded-xl bg-gray-100">

                    Batal

                </button>

                <button class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-xl transition">

                    Update

                </button>

            </div>

        </form>

    </div>

</div>

</div>

@endsection