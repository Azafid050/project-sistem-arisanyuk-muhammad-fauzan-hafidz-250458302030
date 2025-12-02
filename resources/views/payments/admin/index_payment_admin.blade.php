<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 border-b pb-4">Overview Pembayaran (Akses Admin)</h1>

        <!-- Notifikasi Sesi (tetap dipertahankan untuk pesan sistem umum) -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 shadow-md" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 shadow-md" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Responsive Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            
            <!-- Kolom Kiri: Daftar SEMUA Grup (Dibuat Sticky di desktop) -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-xl rounded-xl p-5 lg:sticky lg:top-8">
                    <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">Semua Grup Arisan</h2>
                    
                    <div class="space-y-3 max-h-[80vh] overflow-y-auto pr-2">
                        @forelse ($groups as $groupData)
                            <div wire:click="selectGroup({{ $groupData['id'] }})"
                                class="p-4 rounded-xl border cursor-pointer transition duration-200 transform hover:scale-[1.01] ease-in-out 
                                        {{ $selectedGroupId === $groupData['id'] ? 'bg-indigo-600 text-white shadow-2xl border-indigo-700' : 'bg-white hover:bg-indigo-50 border-gray-200' }}">
                                
                                <div class="flex justify-between items-start">
                                    <p class="font-extrabold text-lg leading-snug">{{ $groupData['name'] }}</p>
                                    @if ($groupData['is_fully_paid'])
                                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full mt-0.5
                                            {{ $selectedGroupId === $groupData['id'] ? 'bg-white text-green-600' : 'bg-green-100 text-green-800' }}">
                                            SIAP DIUNDI
                                        </span>
                                    @elseif ($groupData['is_round_active'])
                                        <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full mt-0.5
                                            {{ $selectedGroupId === $groupData['id'] ? 'bg-white text-yellow-600' : 'bg-yellow-100 text-yellow-800' }}">
                                            PENDING
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="{{ $selectedGroupId === $groupData['id'] ? 'text-indigo-200' : 'text-gray-500' }} text-sm mt-1 font-medium">
                                    Bendahara: {{ $groupData['bendahara_name'] }}
                                </p>

                                @if ($groupData['is_round_active'])
                                    <div class="{{ $selectedGroupId === $groupData['id'] ? 'text-indigo-200' : 'text-gray-500' }} text-sm mt-2">
                                        Putaran Aktif: #{{ $groupData['active_round_number'] }}
                                    </div>
                                    <div class="mt-2 text-xs font-medium">
                                        <span class="{{ $selectedGroupId === $groupData['id'] ? 'text-white' : 'text-gray-700' }}">
                                            Verifikasi: {{ $groupData['verified_payments_count'] }} / {{ $groupData['total_members_quota'] }}
                                        </span>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                            @php
                                                $progress = $groupData['total_members_quota'] > 0 
                                                    ? ($groupData['verified_payments_count'] / $groupData['total_members_quota']) * 100 
                                                    : 0;
                                            @endphp
                                            <div class="h-1.5 rounded-full {{ $selectedGroupId === $groupData['id'] ? 'bg-white' : 'bg-indigo-500' }}" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="{{ $selectedGroupId === $groupData['id'] ? 'text-indigo-200' : 'text-gray-500' }} text-sm mt-2">
                                        Tidak ada putaran yang menanti pembayaran.
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 italic p-4 bg-gray-50 rounded-lg">Tidak ada grup arisan dalam sistem.</p>
                        @endforelse
                    </div>

                </div>
            </div>

            <!-- Kolom Kanan: Detail Verifikasi Pembayaran -->
            <div class="lg:col-span-2">
                @if ($selectedGroupId && $activeRound)
                    <div class="bg-white shadow-xl rounded-xl p-6">
                        <h2 class="text-2xl font-extrabold text-gray-800 mb-2">{{ $group->name }}</h2>
                        <h3 class="text-xl font-semibold text-indigo-600 mb-4 border-b pb-3">Detail Putaran #{{ $activeRound->round_number }}</h3>
                        
                        <p class="text-gray-600 mb-2">Total anggota wajib bayar: <span class="font-extrabold text-gray-800">{{ $totalQuotaMembers }}</span></p>
                        <p class="text-gray-600 mb-6">Pembayaran Terverifikasi: <span class="font-extrabold text-green-600">{{ $verifiedPaymentsCount }}</span></p>

                        <!-- Status Kesiapan Undian -->
                        @if ($isReadyToDraw)
                            <div class="p-4 bg-green-50 border-2 border-green-300 text-green-700 font-bold rounded-lg mb-6 flex items-center shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="text-sm sm:text-base">Grup SIAP DIUNDI! Semua pembayaran telah diverifikasi.</span>
                            </div>
                        @else
                            <div class="p-4 bg-yellow-50 border-2 border-yellow-300 text-yellow-700 font-bold rounded-lg mb-6 flex items-center shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span class="text-sm sm:text-base">Menunggu {{ $totalQuotaMembers - $verifiedPaymentsCount }} pembayaran lagi sebelum siap diundi.</span>
                            </div>
                        @endif


                        <!-- Tabel Daftar Pembayaran (Dibuat Responsive) -->
                        <h3 class="text-xl font-bold text-gray-700 mb-4 border-t pt-4">Daftar Pembayaran Anggota</h3>
                        <div class="overflow-x-auto shadow-md rounded-lg border">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[150px]">Anggota</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[100px]">Jumlah</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[120px]">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider min-w-[120px]">Bukti Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse ($payments as $payment)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $payment->groupMember->user->name ?? 'User Tidak Ditemukan' }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 font-mono">
                                                Rp. {{ number_format($payment->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if ($payment->status === \App\Models\Payment::STATUS_PENDING)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif ($payment->status === \App\Models\Payment::STATUS_VERIFIED)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                                        Terverifikasi
                                                    </span>
                                                @elseif ($payment->status === \App\Models\Payment::STATUS_REJECTED)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                                        Ditolak
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if ($payment->proof_path)
                                                    <a href="{{ Storage::url($payment->proof_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-medium hover:underline">
                                                        Lihat Bukti
                                                    </a>
                                                @else
                                                    <span class="text-red-500 font-medium">Tidak Ada</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic bg-gray-50">
                                                Belum ada pembayaran yang masuk atau perlu diverifikasi untuk putaran ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif ($selectedGroupId)
                    <div class="bg-white shadow-xl rounded-xl p-8 text-center h-full flex flex-col justify-center min-h-[400px]">
                        <p class="text-xl text-gray-500 font-medium">Tidak ada putaran pembayaran aktif untuk grup ini.</p>
                        <p class="text-sm text-gray-400 mt-2">Silakan pilih grup lain atau tunggu putaran baru dimulai.</p>
                    </div>
                @else
                    <div class="bg-white shadow-xl rounded-xl p-8 text-center h-full flex flex-col justify-center min-h-[400px]">
                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2" />
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Pilih Grup</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Pilih salah satu Grup Arisan di kolom kiri untuk melihat detail verifikasi pembayaran putaran aktif.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>