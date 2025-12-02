<div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
    <!-- Judul dan Deskripsi -->
    <header class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight rounded-lg">
            Ringkasan Iuran Arisan Anda (Grup Aktif)
        </h1>
        <p class="mt-2 text-md text-gray-500">
            Berikut adalah status pembayaran Anda untuk grup arisan yang Statusnya Aktif dan Anda menjadi anggotanya. (ID Anggota: {{ $memberId }})
        </p>
    </header>

    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
            <span class="font-medium">Error:</span> {{ session('error') }}
        </div>
    @endif

    <!-- Konten Utama: Daftar Grup -->

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($groupsWithPaymentStatus as $group)
            @php
                // Menyesuaikan penamaan status dari 'none' ke 'Unpaid' untuk tampilan
                $status = $group['payment_status'] === 'none' ? 'unpaid' : $group['payment_status'];
                $statusDisplay = '';

                // LOGIKA NAVIGASI BARU: Mendefinisikan rute dan parameter
                $paymentRoute = 'payments.anggota.group_member_payment_status';
                
                // *** SOLUSI: MENGGANTI KUNCI PARAMETER MENJADI 'group' ***
                // Kunci array harus cocok persis dengan placeholder rute {group}
                $routeParams = ['group' => $group['id']]; 

                switch ($status) {
                    case 'verified':
                        $borderColor = 'border-green-500';
                        $statusColor = 'text-green-700 bg-green-100';
                        $statusIcon = '✅';
                        $statusDisplay = 'LUNAS';
                        break;
                    case 'pending':
                        $borderColor = 'border-yellow-500';
                        $statusColor = 'text-yellow-700 bg-yellow-100';
                        $statusIcon = '⏳';
                        $statusDisplay = 'VERIFIKASI';
                        break;
                    case 'rejected':
                        $borderColor = 'border-red-500';
                        $statusColor = 'text-red-700 bg-red-100';
                        $statusIcon = '❌';
                        $statusDisplay = 'DITOLAK';
                        break;
                    case 'unpaid':
                    default:
                        $borderColor = 'border-indigo-500';
                        $statusColor = 'text-indigo-700 bg-indigo-100';
                        $statusIcon = '⚠️';
                        $statusDisplay = 'BELUM BAYAR';
                        break;
                }
            @endphp

            {{-- Kartu Grup --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden transition duration-300 transform hover:shadow-xl hover:-translate-y-1">
                <div class="p-6 border-t-4 {{ $borderColor }}">
                    
                    <div class="flex items-start justify-between">
                        <h2 class="text-xl font-bold text-gray-800 mb-1 leading-snug">{{ $group['name'] }}</h2>
                        
                        {{-- Status Pembayaran dalam Badge --}}
                        <span class="ml-4 flex-shrink-0 px-3 py-1 text-xs font-bold rounded-full {{ $statusColor }} shadow-sm">
                            {{ $statusIcon }} {{ $statusDisplay }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">
                        <span class="font-semibold">Putaran Aktif:</span> #{{ $group['active_round_number'] ?? 'N/A' }}
                    </p>

                    {{-- DETAIL KUOTA DAN FREKUENSI --}}
                    <div class="space-y-1 text-sm text-gray-600 mb-4 border-t border-gray-100 pt-3 mt-3">
                        <div class="flex justify-between">
                            <span class="font-medium">Anggota Kuota:</span>
                            <span class="text-indigo-600 font-bold">{{ $group['current_members_count'] ?? 0 }} / {{ $group['capacity'] ?? 'N/A' }} Orang</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Frekuensi Pembayaran:</span>
                            <span class="text-gray-800 font-semibold">{{ $group['payment_frequency'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    {{-- AKHIR DETAIL KUOTA --}}

                    <div class="space-y-3 pt-4 border-t border-gray-100">
                        {{-- Detail Iuran --}}
                        <div class="flex justify-between items-center text-md font-semibold">
                            <span class="text-gray-600">Total Iuran Wajib:</span>
                            <span class="text-2xl text-indigo-600 font-extrabold">
                                Rp {{ number_format($group['fee_per_member'] ?? 0, 0, ',', '.') }}
                            </span> 
                        </div>

                        {{-- Tombol Aksi (DIUBAH MENJADI NAVIGASI RUTE) --}}
                        @if ($status === 'unpaid' || $status === 'rejected')
                            <a href="{{ route($paymentRoute, $routeParams) }}"
                                class="w-full mt-4 inline-block text-center py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition duration-150 shadow-md"
                                wire:navigate>
                                Lakukan Pembayaran
                            </a>
                        @elseif ($status === 'pending')
                            <a href="{{ route($paymentRoute, $routeParams) }}"
                                class="w-full mt-4 inline-block text-center py-3 px-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg transition duration-150 shadow-md"
                                wire:navigate>
                                Lihat Status (Menunggu Verifikasi)
                            </a>
                        @else
                            <button disabled
                                class="w-full mt-4 py-3 px-4 bg-green-500 text-white font-bold rounded-lg opacity-80 cursor-not-allowed">
                                Pembayaran Selesai (Lunas)
                            </button>
                        @endif
                        
                    </div>
                </div>
            </div>
        @empty
            {{-- Tampilan Kosong --}}
            <div class="col-span-full bg-white rounded-xl shadow-lg border border-gray-200 p-8 text-center">
                <div class="text-5xl mb-4" role="img" aria-label="Not Found">🧭</div>
                <p class="text-xl font-bold text-gray-700 mb-2">Tidak Ada Grup Arisan Aktif</p>
                <p class="text-sm text-gray-500">
                    Anda belum terdaftar dalam grup arisan yang statusnya saat ini Aktif.
                </p>
            </div>
        @endforelse


    </div>

</div>