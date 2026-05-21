@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <!-- HEADER -->

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">

        <div>
            <h1 class="text-3xl font-bold">

                Buat Site

            </h1>

            <p class="text-gray-500">

                Tambahkan site untuk memulai sistem monitoring aquaponik

            </p>
        </div>

        @include('layouts.user-card', ['subtitle' => 'Create Site'])

    </div>


    <!-- FORM -->

    <div class="bg-white rounded-2xl shadow p-8">

        <form
            action="{{ route('sites.store') }}"
            method="POST"
            class="space-y-6">

            @csrf


            <!-- Nama -->

            <div>

                <label class="block mb-2 font-medium">

                    Nama Site

                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border rounded-xl p-3"
                    placeholder="Kolam Lele A">

                @error('name')

                <p class="text-red-500 text-sm mt-1">

                    {{ $message }}

                </p>

                @enderror

            </div>


            <!-- Lokasi -->

            <div>

                <label class="block mb-2 font-medium">

                    Lokasi

                </label>

                <input
                    type="text"
                    name="location"
                    value="{{ old('location') }}"
                    class="w-full border rounded-xl p-3"
                    placeholder="Wonosobo">

            </div>


            <!-- MAC -->

            <div>

                <label class="block mb-2 font-medium">

                    MAC Address (Opsional)

                </label>

                <input
                    type="text"
                    name="mac_address"
                    value="{{ old('mac_address') }}"
                    class="w-full border rounded-xl p-3"
                    placeholder="AA:BB:CC:DD">

            </div>


            <!-- Deskripsi -->

            <div>

                <label class="block mb-2 font-medium">

                    Deskripsi

                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="w-full border rounded-xl p-3"
                    placeholder="Deskripsi site...">{{ old('description') }}</textarea>

            </div>


            <!-- FOOTER -->

            <div class="flex justify-end gap-3 pt-4 border-t">

                <a
                    href="{{ route('sites.index') }}"
                    class="bg-gray-100 px-6 py-3 rounded-xl">

                    Batal

                </a>

                <button
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl">

                    Simpan Site

                </button>

            </div>

        </form>

    </div>

</div>

@endsection