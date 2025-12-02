<div class="py-12">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
{{-- Card dengan shadow-2xl dan rounded-xl yang konsisten dengan halaman index dan create --}}
<div class="bg-white overflow-hidden shadow-2xl sm:rounded-xl p-8">
<h2 class="text-3xl font-extrabold text-gray-900 tracking-tight border-b-2 border-indigo-100 pb-4">Edit Pengguna : {{ $name }}</h2>

<form wire:submit.prevent="update" class="space-y-6">

{{-- Input Group: Nama --}}
<div class="space-y-1">
    <label class="block text-sm font-semibold text-gray-700" for="name">Nama</label>
    {{-- Input dengan style yang konsisten --}}
    <input type="text" id="name" wire:model.defer="name" placeholder="Nama Lengkap"
        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-2.5 transition duration-200">
    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
</div>

{{-- Input Group: Email --}}
<div class="space-y-1">
    <label class="block text-sm font-semibold text-gray-700" for="email">Email</label>
    <input type="email" id="email" wire:model.defer="email" placeholder="contoh@domain.com"
        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-2.5 transition duration-200">
    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
</div>

{{-- Input Group: NOMOR TELEPON --}}
<div class="space-y-1">
    <label class="block text-sm font-semibold text-gray-700" for="phone">No. Telepon</label>
    <input type="tel" id="phone" wire:model.defer="phone" placeholder="Cth: 081234567890"
        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-2.5 transition duration-200">
    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
</div>

{{-- Input Group: Password --}}
<div class="space-y-1">
    <label class="block text-sm font-semibold text-gray-700" for="password">Password (Kosongkan jika tidak diubah)</label>
    <input type="password" id="password" wire:model.defer="password" 
        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-2.5 transition duration-200">
    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
</div>

{{-- Input Group: Role --}}
<div class="space-y-1">
    <label class="block text-sm font-semibold text-gray-700" for="role">Role</label>
    <select id="role" wire:model.defer="role" 
        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-2.5 transition duration-200">
        <option value="anggota">Anggota</option>
        <option value="bendahara">Bendahara</option>
        {{-- Tambahkan opsi Admin HANYA JIKA pengguna yang diedit saat ini adalah Admin, agar validasi tidak gagal --}}
        @if ($user->role === 'admin')
            <option value="admin">Admin</option>
        @endif
    </select>
    @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
</div>

<div class="flex justify-end pt-6 space-x-3">
    {{-- Tombol Batal: Menggunakan warna abu-abu cerah yang lebih netral dan modern --}}
    <a href="{{ route('users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-xl transition duration-150 shadow-md">Batal</a>
    {{-- Tombol Simpan: Menggunakan indigo yang lebih dalam dan efek shadow yang lebih jelas --}}
    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-500/50 transition duration-300 transform hover:scale-[1.02] hover:shadow-xl">Simpan</button>
</div>


</form>

</div>

</div>
</div>