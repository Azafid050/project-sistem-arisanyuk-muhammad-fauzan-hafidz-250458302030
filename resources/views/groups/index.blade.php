<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

    {{-- HEADER DAN TOMBOL TAMBAH GRUP --}}
    <div class="flex justify-between items-center mb-6 border-b pb-2">
        <h1 class="text-3xl font-extrabold text-gray-900">Daftar Grup Arisan Anda</h1>
        <a href="{{ route('groups.create') }}" wire:navigate
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-1"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Tambah Grup Baru
        </a>
    </div>

    {{-- PESAN SESI (SUCCESS & ERROR) --}}
    @if (session()->has('success'))
        <div class="mb-6 p-4 text-sm text-green-800 bg-green-100 rounded-lg shadow-md border border-green-200" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 text-sm text-red-800 bg-red-100 rounded-lg shadow-md border border-red-200" role="alert">
            <div class="flex items-center">
                <svg class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-2-4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1zm.293-7.293a1 1 0 011.414 0L12 8.586l2.293-2.293a1 1 0 111.414 1.414L13.414 10l2.293 2.293a1 1 0 01-1.414 1.414L12 11.414l-2.293 2.293a1 1 0 01-1.414-1.414L10.586 10 8.293 7.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- TABS DAN SEARCH BAR --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
        {{-- Tabs Navigation --}}
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a wire:click="setTab('managed')"
                class="cursor-pointer whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                {{ $tab === 'managed' ? 'border-indigo-600 text-indigo-600 font-semibold' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                Grup yang Saya Kelola (Owner & Admin)
            </a>
        </nav>

        {{-- Search Input --}}
        <div class="mt-4 md:mt-0 w-full md:w-64">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Grup..."
                    class="p-3 pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-gray-400 text-sm" />
            </div>
        </div>
    </div>

    <div class="border-b border-gray-200 mb-6"></div>

    {{-- DAFTAR GRUP --}}
    <div class="space-y-4">
        @forelse ($groups as $group)
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
                <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="flex-1 min-w-0 mb-4 sm:mb-0">
                        <h2 class="text-xl font-bold text-gray-900 truncate flex items-center">
                            <a href="{{ route($getGroupShowRoute($group), $group->id) }}" wire:navigate
                                class="hover:text-indigo-600 transition duration-150 ease-in-out">
                                {{ $group->name }}
                            </a>
                            {{-- Status Grup --}}
                            @if ($group->status === 'active')
                                <span class="ml-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                            @elseif ($group->status === 'completed')
                                <span class="ml-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">Selesai</span>
                            @else
                                <span class="ml-2 inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu</span>
                            @endif
                        </h2>
                        <div class="mt-2 text-sm text-gray-600 space-y-1">
                            <p>
                                <span class="font-medium text-indigo-600">Pot:</span>
                                <span class="font-bold text-indigo-800">Rp {{ number_format($group->group_pot, 0, ',', '.') }}</span>
                            </p>
                            <p>
                                <span class="font-medium text-gray-500">Iuran:</span>
                                <span class="font-semibold text-gray-700">Rp {{ number_format($group->fee_per_member, 0, ',', '.') }}</span> / {{ $group->payment_frequency }}
                            </p>
                            <p class="text-xs text-gray-400">Kuota: {{ $group->quota }} Anggota</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                        {{-- Tombol Aksi --}}
                        @if ($canManageGroup($group))
                            <a href="{{ route('groups.edit', $group->id) }}" wire:navigate
                                class="inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
                                title="Edit Grup">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pen-line"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </a>
                            <button wire:click="confirmGroupDeletion({{ $group->id }})"
                                class="inline-flex items-center p-2 border border-red-300 shadow-sm text-sm font-medium rounded-lg text-red-600 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150"
                                title="Hapus Grup">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            </button>
                        @endif
                        <a href="{{ route($getGroupShowRoute($group), $group->id) }}" wire:navigate
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
                            title="Lihat Detail Grup">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-gray-50 rounded-xl shadow-inner border border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM12 9v.008H12.012V9H12zM15 15v.008H15.012V15H15zM9 15v.008H9.012V15H9z" />
                </svg>
                <p class="text-lg font-medium text-gray-600 mt-3">Anda belum memiliki atau mengikuti grup arisan.</p>
                <p class="text-sm text-gray-400 mt-1">Buat grup baru atau minta undangan dari teman Anda!</p>
                <a href="{{ route('groups.create') }}" wire:navigate
                    class="mt-5 inline-flex items-center px-5 py-2.5 border border-transparent shadow-lg text-sm font-medium rounded-full text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus mr-2"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Buat Grup Baru
                </a>
            </div>
        @endforelse
    </div>

    {{-- PAGINASI --}}
    <div class="mt-8">
        {{ $groups->links() }}
    </div>

    {{-- MODAL PENGHAPUSAN GRUP (HANYA MUNCUL JIKA is_deleting = true) --}}
    @if ($is_deleting)
        {{-- Menggunakan x-show untuk transisi yang lebih baik jika Anda menggunakan AlpineJS --}}
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-data="{ open: @entangle('is_deleting') }" x-show="open">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- OVERLAY (z-index 40 agar di bawah modal) --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40" aria-hidden="true" wire:click="cancelDelete"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                {{-- KONTEN MODAL (z-index 50) --}}
                <div class="inline-block relative z-50 align-bottom bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <div class="px-6 pt-6 pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Konfirmasi Penghapusan Grup
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Anda yakin ingin menghapus grup <strong>"{{ $groupToDeleteName }}"</strong>? Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data anggota akan dihapus secara permanen, dan semua anggota akan dikeluarkan dari grup ini.
                                    </p>
                                    <p class="text-sm font-semibold text-red-600 mt-2">
                                        Ini akan menghapus seluruh catatan grup.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click.prevent="deleteGroup" wire:loading.attr="disabled" wire:target="deleteGroup"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            <span wire:loading wire:target="deleteGroup">Menghapus...</span>
                            <span wire:loading.remove wire:target="deleteGroup">Hapus Grup Secara Permanen</span>
                        </button>
                        <button type="button" wire:click="cancelDelete" 
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>