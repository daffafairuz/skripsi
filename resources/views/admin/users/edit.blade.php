@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow p-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">
            Edit Site
        </h1>
        @include('layouts.user-card', ['subtitle' => 'Edit Site'])
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sites.update', $site->id) }}"
          method="POST"
          class="space-y-5">

        @csrf
        @method('PUT')

        <!-- NAME -->
        <div>
            <label class="block mb-2 text-sm font-medium">Nama Site</label>
            <input type="text" name="name"
                   value="{{ old('name', $site->name) }}"
                   class="w-full border rounded-xl p-3" required>
        </div>

        <!-- LOCATION -->
        <div>
            <label class="block mb-2 text-sm font-medium">Lokasi</label>
            <input type="text" name="location"
                   value="{{ old('location', $site->location) }}"
                   class="w-full border rounded-xl p-3" required>
        </div>

        <!-- DESCRIPTION -->
        <div>
            <label class="block mb-2 text-sm font-medium">Deskripsi</label>
            <textarea name="description"
                      class="w-full border rounded-xl p-3" rows="3">{{ old('description', $site->description) }}</textarea>
        </div>

        <!-- MAC ADDRESS -->
        <div>
            <label class="block mb-2 text-sm font-medium">MAC Address</label>
            <input type="text" name="mac_address"
                   value="{{ old('mac_address', $site->mac_address) }}"
                   class="w-full border rounded-xl p-3" required>
        </div>

        <!-- USER -->
        <div>
            <label class="block mb-2 text-sm font-medium">Owner (User)</label>
            <select name="user_id" class="w-full border rounded-xl p-3" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}"
                        {{ $site->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- BUTTONS -->
        <div class="flex justify-end gap-3">

            <a href="{{ route('sites.index') }}"
               class="px-6 py-3 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                Batal
            </a>

            <button class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition">
                Update
            </button>

        </div>

    </form>

</div>

@endsection