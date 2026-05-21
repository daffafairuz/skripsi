<div
x-show="openCreate"
x-transition
class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
style="display:none">

<div
class="bg-white rounded-2xl shadow w-full max-w-xl p-6 max-h-[90vh] overflow-y-auto">

<h2 class="text-xl font-bold mb-6">

Tambah Site

</h2>

<form
action="{{ route('sites.store') }}"
method="POST">

@csrf

<div class="space-y-4">

<div>

<label>User</label>

<select
name="user_id"
class="w-full border rounded-xl p-3">

@foreach($users as $user)

<option value="{{ $user->id }}">

{{ $user->name }}

</option>

@endforeach

</select>

</div>


<div>

<label>Nama Site</label>

<input
type="text"
name="name"
class="w-full border rounded-xl p-3">

</div>


<div>

<label>Lokasi</label>

<input
type="text"
name="location"
class="w-full border rounded-xl p-3">

</div>


<div>

<label>Deskripsi</label>

<textarea
name="description"
rows="4"
class="w-full border rounded-xl p-3"></textarea>

</div>

</div>


<div class="flex justify-end gap-3 mt-6">

<button
type="button"
@click="openCreate=false"
class="px-5 py-2 rounded-xl bg-gray-100">

Batal

</button>

<button
class="bg-green-500 text-white px-5 py-2 rounded-xl">

Simpan

</button>

</div>

</form>

</div>

</div>