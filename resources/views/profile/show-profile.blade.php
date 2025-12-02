<div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">

    {{-- Notifikasi Umum (Pesan sukses) --}}
    @if (session('profile_status') || $profile_status)
        <div class="mb-8 bg-green-50 border border-green-400 text-green-700 px-6 py-4 rounded-xl shadow-lg transition-all duration-300 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-semibold">{{ session('profile_status') ?? $profile_status }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- SISI KIRI: KARTU RINGKASAN PROFIL --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-indigo-100 sticky top-5 transform transition duration-500 hover:shadow-indigo-300/50">
                <div class="flex flex-col items-center">
                    
                    {{-- Avatar Pengguna (LOGIC BARU) --}}
                    @php
                        // Logika untuk menampilkan foto dari storage atau fallback ke UI Avatar
                        $photoUrl = optional($user)->profile_photo_path 
                            ? \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($name ?? 'User') . '&background=4F46E5&color=fff&size=128&bold=true';
                    @endphp

                    <img src="{{ $photoUrl }}"
                        class="w-28 h-28 rounded-full border-4 border-indigo-500 shadow-xl mb-6 transform hover:scale-105 transition duration-300 object-cover" 
                        alt="User Avatar">
                    
                    <!-- Nama & Peran -->
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2 text-center">{{ $name }}</h2>
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold capitalize tracking-wide 
                                 {{ $role === 'Admin' ? 'bg-red-600 text-white shadow-md' : 
                                     ($role === 'Bendahara' ? 'bg-teal-500 text-white shadow-md' : 'bg-indigo-600 text-white shadow-md') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0h4" />
                        </svg>
                        {{ $role }}
                    </span>
                </div>
                
                <hr class="my-8 border-indigo-100">
                
                <div class="space-y-6">
                    
                    {{-- Detail Email --}}
                    <div class="flex items-start text-base text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0 mr-4 text-indigo-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div class="min-w-0">
                            <span class="font-semibold block">Email</span>
                            <span class="text-gray-600 break-all">{{ $email }}</span>
                        </div>
                    </div>
                    
                    {{-- Detail Telepon --}}
                    <div class="flex items-start text-base text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0 mr-4 text-indigo-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <span class="font-semibold block">Telepon</span>
                            <span class="text-gray-600">{{ $phone ?? 'Belum Diatur' }}</span>
                        </div>
                    </div>

                    {{-- Detail Bergabung --}}
                    <div class="flex items-start text-base text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 flex-shrink-0 mr-4 text-indigo-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <span class="font-semibold block">Bergabung Sejak</span>
                            <span class="text-gray-600">{{ optional($user->created_at)->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SISI KANAN: FORMULIR PROFIL & PASSWORD --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- 1. FORM PEMBARUAN INFORMASI PROFIL --}}
            <div class="bg-white shadow-xl rounded-2xl sm:rounded-lg overflow-hidden border border-indigo-100 p-6 sm:p-8">
                <h3 class="text-3xl font-extrabold text-gray-900 border-b border-indigo-100 pb-4 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Profil Anda
                </h3>
                <p class="mt-1 text-sm text-gray-600 mb-6">
                    Anda dapat memperbarui nama, email, dan nomor telepon yang terkait dengan akun Anda.
                </p>
                
                <form wire:submit.prevent="updateProfileInformation" class="space-y-6">
                    
                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="name" type="text" wire:model.defer="name" 
                            class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 p-3" 
                            required autofocus>
                        @error('name') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <input id="email" type="email" wire:model.defer="email" 
                            class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 p-3" 
                            required>
                        @error('email') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon (Opsional)</label>
                        <input id="phone" type="text" wire:model.defer="phone" 
                            class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 p-3" 
                            placeholder="Contoh: 081234567890">
                        @error('phone') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end pt-3">
                        <span wire:loading.delay wire:target="updateProfileInformation" class="text-sm text-gray-500 mr-4">Sedang menyimpan...</span>
                        
                        <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:scale-[1.01]"
                            wire:loading.attr="disabled">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div> 
            
            {{-- 2. FORM PEMBARUAN FOTO PROFIL (BARU) --}}
            @if (session('photo_status') || $photo_status)
                <div class="bg-green-50 border border-green-400 text-green-700 px-6 py-4 rounded-xl shadow-lg transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold">{{ session('photo_status') ?? $photo_status }}</span>
                </div>
            @endif
            
            <div class="bg-white shadow-xl rounded-2xl sm:rounded-lg overflow-hidden border border-indigo-100 p-6 sm:p-8">
                <h3 class="text-3xl font-extrabold text-gray-900 border-b border-indigo-100 pb-4 mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Perbarui Foto Profil
                </h3>
                <p class="mt-1 text-sm text-gray-600 mb-6">
                    Pilih gambar baru. Pastikan foto memiliki rasio persegi yang baik. Maksimal 2MB.
                </p>

                <form wire:submit.prevent="updateProfilePhoto" class="space-y-6">
                    
                    {{-- Input Foto --}}
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">Pilih Foto Baru</label>
                        <input id="photo" type="file" wire:model="photo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-xl cursor-pointer bg-gray-50 focus:outline-none p-2.5">
                        @error('photo') <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Pratinjau Foto (Livewire Temporary URL) --}}
                    @if ($photo)
                    <div class="mt-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Pratinjau Foto Baru:</p>
                        <img src="{{ $photo->temporaryUrl() }}" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-indigo-400 shadow-xl transition duration-300" 
                             alt="Photo Preview">
                    </div>
                    @endif

                    <div class="flex items-center justify-end pt-3">
                        <span wire:loading.delay wire:target="updateProfilePhoto" class="text-sm text-gray-500 mr-4">Sedang mengunggah...</span>
                        
                        <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-wider hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:scale-[1.01]"
                            wire:loading.attr="disabled">
                            Unggah Foto
                        </button>
                    </div>
                </form>
            </div>    
        </div>
    </div>
</div>