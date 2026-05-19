@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow p-6">

    <h1 class="text-2xl font-bold mb-6">
        Tambah User
    </h1>

    <form action="/users" method="POST" class="space-y-5">

        @csrf

        <!-- NAME -->
        <div>

            <label class="block mb-2 text-sm font-medium">
                Nama
            </label>

            <input type="text"
                   name="name"
                   class="w-full border rounded-xl p-3">

        </div>

        <!-- EMAIL -->
        <div>

            <label class="block mb-2 text-sm font-medium">
                Email
            </label>

            <input type="email"
                   name="email"
                   class="w-full border rounded-xl p-3">

        </div>

        <!-- PHONE -->
        <div>

            <label class="block mb-2 text-sm font-medium">
                Nomor HP
            </label>

            <input type="text"
                   name="phone_number"
                   class="w-full border rounded-xl p-3">

        </div>

        <!-- ROLE -->
        <div>

            <label class="block mb-2 text-sm font-medium">
                Role
            </label>

            <select name="role"
                    class="w-full border rounded-xl p-3">

                <option value="user">User</option>
                <option value="admin">Admin</option>

            </select>

        </div>

        <!-- PASSWORD -->
        <div>

            <label class="block mb-2 text-sm font-medium">
                Password
            </label>

            <input type="password"
                   name="password"
                   class="w-full border rounded-xl p-3">

        </div>

        <!-- BUTTON -->
        <div class="flex justify-end">

            <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl transition">

                Simpan

            </button>

        </div>

    </form>

</div>

@endsection