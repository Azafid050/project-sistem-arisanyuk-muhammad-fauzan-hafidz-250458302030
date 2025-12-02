<div class="p-4 sm:p-6 lg:p-7 max-w-6xl mx-auto font-sans">
<script src="https://cdn.tailwindcss.com"></script>
<style>
.font-sans {
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
}
/* Penyesuaian kustom untuk border-l-6 */
.border-l-6 {
    border-left-width: 6px;
}
</style>

<header class="mb-8 text-center">
    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight tracking-tight rounded-lg p-2 border-b-4 border-indigo-600">
        💰 Verifikasi Pembayaran Arisan
    </h1>
</header>

@if (isset($group))
    <div class="mt-8 p-4 sm:p-7 rounded-xl shadow-xl bg-white/70 backdrop-blur-sm">
        {{-- BAGIAN DETAIL PEMBAYARAN --}}
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-1">
                Detail Pembayaran: {{ $group->name }}
            </h2>
            <p class="text-sm text-gray-600">
                Putaran #{{ $activeRound->round_number ?? 'N/A' }} | Iuran Anggota: 
                <span class="font-extrabold text-indigo-700">Rp {{ number_format($group->fee_per_member ?? 0, 0, ',', '.') }}</span>
            </p>
        </div>

        {{-- PERBAIKAN FOKUS DI SINI: STATUS CARD LEBIH PADAT DAN RAPIH --}}
        <div class="bg-white rounded-xl shadow-lg p-5 mb-7 flex flex-col sm:flex-row justify-between items-center border-l-6 @if($isReadyToDraw) border-green-600 @else border-yellow-600 @endif">
            <div class="flex-grow mb-4 sm:mb-0">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Status Pembayaran Putaran Ini</p>
                <div class="flex items-center mt-2">
                    @if($isReadyToDraw)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-green-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        <h3 class="text-xl sm:text-2xl font-black text-green-700">
                            ARISAN LUNAS! 
                        </h3>
                        <span class="ml-3 text-lg font-bold text-green-600">({{ $verifiedPaymentsCount }} / {{ $totalQuotaMembers }})</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-yellow-600 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.487 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 11a1 1 0 100 2 1 1 0 000-2zm0-4a1 1 0 000 2h.01a1 1 0 000-2H10z" clip-rule="evenodd" /></svg>
                        <h3 class="text-xl sm:text-2xl font-black text-yellow-700">
                            Menunggu Pembayaran
                        </h3>
                        <span class="ml-3 text-lg font-bold text-yellow-600">({{ $verifiedPaymentsCount }} / {{ $totalQuotaMembers }})</span>
                    @endif
                </div>
            </div>

            {{-- TOMBOL MULAI UNDIAN --}}
            <div class="ml-0 sm:ml-4 flex-shrink-0 w-full sm:w-auto">
                @if ($isReadyToDraw)
                    <a href="{{ '/rounds/roulette/' . $group->id }}"
                        class="w-full justify-center sm:w-auto py-2.5 px-5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition duration-150 shadow-md hover:shadow-lg flex items-center text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                        Mulai Undian Putaran #{{ $activeRound->round_number ?? 'N/A' }}
                    </a>
                @else
                    <button disabled class="w-full justify-center sm:w-auto py-2.5 px-5 bg-gray-400 text-white font-bold rounded-lg cursor-not-allowed shadow-md text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Tunggu {{ $totalQuotaMembers - $verifiedPaymentsCount }} Anggota Lagi
                    </button>
                @endif
            </div>
        </div>
        {{-- END: PERBAIKAN STATUS CARD --}}

        {{-- START: CARD VIEW (Digunakan untuk semua ukuran layar, ini adalah daftar anggotanya) --}}
        <div class="space-y-4">
            @forelse ($payments as $payment)
            <div class="bg-white p-4 rounded-xl shadow-lg hover:shadow-xl transition duration-200 border-l-4 
                @if($payment->status === 'verified') border-green-500 
                @elseif($payment->status === 'pending') border-yellow-500 
                @else border-red-500 @endif">
                {{-- Baris 1: Nama & Status --}}
                <div class="flex justify-between items-start mb-3 pb-2 border-b border-gray-100">
                    <div>
                        <p class="text-base font-extrabold text-gray-900 leading-tight">
                            {{ $payment->groupMember->user->name ?? 'Anggota Dihapus' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">({{ $payment->groupMember->role ?? 'N/A' }})</p>
                    </div>
                    <span class="px-3 py-1 text-xs leading-5 font-bold rounded-full flex-shrink-0
                        @if($payment->status === 'verified') bg-green-500 text-white
                        @elseif($payment->status === 'pending') bg-yellow-500 text-white
                        @else bg-red-500 text-white @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                {{-- Baris 2: Detail Pembayaran (Grid untuk kerapian) --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3 text-sm">
                    <div>
                        <p class="text-xs font-medium text-gray-500">Iuran Dibayar</p>
                        <p class="font-bold text-gray-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">Diverifikasi Oleh</p>
                        <p class="font-semibold text-gray-700">{{ $payment->verifier->name ?? ($payment->status === 'verified' ? 'Bendahara' : '-') }}</p>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <p class="text-xs font-medium text-gray-500">Waktu Verifikasi</p>
                        <p class="font-semibold text-gray-700 text-xs">
                            @if($payment->status === 'verified')
                                {{ $payment->verified_at ? $payment->verified_at->format('d M Y, H:i') : '-' }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Baris 3: Aksi/Bukti --}}
                <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap justify-between items-center space-y-2 sm:space-y-0">
                    <div class="flex-grow">
                        @if ($payment->proof_path)
                            <a href="{{ \Storage::url($payment->proof_path) }}" target="_blank" class="text-sm text-indigo-600 hover:underline flex items-center font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                Lihat Bukti Pembayaran
                            </a>
                        @else
                            @if ($payment->status === 'rejected')
                                <span class="text-sm text-red-500 font-medium">Bukti Ditolak</span>
                            @else
                                <span class="text-sm text-gray-400">Menunggu Unggahan Bukti</span>
                            @endif
                        @endif
                    </div>

                    @if ($payment->status === 'pending')
                        <div class="flex space-x-2 w-full sm:w-auto justify-end">
                            <button wire:click="verifyPayment({{ $payment->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition duration-150 shadow-md flex-shrink-0">
                                Verifikasi
                            </button>
                            <button wire:click="confirmRejectPayment({{ $payment->id }})" class="bg-red-600 hover:bg-red-700 text-white text-xs font-medium py-2 px-3 rounded-lg transition duration-150 shadow-md flex-shrink-0">
                                Tolak
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            @empty
                <div class="p-6 text-center text-gray-500 italic bg-white rounded-xl shadow-lg">Tidak ada data pembayaran yang perlu diverifikasi pada putaran ini.</div>
            @endforelse
        </div>
        {{-- END: CARD VIEW --}}
    </div>
@else
    <div class="mt-8 p-6 bg-white border-l-6 border-indigo-600 rounded-xl text-indigo-800 shadow-lg">
        <p class="font-extrabold text-lg mb-1 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Informasi
        </p>
        <p class="text-sm">Pilih salah satu kartu grup di atas untuk memuat daftar pembayaran.</p>
    </div>
@endif

{{-- MODAL PENOLAKAN --}}
@if ($showRejectModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-sm mx-auto">
            <h3 class="text-xl font-bold mb-4 text-red-700 border-b pb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 24 24" stroke="currentColor" fill="none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.503-1.66 1.732-3.0l-6.928-12.0c-.77-1.33-2.69-1.33-3.46 0l-6.928 12.0c-.77 1.34.193 3.0 1.732 3.0z" /></svg>
                Tolak Pembayaran
            </h3>
            <form wire:submit.prevent="rejectPayment">
                @if ($currentProofPath)
                    <div class="mb-4 text-center">
                        <a href="{{ \Storage::url($currentProofPath) }}" target="_blank" class="text-sm text-indigo-600 hover:underline font-medium flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Lihat Bukti Pembayaran
                        </a>
                    </div>
                @endif
                <div class="mb-4">
                    <label for="rejectReason" class="block text-sm font-medium text-gray-700 mb-1">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejectReason" wire:model.defer="rejectReason" rows="3" placeholder="Contoh: Bukti transfer tidak jelas."
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 p-2 text-sm"></textarea>
                    @error('rejectReason') <span class="text-red-500 text-xs mt-1 block font-semibold">{{ $message }}</span> @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" wire:click="$toggle('showRejectModal')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50 shadow-md transition duration-150">
                        Konfirmasi Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

</div>