<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b pb-2">
<h1 class="text-3xl font-extrabold text-gray-900 mb-2 sm:mb-0">
Daftar Grup Arisan Anda
</h1>

</div>

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

<!-- --- Bagian Pencarian dan Filter Baru --- -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    
    <!-- Search Input -->
    <div class="relative flex-1 w-full md:max-w-md">
        <label for="search" class="sr-only">Cari Grup</label>
        <input type="text" name="search" id="search" placeholder="Cari berdasarkan nama grup..."
            wire:model.live.debounce.300ms="search" 
            class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm shadow-sm transition duration-150"
        >
        <!-- Search Icon (Lucide icon) -->
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="border-b border-gray-200 w-full md:w-auto">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            @php
                // Asumsi: $filterStatus adalah properti Livewire, default 'all'
                $currentStatus = $filterStatus ?? 'all';
            @endphp
            
            
        </nav>
    </div>
</div>
<!-- --- Akhir Bagian Pencarian dan Filter Baru --- -->


<div class="space-y-4">
    @forelse ($groups as $group)
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
            <div class="p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div class="flex-1 min-w-0 mb-4 sm:mb-0">
                    <h2 class="text-xl font-bold text-gray-900 truncate flex items-center">

                        <a href="{{ route('groups.show', $group->id) }}" wire:navigate
                            class="hover:text-indigo-600 transition duration-150 ease-in-out">
                            {{ $group->name }}
                        </a>

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
                        <p class="text-xs text-gray-400">
                            Kuota: {{ $group->quota }} Anggota
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3 mt-4 sm:mt-0">
                    
                    
                    <a href="{{ route('groups.anggota.show_anggota', $group->id) }}" wire:navigate
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-md text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150"
                        title="Lihat Detail Grup">
                        Detail
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-10 bg-gray-50 rounded-xl shadow-inner border border-dashed border-gray-300">
            <svg class="mx-auto h-16 w-16 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.75V20.25a.75.75 0 00.75.75H21a.75.75 0 00.75-.75v-1.5m-3.75 0l-.003-.001a.75.75 0 00-.75.75v1.5m-.003-.001H18m0 0V3.75a3 3 0 00-3-3H9a3 3 0 00-3 3v15.75m12 0H6m12 0a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75m12 0h-12m12 0a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75m12 0H6m12 0a.75.75 0 01-.75.75H6.75a.75.75 0 01-.75-.75M6 18.75V3.75m0 0h12m0 0a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75M6 3.75v15.75m0 0H6m0 0a.75.75 0 01.75.75h10.5a.75.75 0 01.75-.75m-12 0a.75.75 0 00-.75.75h-2.5a.75.75 0 00-.75-.75m0 0a.75.75 0 01.75-.75h2.5a.75.75 0 01.75.75m0 0a.75.75 0 00-.75-.75h-2.5a.75.75 0 00-.75.75m0 0a.75.75 0 01.75.75h10.5a.75.75 0 01.75-.75" />
        </svg>
            <p class="text-lg font-medium text-gray-600 mt-3">Anda belum memiliki atau mengikuti grup arisan.</p>
            <p class="text-sm text-gray-400 mt-1">Buat grup baru atau minta undangan dari teman Anda!</p>
            <!-- Removed duplicate "Buat Grup Baru" button here, as it's now at the top -->
        </div>
    @endforelse
</div>


<div class="mt-8">
    {{ $groups->links() }}
</div>


@if ($is_deleting)
    <div x-data="{}" x-init="$el.querySelector('button.focus-initial')?.focus()"
        class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 transition-opacity duration-300 flex items-center justify-center p-4">

        <div class="bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all sm:max-w-lg w-full"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            wire:click.away="$set('is_deleting', false)"> <div class="px-6 pt-6 pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
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
            
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
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


</div>