<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

    <!-- Kontainer Flex untuk Judul dan Tombol -->
    <div class="flex items-center justify-between mb-6 border-b pb-2">
        <!-- Judul -->
        <h1 class="text-3xl font-extrabold text-gray-900 flex-1 min-w-0 pr-4">
            Daftar Grup Arisan Anda (Bendahara)
        </h1>
        
        <!-- Tombol "Buat Grup" Ditempatkan di Samping Judul -->
        <a href="{{ route('groups.bendahara.create') }}" wire:navigate
            class="flex-shrink-0 inline-flex items-center px-4 py-2 border border-transparent shadow-md text-sm font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 whitespace-nowrap"
            title="Buat Grup Arisan Baru">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-1"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Buat Grup
        </a>
    </div>

    <!-- --- Notifikasi Status (Success/Error) --- -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 text-sm text-green-800 bg-green-100 rounded-lg shadow-md border border-green-200" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 text-sm text-red-800 bg-red-100 rounded-lg shadow-md border border-red-200" role="alert">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- --- Search Input --- -->
    <div class="mb-8">
        <label for="search" class="sr-only">Cari Grup</label>
        <div class="p-2 relative rounded-xl shadow-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                name="search" 
                id="search" 
                class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-4 py-2 sm:text-sm border-gray-300 rounded-xl transition duration-150" 
                placeholder="Cari nama grup arisan yang Anda kelola..."
            >
        </div>
    </div>

    <!-- --- Group List --- -->
    <div class="space-y-4">
        @forelse ($groups as $group)
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
                <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="flex-1 min-w-0 mb-4 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-900 truncate flex items-center">
                            <!-- Link Detail Grup -->
                            <a href="{{ route('groups.bendahara.show', $group->id) }}" wire:navigate
                                class="hover:text-indigo-600 transition duration-150 ease-in-out">
                                {{ $group->name }}
                            </a>
                            
                            <!-- Status Badge -->
                            @if ($group->status === 'active')
                                <span class="ml-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                            @elseif ($group->status === 'completed')
                                <span class="ml-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">Selesai</span>
                            @else
                                <span class="ml-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu</span>
                            @endif
                        </h2>
                        
                        <!-- Detail Keuangan -->
                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            <p>
                                <span class="font-medium text-indigo-600">Pot:</span> 
                                <span class="font-bold text-indigo-800">Rp {{ number_format($group->group_pot, 0, ',', '.') }}</span>
                            </p>
                            <p>
                                <span class="font-medium text-gray-500">Iuran:</span> 
                                <span class="font-semibold text-gray-700">Rp {{ number_format($group->fee_per_member, 0, ',', '.') }}</span> / {{ $group->payment_frequency }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Kuota: {{ $group->quota }} Anggota
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Tombol Edit (Hanya untuk Bendahara) -->
                        <a href="{{ route('groups.bendahara.edit', $group->id) }}" wire:navigate
                            class="inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
                            title="Edit Grup">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pen-line"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        </a>
                        
                        <!-- Tombol Hapus Grup (Trigger Modal) -->
                        <button 
                            wire:click="confirmGroupDeletion({{ $group->id }})"
                            class="inline-flex items-center p-2 border border-red-300 shadow-sm text-sm font-medium rounded-lg text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150"
                            title="Hapus Grup">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                        
                        <!-- Tombol Detail (Untuk semua) -->
                        <a href="{{ route('groups.bendahara.show', $group->id) }}" wire:navigate
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
                            title="Lihat Detail Grup">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <!-- State Kosong / Tidak Ada Hasil Pencarian -->
            <div class="text-center py-10 bg-gray-50 rounded-xl shadow-inner border border-dashed border-gray-300">
                <svg class="mx-auto" width="200" height="150" viewBox="0 0 200 150" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG Uang Kertas & Koin -->
                <rect x="10" y="50" width="180" height="80" rx="10" fill="#a7f3d0" stroke="#059669" stroke-width="2"/>
                <text x="30" y="75" font-family="Inter" font-size="18" font-weight="bold" fill="#065f46">$100</text>
                <rect x="150" y="90" width="20" height="20" rx="4" fill="#10b981"/>
                <rect x="20" y="30" width="160" height="70" rx="8" fill="#d1fae5" stroke="#10b981" stroke-width="2"/>
                <text x="40" y="55" font-family="Inter" font-size="16" font-weight="bold" fill="#059669">IDR</text>
                <circle cx="140" cy="65" r="7" fill="#065f46"/>
                <rect x="30" y="10" width="140" height="60" rx="6" fill="#f0fff4" stroke="#34d399" stroke-width="2"/>
                <text x="50" y="35" font-family="Inter" font-size="14" font-weight="bold" fill="#059669">Rp</text>
                <path d="M110 30L125 30L125 45L110 45Z" fill="#34d399"/>
                <circle cx="150" cy="115" r="15" fill="#fcd34d" stroke="#f59e0b" stroke-width="3"/>
                <circle cx="145" cy="100" r="15" fill="#fde68a" stroke="#fbbf24" stroke-width="3"/>
                <circle cx="140" cy="85" r="15" fill="#fef08a" stroke="#f59e0b" stroke-width="3"/>
                <text x="135" y="90" font-family="Inter" font-size="16" font-weight="700" fill="#b45309">€</text>
                </svg>
                    @if (!empty($search))
                        <p class="text-lg font-medium text-gray-600 mt-3">Tidak ada grup Bendahara yang cocok dengan "{{ $search }}".</p>
                        <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau kosongkan pencarian Anda.</p>
                    @else
                        <p class="text-lg font-medium text-gray-600 mt-3">Anda belum memiliki grup arisan (Bendahara) yang dikelola.</p>
                        <p class="text-sm text-gray-400 mt-1">Buat grup baru untuk mulai mengelola keuangan!</p>
                    @endif
                <a href="{{ route('groups.bendahara.create') }}" wire:navigate
                    class="mt-5 inline-flex items-center px-5 py-2.5 border border-transparent shadow-lg text-sm font-medium rounded-full text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Buat Grup Baru
                </a>
            </div>
        @endforelse
    </div>

    <!-- --- Pagination --- -->
    <div class="mt-8">
        {{ $groups->links() }}
    </div>


    <!-- --- MODAL KONFIRMASI HAPUS GRUP --- -->
    @if ($is_deleting)
        <!-- Menggunakan Alpine.js untuk transisi dan fokus awal -->
        <div x-data="{}" x-init="$el.querySelector('button.focus-initial')?.focus()"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 transition-opacity duration-300 flex items-center justify-center p-4">

            <!-- Modal Content Container -->
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all sm:max-w-lg w-full"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="px-6 pt-6 pb-4">
                    <div class="sm:flex sm:items-start">
                        <!-- Icon -->
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <!-- Content -->
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Konfirmasi Penghapusan Grup
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Anda yakin ingin menghapus grup ini? Tindakan ini **tidak dapat dibatalkan**. 
                                    Semua data anggota akan dihapus secara permanen, dan semua anggota akan dikeluarkan dari grup ini.
                                </p>
                                <p class="text-sm font-semibold text-red-600 mt-2">
                                    Ini akan menghapus seluruh catatan grup.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Buttons -->
                <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                    <!-- Tombol Hapus Utama -->
                    <button 
                        type="button" 
                        wire:click.prevent="deleteGroup" 
                        wire:loading.attr="disabled"
                        wire:target="deleteGroup"
                        class="focus-initial w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150"
                    >
                        <span wire:loading wire:target="deleteGroup">Menghapus...</span>
                        <span wire:loading.remove wire:target="deleteGroup">Hapus Grup Secara Permanen</span>
                    </button>
                    
                    <!-- Tombol Batal -->
                    <button 
                        type="button" 
                        wire:click="$set('is_deleting', false)" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
    <!-- --- AKHIR MODAL --- -->
</div>