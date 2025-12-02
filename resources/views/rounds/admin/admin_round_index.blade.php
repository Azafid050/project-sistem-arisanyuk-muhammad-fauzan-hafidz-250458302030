<div class="p-4 sm:p-6 lg:p-10 max-w-6xl mx-auto min-h-screen pt-8 pb-12 font-inter bg-gray-50">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>

    <header class="mb-8 text-center">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">
            Overview Status Putaran (Akses Admin)
        </h1>
        <p class="mt-1 text-sm text-gray-600">
            Daftar semua Grup Arisan dan status putaran aktif mereka.
        </p>
    </header>

    <!-- Notifikasi -->
    @if (session()->has('error'))
        <div class="mb-4 p-3 text-sm text-red-800 rounded-xl bg-red-100 border border-red-300 shadow-sm" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('success'))
        <div class="mb-4 p-3 text-sm text-green-800 rounded-xl bg-green-100 border border-green-300 shadow-sm" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Konten Utama: Daftar Semua Grup -->
    <div class="space-y-6">
        @forelse ($managedGroups as $group)
            <div class="bg-white rounded-xl shadow-lg transition duration-300 p-4 md:p-6 border-l-4 border-indigo-600/70">
                <!-- Header Grup -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4 border-b pb-3">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-2 lg:mb-0">{{ $group->name }}</h2>
                    <div class="flex flex-wrap gap-2 items-center mt-2 lg:mt-0">
                        <!-- Status Putaran Saat Ini (READ-ONLY) -->
                        <span class="px-3 py-1 text-xs font-bold rounded-full shadow-sm {{ $group->statusClass }}">
                            {{ $group->roundStatus }}
                        </span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700 mb-6">
                    <p><strong>Bendahara:</strong> {{ $group->bendaharaName }}</p>
                    <p><strong>Total Anggota:</strong> {{ $group->members->count() }}</p>
                </div>


                <!-- Bagian Detail Putaran Selesai -->
                
                @if ($group->completedRounds->isEmpty())
                    <p class="text-gray-500 italic p-3 bg-gray-100 rounded-lg text-sm text-center border border-dashed">
                        Belum ada putaran yang berhasil diselesaikan di grup ini.
                    </p>
                @else
                    <div class="overflow-x-auto rounded-lg shadow-inner border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600 uppercase tracking-wider font-bold">Putaran</th>
                                    <th class="px-4 py-2 text-left text-gray-600 uppercase tracking-wider font-bold">Pemenang</th>
                                    <th class="px-4 py-2 text-left text-gray-600 uppercase tracking-wider font-bold">Status</th>
                                    <th class="px-4 py-2 text-left text-gray-600 uppercase tracking-wider font-bold">Tanggal Cair</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach ($group->completedRounds as $round)
                                    <tr class="hover:bg-indigo-50/10 transition duration-100">
                                        <td class="px-4 py-3 font-medium text-gray-900">#{{ $round->round_number }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $round->winnerMember?->user?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
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
            <!-- State Kosong untuk Admin -->
            <div class="text-center py-12 bg-white rounded-xl shadow-xl border-4 border-dashed border-gray-300">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.477 3-9s-1.343-9-3-9-3 4.477-3 9 1.343 9 3 9z"></path></svg>
                <h3 class="text-lg font-medium text-gray-900">Tidak Ada Grup Arisan Aktif</h3>
                <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada grup arisan yang tercatat dalam sistem.</p>
            </div>
        @endforelse
    </div>

    <!-- Tautan Kembali (Disimpan sebagai contoh jika ada navigasi admin) -->
    <div class="mt-10 text-center">
        <!-- Rute ini mungkin perlu disesuaikan di aplikasi Anda -->
        <a href="#" class="flex items-center justify-center space-x-2 text-indigo-600 hover:text-indigo-800 font-semibold text-sm transition duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Halaman Admin Utama</span>
        </a>
    </div>
</div>