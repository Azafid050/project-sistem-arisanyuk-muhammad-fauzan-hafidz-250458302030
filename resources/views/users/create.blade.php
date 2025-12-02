<div class="py-12">
    {{-- Mengurangi max-width agar formulir lebih terpusat --}}
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        {{-- Card dengan shadow-2xl dan rounded-xl yang konsisten --}}
        <div class="bg-white overflow-hidden shadow-2xl sm:rounded-xl p-6 sm:p-10 border border-indigo-100">
            
            {{-- JUDUL LEBIH MENONJOL dan konsisten dengan index --}}
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-8 border-b-2 border-indigo-200 pb-4">Tambah Pengguna Baru</h2>

            {{-- wire:submit.prevent adalah praktik terbaik untuk mencegah reload --}}
            <form wire:submit.prevent="store" class="space-y-6">

                {{-- Input Group: Nama --}}
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-gray-700" for="name">Nama Lengkap</label>
                    <input type="text" id="name" wire:model.defer="name" placeholder="Masukkan nama lengkap" 
                        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-3 transition duration-200">
                    @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Input Group: Email --}}
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-gray-700" for="email">Email</label>
                    <input type="email" id="email" wire:model.defer="email" placeholder="contoh@domain.com" autocomplete="off"
                        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-3 transition duration-200">
                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Input Group: NOMOR TELEPON --}}
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-gray-700" for="phone">No. Telepon (Opsional)</label>
                    <input type="tel" id="phone" wire:model.defer="phone" placeholder="Cth: 081234567890 (tanpa spasi/tanda hubung)" 
                        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-3 transition duration-200">
                    @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Input Group: Password --}}
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-gray-700" for="password">Password</label>
                    <input type="password" id="password" wire:model.defer="password" placeholder="Minimal 8 Karakter" autocomplete="new-password"
                        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-3 transition duration-200">
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Input Group: Role --}}
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-gray-700" for="role">Role</label>
                    <select id="role" wire:model.defer="role" 
                        class="mt-1 block w-full border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-2 focus:ring-indigo-200 p-3 transition duration-200 appearance-none">
                        <option value="" disabled selected>Pilih Role Pengguna</option>
                        <option value="anggota">Anggota</option>
                        <option value="bendahara">Bendahara</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end pt-6 space-x-3 border-t border-gray-100">
                    {{-- Tombol Batal: Menggunakan warna abu-abu cerah --}}
                    <a href="{{ route('users.index') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 px-6 rounded-xl transition duration-150 shadow-md transform hover:scale-[1.01]">
                       Batal
                    </a>
                    {{-- Tombol Simpan lebih vibrant dengan efek scale --}}
                    <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-500/50 transition duration-300 transform hover:scale-[1.02] hover:shadow-xl">
                        Simpan Pengguna
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>