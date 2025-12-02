<div class="py-6 sm:py-12">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Shadow card lebih menonjol dan padding disesuaikan --}}
        <div class="bg-white overflow-hidden shadow-2xl sm:rounded-xl p-4 sm:p-8"> 
            
            {{-- JUDUL LEBIH MENONJOL --}}
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-8 border-b-2 border-indigo-200 pb-4">Kelola Semua Pengguna</h2>

            {{-- Pesan Notifikasi (Sudah Responsif) --}}
            @if (session()->has('message'))
                <div class="flex items-center bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <p>{{ session('message') }}</p>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="flex items-center bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Aksi: Pencarian dan Tombol (sudah responsif) --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama atau Email..." 
                    class="w-full md:w-1/3 border-gray-300 rounded-xl shadow-inner focus:border-indigo-600 focus:ring-indigo-600 p-2.5 transition duration-150">
                
                <a href="{{ route('users.create') }}" 
                    class="w-full md:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-indigo-500/50 transform hover:scale-[1.02] transition duration-300">
                    + Tambah Pengguna
                </a>
            </div>

            {{-- ---------------------------------------------------------------------- --}}
            {{-- 1. TABEL VIEW (Hanya Tampil di Desktop/Tablet ke Atas) --}}
            {{-- ---------------------------------------------------------------------- --}}
            <div class="hidden md:block overflow-x-auto border border-indigo-200 rounded-xl shadow-lg">
                <table class="min-w-full divide-y divide-indigo-200">
                    <thead class="bg-indigo-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">No. Phone</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-indigo-800 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-800 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($users as $user)
                            {{-- Warna zebra stripe dan hover yang lebih kental --}}
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-indigo-100 transition duration-150 even:bg-indigo-50"> 
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->phone ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- ROLE TAG LOGIC IN-LINE (Tambahkan ring/border) --}}
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full ring-1 
                                        @if ($user->role == 'admin') text-red-700 bg-red-100 ring-red-300
                                        @elseif ($user->role == 'bendahara') text-blue-700 bg-blue-100 ring-blue-300
                                        @else text-green-700 bg-green-100 ring-green-300
                                        @endif
                                    ">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                    {{-- ACTION BUTTONS LOGIC IN-LINE --}}
                                    <a href="{{ route('users.edit', $user->id) }}" 
                                        class="text-indigo-600 hover:text-white transition duration-200 inline-flex items-center p-2 rounded-full hover:bg-indigo-500 shadow-md hover:shadow-lg" 
                                        title="Edit Pengguna">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                    </a>
                                    
                                    @if (auth()->id() !== $user->id) 
                                        <button 
                                            wire:click="delete({{ $user->id }})" 
                                            onclick="confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?') || event.stopImmediatePropagation()" 
                                            class="text-red-600 hover:text-white transition duration-200 inline-flex items-center p-2 rounded-full hover:bg-red-500 shadow-md hover:shadow-lg" 
                                            title="Hapus Pengguna"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-lg text-gray-500 bg-gray-50">Tidak ada data pengguna yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ---------------------------------------------------------------------- --}}
            {{-- 2. LIST VIEW / CARD VIEW (Hanya Tampil di HP/Mobile) --}}
            {{-- ---------------------------------------------------------------------- --}}
            <div class="md:hidden space-y-4">
                @forelse ($users as $user)
                    <div wire:key="user-mobile-{{ $user->id }}" class="bg-white p-4 border border-indigo-200 rounded-xl shadow-lg space-y-3">
                        
                        {{-- Baris 1: Nama & Role --}}
                        <div class="flex justify-between items-center border-b pb-2">
                            <div class="text-lg font-bold text-gray-900">{{ $user->name }}</div>
                            {{-- Logika Tag Role In-line (Diulang - Tambahkan ring/border) --}}
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full ring-1 
                                @if ($user->role == 'admin') text-red-700 bg-red-100 ring-red-300
                                @elseif ($user->role == 'bendahara') text-blue-700 bg-blue-100 ring-blue-300
                                @else text-green-700 bg-green-100 ring-green-300
                                @endif
                            ">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        {{-- Baris 2: Detail Email & Phone --}}
                        <div class="space-y-1 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span class="font-medium text-indigo-600">Email:</span>
                                <span>{{ $user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-indigo-600">Phone:</span>
                                <span>{{ $user->phone ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Baris 3: Aksi --}}
                        <div class="pt-2 border-t flex justify-end space-x-2">
                            {{-- Logika Aksi In-line (Diulang) --}}
                            <a href="{{ route('users.edit', $user->id) }}" 
                                class="text-indigo-600 hover:text-white transition duration-200 inline-flex items-center p-2 rounded-full hover:bg-indigo-500 shadow-md hover:shadow-lg" 
                                title="Edit Pengguna">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </a>
                            
                            @if (auth()->id() !== $user->id) 
                                <button 
                                    wire:click="delete({{ $user->id }})" 
                                    onclick="confirm('Apakah Anda yakin ingin menghapus pengguna {{ $user->name }}?') || event.stopImmediatePropagation()" 
                                    class="text-red-600 hover:text-white transition duration-200 inline-flex items-center p-2 rounded-full hover:bg-red-500 shadow-md hover:shadow-lg" 
                                    title="Hapus Pengguna"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-lg text-gray-500 bg-gray-50 rounded-xl">Tidak ada data pengguna yang ditemukan.</div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>