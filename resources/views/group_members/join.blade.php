<div>
<!-- Notifikasi Sukses/Error/Info - Digunakan untuk menampilkan session flash messages dari Livewire -->
@if (session()->has('success'))
<div class="text-sm mb-4 text-green-600 bg-green-100 p-3 rounded-xl transition duration-300 shadow-sm border border-green-200" role="alert">
<span class="font-medium">Berhasil!</span> {{ session('success') }}
</div>
@endif
@if (session()->has('info'))
<div class="text-sm mb-4 text-blue-600 bg-blue-100 p-3 rounded-xl transition duration-300 shadow-sm border border-blue-200" role="alert">
<span class="font-medium">Informasi:</span> {{ session('info') }}
</div>
@endif
@if (session()->has('error'))
<div class="text-sm mb-4 text-red-600 bg-red-100 p-3 rounded-xl transition duration-300 shadow-sm border border-red-200" role="alert">
<span class="font-medium">Gagal!</span> {{ session('error') }}
</div>
@endif

{{-- LOGIKA BARU KRITIS: Tombol Kuota Penuh --}}
@if ($isGroupFull && $memberStatus === 'none')
    <!-- STATUS: GRUP PENUH (User Belum Gabung) -->
    <button disabled
        class="w-full bg-gray-400 text-white font-bold py-3 px-4 rounded-xl shadow-lg cursor-not-allowed">
        <div class="flex items-center justify-center">
            <!-- Icon Kunci/Terkunci -->
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9V5a3 3 0 00-6 0v4"></path></svg>
            Kuota Penuh ({{ $group->quota }} Anggota Disetujui)
        </div>
    </button>
@else 
    {{-- Logika Tombol berdasarkan memberStatus: approved, pending, atau none (jika grup belum penuh) --}}
    @switch($memberStatus)
        @case('approved')
            <!-- STATUS: APPROVED (Anggota Disetujui) -->
            <button wire:click="leaveGroup" wire:confirm="Apakah Anda yakin ingin meninggalkan grup ini? Tindakan ini tidak dapat dibatalkan."
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition duration-150 shadow-lg 
                        focus:outline-none focus:ring-4 focus:ring-red-300 transform hover:scale-[1.01]">
                <div class="flex items-center justify-center">
                    <!-- Icon Pintu Keluar/Keluar -->
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Anda Anggota (Tinggalkan Grup)
                </div>
            </button>
            @break

        @case('pending')
            <!-- STATUS: PENDING (Menunggu Persetujuan) -->
            <button wire:click="leaveGroup" wire:confirm="Apakah Anda yakin ingin membatalkan permintaan gabung ini?"
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-bold py-3 px-4 rounded-xl transition duration-150 shadow-lg 
                        focus:outline-none focus:ring-4 focus:ring-yellow-300 transform hover:scale-[1.01]">
                <div class="flex items-center justify-center">
                    <!-- Icon Jam/Menunggu -->
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Menunggu Persetujuan (Batalkan)
                </div>
            </button>
            @break

        @default
            <!-- STATUS: NONE (Belum Bergabung, dan Grup Belum Penuh) -->
            <button wire:click="joinGroup" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl transition duration-150 shadow-2xl shadow-indigo-500/50 
                        focus:outline-none focus:ring-4 focus:ring-indigo-300 transform hover:scale-[1.03]">
                <div class="flex items-center justify-center">
                    <!-- Icon Tambah/Gabung -->
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Gabung Sekarang
                </div>
            </button>
            @break
    @endswitch
@endif

<!-- Informasi ID Grup (Debugging/Referensi) -->
@if (isset($groupId))
<p class="text-xs text-gray-400 mt-3 text-center">
    ID Grup: <code class="text-gray-500">{{ $groupId }}</code> 
    <span class="ml-2">Status Penuh: <code class="text-gray-500">{{ $isGroupFull ? 'YA' : 'TIDAK' }}</code></span>
</p>
@endif


</div>