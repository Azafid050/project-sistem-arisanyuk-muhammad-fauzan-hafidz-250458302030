<div class="p-4 sm:p-5 md:p-6 lg:p-8 xl:p-10 max-w-6xl mx-auto min-h-screen pt-6 pb-12 font-inter bg-gray-100">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>

    <header class="mb-6 sm:mb-8 text-center">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">
            Manajemen Putaran Arisan
        </h1>
        <p class="mt-1 text-xs sm:text-sm text-gray-600 px-2">
            Daftar Grup yang Anda kelola dan status putaran saat ini.
        </p>
    </header>

    @if (session()->has('error'))
        <div class="mb-4 p-3 text-xs sm:text-sm text-red-800 rounded-xl bg-red-100 border border-red-300 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('success'))
        <div class="mb-4 p-3 text-xs sm:text-sm text-green-800 rounded-xl bg-green-100 border border-green-300 shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-5 sm:space-y-6">
        @forelse ($managedGroups as $group)
            <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 p-4 sm:p-5 md:p-6 border-l-4 border-indigo-600">
                <!-- Header & Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900">
                        {{ $group->name }}
                    </h2>
                    <div class="flex flex-wrap gap-2">
                        @if ($group->readyToDrawRound)
                            <span class="px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded-full shadow-sm">
                                SIAP DIUNDI
                            </span>
                            <a href="{{ route('rounds.round_roulette', ['group' => $group->id]) }}"
                                class="inline-flex items-center justify-center py-1.5 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs sm:text-sm rounded-lg transition duration-150 shadow-md hover:shadow hover:scale-[1.02]">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.836 0h.582m0 0v5h.582m0-10l-2.484 2.484m0 0-2.484 2.484m-4.968 4.968l-2.484 2.484m0 0-2.484 2.484m9.936-9.936l2.484 2.484m0 0 2.484 2.484"></path>
                                </svg>
                                <span>Undi #{{ $group->readyToDrawRound->round_number }}</span>
                            </a>
                        @elseif ($group->pendingPaymentRound)
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full shadow-sm border border-yellow-300">
                                MENUNGGU PEMBAYARAN
                            </span>
                            <a href="{{ route('payments.verifikasi.payment_verification_bendahara', ['group' => $group->id]) }}"
                                class="inline-flex items-center justify-center py-1.5 px-3 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm rounded-lg transition duration-150 shadow-md hover:shadow hover:scale-[1.02]">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Pem. #{{ $group->pendingPaymentRound->round_number }}</span>
                            </a>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-200 rounded-full">
                                {{ $group->roundStatus }}
                            </span>
                        @endif
                    </div>
                </div>

                @if ($group->completedRounds->isEmpty())
                    <p class="text-gray-500 italic p-3 bg-gray-50 rounded-lg text-xs sm:text-sm text-center border">
                        Belum ada putaran selesai.
                    </p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2 sm:px-3 sm:py-2.5 text-left text-gray-600 uppercase tracking-wider whitespace-nowrap">Putaran</th>
                                    <th class="px-2 py-2 sm:px-3 sm:py-2.5 text-left text-gray-600 uppercase tracking-wider whitespace-nowrap">Pemenang</th>
                                    <th class="px-2 py-2 sm:px-3 sm:py-2.5 text-left text-gray-600 uppercase tracking-wider whitespace-nowrap">Status</th>
                                    <th class="px-2 py-2 sm:px-3 sm:py-2.5 text-left text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($group->completedRounds as $round)
                                    <tr class="hover:bg-indigo-50/30 transition duration-100">
                                        <td class="px-2 py-2 sm:px-3 sm:py-2.5 font-medium text-gray-900 whitespace-nowrap">#{{ $round->round_number }}</td>
                                        <td class="px-2 py-2 sm:px-3 sm:py-2.5 text-gray-700 truncate max-w-[120px] sm:max-w-none">
                                            {{ $round->winnerMember?->user?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-2 py-2 sm:px-3 sm:py-2.5">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                        </td>
                                        <td class="px-2 py-2 sm:px-3 sm:py-2.5 text-gray-500 whitespace-nowrap">
                                            {{ $round->payout_date ? \Carbon\Carbon::parse($round->payout_date)->format('d M Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10 sm:py-12 bg-white rounded-xl shadow-md border-2 border-dashed border-gray-200">
                <svg class="mx-auto h-9 w-9 sm:h-10 sm:w-10 text-gray-400 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Tidak Ada Grup</h3>
                <p class="mt-1 text-xs sm:text-sm text-gray-500 px-4">
                    Anda belum mengelola grup arisan sebagai bendahara.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('bendahara.dashboard') }}" class="inline-flex items-center justify-center space-x-1.5 text-indigo-600 hover:text-indigo-800 font-semibold text-sm group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Dashboard Bendahara</span>
        </a>
    </div>
</div>