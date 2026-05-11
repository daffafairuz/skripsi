@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow p-6">

    <h1 class="text-2xl font-bold mb-6">
        Edit User
    </h1>

    <form action="/users/{{ $user->id }}"
          method="POST"
          class="space-y-5">

        @csrf
        @method('PUT')

        <div>

            <label class="block mb-2 text-sm font-medium">
                Nama
            </label>

            <input type="text"
                   name="name"
                   value="{{ $user->name }}"
                   class="w-full border rounded-xl p-3">

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium">
                Email
            </label>

            <input type="email"
                   name="email"
                   value="{{ $user->email }}"
                   class="w-full border rounded-xl p-3">

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium">
                Nomor HP
            </label>

            <input type="text"
                   name="phone_number"
                   value="{{ $user->phone_number }}"
                   class="w-full border rounded-xl p-3">

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium">
                Role
            </label>

            <select name="role"
                    class="w-full border rounded-xl p-3">

                <option value="user"
                    {{ $user->role == 'user' ? 'selected' : '' }}>
                    User
                </option>

                <option value="admin"
                    {{ $user->role == 'admin' ? 'selected' : '' }}>
                    Admin
                </option>

            </select>

        </div>

        <div>

            <label class="block mb-2 text-sm font-medium">
                Status
            </label>

            <select name="status"
                    class="w-full border rounded-xl p-3">

                <option value="active"
                    {{ $user->status == 'active' ? 'selected' : '' }}>
                    Aktif
                </option>

                <option value="inactive"
                    {{ $user->status == 'inactive' ? 'selected' : '' }}>
                    Nonaktif
                </option>

            </select>

        </div>

        <div class="flex justify-end">

            <button class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition">

                Update

            </button>

        </div>

    </form>

</div>

@endsection