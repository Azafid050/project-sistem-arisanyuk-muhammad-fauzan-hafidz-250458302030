<div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
<!-- Header -->
<header class="mb-6">
<h1 class="text-3xl font-extrabold text-gray-900 leading-tight">
Status Iuran Grup: {{ $group->name }}
</h1>
<p class="mt-2 text-md text-gray-500">
Putaran Aktif #{{ $activeRound->round_number ?? 'N/A' }} | Iuran Wajib: Rp {{ number_format($group->fee_per_member, 0, ',', '.') }}
</p>
</header>

@if (session()->has('success'))
    <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">{{ session('success') }}</div>
@endif
@if (session()->has('error'))
    <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">{{ session('error') }}</div>
@endif
@error('proof_image')
    <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
        <span class="font-medium">Error Upload:</span> {{ $message }}
    </div>
@enderror

@if (!$activeRound)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <p class="text-sm text-yellow-700 font-semibold">Grup belum memiliki putaran aktif atau sudah selesai.</p>
    </div>
@else
    <!-- Status Pembayaran Saat Ini -->
    <div class="bg-white rounded-xl shadow-xl p-6 mb-8 border-t-4 @if($paymentSubmissionStatus === 'verified') border-green-500 @elseif($paymentSubmissionStatus === 'pending') border-yellow-500 @elseif($paymentSubmissionStatus === 'rejected') border-red-500 @else border-indigo-500 @endif">
        <h2 class="text-xl font-bold mb-4">Status Pembayaran Anda</h2>
        
        <div class="flex justify-between items-center">
            <p class="text-gray-600 font-semibold">Status:</p>
            <span class="text-lg font-extrabold px-4 py-2 rounded-full @if($paymentSubmissionStatus === 'verified') text-green-700 bg-green-100 @elseif($paymentSubmissionStatus === 'pending') text-yellow-700 bg-yellow-100 @elseif($paymentSubmissionStatus === 'rejected') text-red-700 bg-red-100 @else text-indigo-700 bg-indigo-100 @endif">
                {{ ucfirst($paymentSubmissionStatus === 'none' ? 'Belum Bayar' : $paymentSubmissionStatus) }}
            </span>
        </div>

        @if ($currentPayment)
            <div class="mt-4 pt-4 border-t border-gray-100 text-sm">
                <p class="font-medium">Dikirim:</p>
                <p class="text-gray-500">{{ $currentPayment->created_at->format('d M Y H:i') }}</p>
                <p class="font-medium mt-2">Jumlah:</p>
                <p class="text-indigo-600 font-bold">Rp {{ number_format($currentPayment->amount, 0, ',', '.') }}</p>
                @if ($currentPayment->status === 'rejected')
                    <p class="mt-2 text-red-500 font-semibold">Catatan: Bukti ditolak. Silakan kirim ulang pembayaran yang benar.</p>
                @endif
            </div>
        @endif

        <!-- Tombol Aksi -->
        <div class="mt-6 border-t pt-6">
            @if ($paymentSubmissionStatus === 'none' || $paymentSubmissionStatus === 'rejected')
                <button wire:click="$set('showPaymentModal', true)"
                        class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150 shadow-md">
                    Lakukan / Kirim Ulang Pembayaran
                </button>
            @elseif ($paymentSubmissionStatus === 'pending')
                <button disabled
                        class="w-full py-3 px-4 bg-yellow-500 text-white font-bold rounded-lg opacity-80 cursor-not-allowed">
                    Menunggu Verifikasi Bendahara...
                </button>
            @else
                <button disabled
                        class="w-full py-3 px-4 bg-green-500 text-white font-bold rounded-lg opacity-80 cursor-not-allowed">
                    Pembayaran LUNAS
                </button>
            @endif
        </div>

    </div>
@endif

<!-- Modal Pengiriman Pembayaran -->
@if ($showPaymentModal)
    {{-- HAPUS: bg-gray-600 bg-opacity-75 --}}
    {{-- KELAS BARU: fixed inset-0 hanya menyisakan pemusatan dan z-index. --}}
    <div class="fixed inset-0 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-auto">
            <h3 class="text-xl font-bold mb-4">Kirim Bukti Pembayaran</h3>
            <form wire:submit.prevent="submitPayment">
                
                <!-- Jumlah Iuran -->
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah Transfer</label>
                    <input type="number" id="amount" wire:model.defer="amount" step="0.01" 
                            class="p-2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('amount') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">Minimal: Rp {{ number_format($group->fee_per_member, 0, ',', '.') }}</p>
                </div>

                <!-- Bukti Gambar -->
                <div class="mb-4">
                    <label for="proof_image" class="block text-sm font-medium text-gray-700">Upload Bukti Transfer (Gambar)</label>
                    <input type="file" id="proof_image" wire:model="proof_image" accept="image/*"
                            class="p-2 mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
                    @error('proof_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    
                    @if ($proof_image)
                        <p class="text-xs text-green-600 mt-2">File siap diupload.</p>
                    @endif
                    <div wire:loading wire:target="proof_image" class="text-indigo-500 text-sm mt-2">Uploading...</div>
                </div>
                
                <!-- Tombol Aksi Modal -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showPaymentModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                        Kirim Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif


</div>