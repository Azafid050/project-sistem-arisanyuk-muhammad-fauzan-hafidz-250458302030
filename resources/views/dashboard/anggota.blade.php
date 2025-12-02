<div class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-6">
                <h2 class="font-playfair text-2xl font-bold text-gray-900 mb-6">Arisan Saya</h2>

                @if($arisanGroups->isEmpty())
                    <div class="text-center py-10 text-gray-500">
                        <p>Anda belum mengikuti arisan aktif apa pun.</p>
                        <a href="{{ route('groups.anggota.index_anggota') }}" wire:navigate class="text-3xl font-extrabold text-gray-900 hover:underline mt-2 inline-block font-medium">Jelajahi arisan publik</a>
                    </div>
                @else
                    <div class="space-y-5">
                        @foreach($arisanGroups as $group)
                            <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition hover:border-blue-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold text-xl text-gray-900">{{ $group->name }}</h3>
                                        <p class="text-gray-600 text-sm">{{ $group->members_count }} anggota • {{ $group->payment_frequency ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                        {{ $group->status_label }}
                                    </span>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Iuran</p>
                                        <p class="font-semibold text-blue-700">Rp{{ number_format($group->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Putaran ke-</p>
                                        <p class="font-semibold">{{ $group->current_round }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="text-sm text-gray-500">Status Pembayaran:</p>
                                    @if($group->is_paid)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sudah Dibayar ✅
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Belum Dibayar ⏳
                                        </span>
                                        <a href="{{ route('payments.anggota.index_payment_anggota', $group->id) }}" wire:navigate class="ml-2 text-blue-600 hover:underline text-sm font-medium">Bayar Sekarang</a>
                                    @endif
                                </div>

                                <div class="mt-4 text-right">
                                    <a href="{{ route('groups.anggota.show_anggota', $group->id) }}" wire:navigate class="text-blue-600 font-medium hover:underline">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Ringkasan Aktivitas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Arisan Aktif</span>
                        <span class="font-medium text-blue-600">{{ $stats['active_groups'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Belum Bayar</span>
                        <span class="font-medium text-red-600">{{ $stats['unpaid'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sudah Menang</span>
                        <span class="font-medium text-green-600">{{ $stats['wins'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-blue-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Jatuh Tempo Mendatang</h3>
                @if($upcomingPayments->isEmpty())
                    <p class="text-gray-500 text-sm">Tidak ada pembayaran dalam 7 hari ke depan.</p>
                @else
                    <ul class="space-y-3">
                        @foreach($upcomingPayments as $payment)
                            <li class="text-sm">
                                <span class="font-medium text-blue-700">{{ $payment->group_name }}</span><br>
                                <span class="text-gray-600">Jatuh tempo: {{ $payment->due_date->format('d M Y') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                <h3 class="font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('groups.anggota.index_anggota') }}" wire:navigate class="block text-blue-700 hover:text-blue-900 font-medium flex items-center">
                        <span>🔍</span>
                        <span class="ml-2">Cari Arisan</span>
                    </a>
                    <a href="{{ route('payments.anggota.index_payment_anggota') }}" wire:navigate class="block text-blue-700 hover:text-blue-900 font-medium flex items-center">
                        <span>📜</span>
                        <span class="ml-2">Riwayat Pembayaran</span>
                    </a>
                    <a href="{{ route('profile.show') }}" wire:navigate class="block text-blue-700 hover:text-blue-900 font-medium flex items-center">
                        <span>👤</span>
                        <span class="ml-2">Kelola Profil</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>