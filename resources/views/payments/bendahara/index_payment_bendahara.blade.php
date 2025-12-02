<div class="p-4 sm:p-6 lg:p-7 max-w-6xl mx-auto">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
    
    <header class="mb-6 sm:mb-8 text-center">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight font-inter">
            💰 Verifikasi Pembayaran Arisan
        </h1>
        <p class="mt-1 text-sm sm:text-md text-gray-500 font-inter">
            Kelola & verifikasi pembayaran.
        </p>
    </header>

    <div class="bg-white shadow-lg sm:shadow-xl rounded-lg p-4 sm:p-6 mb-8 border-t-4 border-indigo-600">
        <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-5 text-gray-800 flex items-center font-inter">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012 2v2M7 7h10" />
            </svg>
            Grup Kelolaan ({{ count($groupsToManage) }})
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5">
            @forelse ($groupsToManage as $groupData)
                @php
                    $isPending = $groupData['pending_payments_count'] > 0;
                    $isActiveRound = $groupData['is_round_active'];
                    $isFullyPaid = $groupData['pending_payments_count'] === 0 && $isActiveRound;
                @endphp
                <button
                    wire:click="selectGroup({{ $groupData['id'] }})"
                    class="block text-left p-3 sm:p-4 rounded-lg transition duration-300 transform hover:scale-[1.01] border-2 shadow-sm font-inter
                        @if($groupData['id'] === $selectedGroupId)
                            border-indigo-700 bg-indigo-100 ring-2 ring-indigo-300 shadow-lg
                        @elseif($isPending)
                            border-red-600 bg-red-50 hover:shadow-md
                        @elseif($isFullyPaid)
                            border-green-600 bg-green-50 hover:shadow-md
                        @else
                            border-gray-300 bg-gray-50 hover:shadow-md
                        @endif">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-base sm:text-lg font-bold
                            @if($groupData['id'] === $selectedGroupId) text-indigo-800
                            @elseif($isPending) text-red-800
                            @elseif($isFullyPaid) text-green-800
                            @else text-gray-900 @endif">
                            {{ $groupData['name'] }}
                        </p>
                        @if ($groupData['id'] === $selectedGroupId)
                            <span class="text-xs font-semibold px-2 py-0.5 bg-indigo-600 text-white rounded-full">AKTIF</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-600">
                        Putaran:
                        <span class="font-semibold text-gray-800">#{{ $groupData['active_round_number'] }}</span>
                    </p>
                    @if ($isPending)
                        <div class="mt-2 p-2 bg-red-100 border border-red-400 rounded-md flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.487 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 11a1 1 0 100 2 1 1 0 000-2zm0-4a1 1 0 000 2h.01a1 1 0 000-2H10z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-bold">{{ $groupData['pending_payments_count'] }} Bayaran Menunggu Verifikasi</span>
                        </div>
                    @elseif (!$isActiveRound)
                        <div class="mt-2 p-2 bg-gray-200 rounded-md flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-xs font-medium">Putaran TIDAK Aktif</span>
                        </div>
                    @elseif ($isActiveRound && !$isFullyPaid)
                        <div class="mt-2 p-2 bg-yellow-100 border border-yellow-400 rounded-md flex items-center text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-7-9a1 1 0 011-1h1a1 1 0 110 2H4a1 1 0 01-1-1zM9 11a1 1 0 112 0v2a1 1 0 11-2 0v-2z" clip-rule="evenodd" /></svg>
                            <span class="text-xs font-bold">Pembayaran Sedang Berjalan (Belum Lunas)</span>
                        </div>
                    @endif
                </button>
            @empty
                <p class="text-gray-500 col-span-full p-4 bg-gray-50 rounded-lg border border-gray-200 text-sm font-inter">Anda tidak mengelola grup aktif manapun sebagai bendahara.</p>
            @endforelse
        </div>
    </div>

    @if ($group)
        <div class="mt-4 sm:mt-8 p-4 sm:p-7 bg-gray-50 rounded-lg shadow-xl">
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-2 font-inter">
                Detail Pembayaran: {{ $group->name }}
            </h2>
            <p class="text-sm sm:text-md text-gray-600 mb-4 sm:mb-5 font-inter">
                Putaran #{{ $activeRound->round_number ?? 'N/A' }} | Iuran: <span class="font-bold text-gray-800">Rp {{ number_format($group->fee_per_member, 0, ',', '.') }}</span>
            </p>

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-l-4 sm:border-l-6 @if($isReadyToDraw) border-green-600 @else border-yellow-600 @endif rounded-lg mb-4 p-4 font-inter">
                <div class="flex-1 mb-3 sm:mb-0">
                    <p class="text-xs sm:text-sm font-semibold text-gray-500">Status Pembayaran Round Ini</p>
                    <h3 class="flex items-center mt-1 text-xl sm:text-2xl font-black">
                        @if($isReadyToDraw)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-3 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            <span class="text-green-700">{{ $verifiedPaymentsCount }} / {{ $totalQuotaMembers }} LUNAS!</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-3 text-yellow-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.487 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 11a1 1 0 100 2 1 1 0 000-2zm0-4a1 1 0 000 2h.01a1 1 0 000-2H10z" clip-rule="evenodd" /></svg>
                            <span class="text-yellow-700">{{ $verifiedPaymentsCount }} / {{ $totalQuotaMembers }} Anggota Lunas</span>
                        @endif
                    </h3>
                </div>
                <div class="mt-2 sm:mt-0">
                    @if ($isReadyToDraw)
                        <a href="{{ route('rounds.round_roulette', $group->id) }}" class="w-full sm:w-auto py-2 px-4 bg-green-600 hover:bg-green-700 text-white rounded-full flex items-center justify-center text-sm shadow transition duration-150 font-inter">
                            <svg class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                            Mulai Undian
                        </a>
                    @else
                        <button disabled class="w-full sm:w-auto py-2 px-4 bg-gray-400 text-white rounded-full cursor-not-allowed shadow transition duration-150 text-sm font-inter">
                            Tunggu {{ $totalQuotaMembers - $verifiedPaymentsCount }} Anggota Lagi
                        </button>
                    @endif
                </div>
            </div>

            {{-- List View Pembayaran Responsif (Pengganti Tabel) --}}
            <div class="space-y-4 font-inter">
                @forelse ($payments as $payment)
                {{-- Kartu Pembayaran Tunggal --}}
                <div class="bg-white p-4 rounded-lg shadow-md border-l-4 
                    @if($payment->status === 'verified') border-green-600 
                    @elseif($payment->status === 'pending') border-yellow-600 
                    @else border-red-600 @endif">

                    {{-- BARIS 1: Anggota & Status --}}
                    <div class="flex justify-between items-start border-b pb-2 mb-2">
                        <div>
                            <p class="text-lg font-extrabold text-gray-900">{{ $payment->groupMember?->user?->name ?? 'Anggota Dihapus' }}</p>
                            <p class="text-xs text-indigo-600 font-semibold">({{ $payment->groupMember?->role ?? 'N/A' }})</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                            @if($payment->status === 'verified') bg-green-100 text-green-800
                            @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>

                    {{-- BARIS 2: Detail Grid (Responsif: 2 kolom di mobile, 3 kolom di layar kecil/medium) --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-y-3 text-sm">
                        
                        {{-- KOLOM 1: JUMLAH IURAN --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Jumlah Iuran</p>
                            <p class="font-bold text-gray-800">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </p>
                        </div>
                        
                        {{-- KOLOM 2: WAKTU VERIFIKASI --}}
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Waktu Verifikasi</p>
                            @if($payment->status === 'verified' && $payment->verified_at)
                                <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($payment->verified_at)->isoFormat('D MMM Y, HH:mm') }}</p>
                            @else
                                <p class="italic text-gray-400">Belum Diverifikasi</p>
                            @endif
                        </div>

                        {{-- KOLOM 3: VERIFIKATOR (Terlihat di layar kecil/medium ke atas) --}}
                        <div class="sm:block hidden">
                            <p class="text-xs font-medium text-gray-500 uppercase">Diverifikasi Oleh</p>
                            @if($payment->status === 'verified')
                                <p class="text-gray-700 font-medium">{{ $payment->verifier?->name ?? 'Bendahara' }}</p>
                            @else
                                <p class="italic text-gray-400">N/A</p>
                            @endif
                        </div>

                        {{-- KOLOM 4: Aksi/Bukti (Mengambil 2-3 kolom) --}}
                        <div class="col-span-2 sm:col-span-3 border-t pt-3 mt-3">
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Aksi Cepat</p>
                            <div class="flex flex-wrap gap-2">
                                @if ($payment->status === 'pending')
                                    <button wire:click="verifyPayment({{ $payment->id }})" class="flex-1 min-w-[100px] text-green-600 hover:text-green-800 text-xs p-2 rounded bg-green-50 hover:bg-green-100 border border-green-200 justify-center font-medium shadow-sm transition duration-150 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Verifikasi
                                    </button>
                                    <button wire:click="confirmRejectPayment({{ $payment->id }})" class="flex-1 min-w-[100px] text-red-600 hover:text-red-800 text-xs p-2 rounded bg-red-50 hover:bg-red-100 border border-red-200 justify-center font-medium shadow-sm transition duration-150 flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        Tolak
                                    </button>
                                @endif
                                
                                @if ($payment->proof_path)
                                <a href="{{ \Storage::url($payment->proof_path) }}" target="_blank" class="flex-1 min-w-[100px] text-xs text-indigo-600 hover:underline hover:text-indigo-800 flex items-center justify-center p-2 rounded bg-indigo-50 transition duration-150 font-medium border border-indigo-200">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Bukti
                                </a>
                                @else
                                <span class="flex-1 text-xs text-gray-500 font-medium p-2 bg-gray-100 rounded-md text-center border border-gray-200">Menunggu Unggahan</span>
                                @endif
                            </div>
                        </div>
                        
                    </div>
                </div>
                @empty
                <p class="text-gray-500 p-4 bg-gray-100 rounded-lg border border-gray-200 text-sm italic">Tidak ada data pembayaran yang ditemukan untuk grup ini.</p>
                @endforelse
            </div>
        </div>
    @else
        <div class="mt-8 p-4 sm:p-6 bg-indigo-50 border-l-6 border-indigo-600 rounded-lg text-indigo-800 shadow-lg font-inter">
            <p class="font-extrabold text-base sm:text-lg mb-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Informasi
            </p>
            <p class="text-xs sm:text-sm">Pilih salah satu kartu grup di atas untuk memuat daftar pembayaran. Klik kartu yang sama lagi untuk menutupnya.</p>
        </div>
    @endif

    {{-- Modal Reject Payment --}}
    @if ($showRejectModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 font-inter">
            <div class="bg-white rounded-xl shadow-2xl p-5 sm:p-6 w-full max-w-xs sm:max-w-sm mx-auto">
                <h3 class="text-lg sm:text-xl font-bold mb-4 text-red-700 border-b pb-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.503-1.66 1.732-3.0l-6.928-12.0c-.77-1.33-2.69-1.33-3.46 0l-6.928 12.0c-.77 1.34.193 3.0 1.732 3.0z" /></svg>
                    Tolak Pembayaran
                </h3>
                <form wire:submit.prevent="rejectPayment">
                    <div class="mb-4">
                        <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-1">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejectReason" wire:model.defer="rejectReason" rows="3" placeholder="Contoh: Bukti transfer tidak jelas."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-sm"></textarea>
                        @error('rejectReason') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="$set('showRejectModal', false)"
                            class="px-3 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-3 py-2 text-xs sm:text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 shadow-md transition duration-150">
                            Konfirmasi Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>