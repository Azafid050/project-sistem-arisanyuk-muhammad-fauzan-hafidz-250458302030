<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

    {{-- Notifikasi Status (Verifikasi/Tolak) --}}
    @if (session()->has('success'))
        <div class="mb-8 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md">
            <p class="font-bold">Berhasil! 🎉</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-8 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md">
            <p class="font-bold">Error! ⚠️</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Peringatan Notifikasi --}}
    @if ($unreadNotifications > 0)
        <div class="mb-8 bg-indigo-100 border-l-4 border-indigo-500 text-indigo-700 p-4 rounded-md shadow-lg flex justify-between items-center flex-wrap">
            <div>
                <p class="font-bold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.405L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Anda memiliki **{{ $unreadNotifications }}** notifikasi baru!
                </p>
            </div>
            <button wire:click="markAllAsRead" class="mt-2 sm:mt-0 px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600 transition duration-150">
                Tandai Sudah Dibaca
            </button>
        </div>
    @endif

    <div class="space-y-8">

        {{-- SECTION 1: KARTU RINGKASAN KEUANGAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-green-500 transition duration-300 hover:shadow-green-300/50">
                <p class="text-xs font-medium text-gray-500 truncate uppercase">Dana Verified (Total)</p>
                <p class="mt-1 text-xl font-extrabold text-gray-900">
                    Rp {{ number_format($stats['total_verified_amount'], 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-red-500 transition duration-300 hover:shadow-red-300/50">
                <p class="text-xs font-medium text-gray-500 truncate uppercase">Total Tunggakan (Perlu Verifikasi)</p>
                <p class="mt-1 text-xl font-extrabold text-red-600">
                    Rp {{ number_format($stats['total_unverified_amount'], 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-yellow-500 transition duration-300 hover:shadow-yellow-300/50">
                <p class="text-xs font-medium text-gray-500 truncate uppercase">Verifikasi Tertunda</p>
                <p class="mt-1 text-xl font-extrabold text-yellow-600">
                    {{ number_format($stats['pending_verifications_count'], 0) }} Transaksi
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-xl border-t-4 border-indigo-500 transition duration-300 hover:shadow-indigo-300/50">
                <p class="text-xs font-medium text-gray-500 truncate uppercase">Grup Siap Diundi</p>
                <p class="mt-1 text-xl font-extrabold text-indigo-600">
                    {{ number_format($stats['ready_to_draw_count'], 0) }} Grup
                </p>
            </div>
        </div>

        {{-- SECTION 4: GRAFIK KEPATUHAN PEMBAYARAN GRUP BARU --}}
        @if ($groupStatuses->count() > 0)
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Progres Pembayaran Grup (Visualisasi)
                </h3>
                <p class="text-sm text-gray-500 mt-1">Perbandingan persentase anggota yang sudah melakukan pembayaran iuran per grup.</p>
            </div>
            <div class="p-6">
                <div style="height: 300px;">
                    <canvas id="groupComplianceChart"></canvas>
                </div>
            </div>
        </div>
        @endif

        {{-- SECTION 2: TABEL PEMBAYARAN PENDING --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Daftar Verifikasi Pembayaran ({{ $unverifiedPayments->count() }})
                </h3>
            </div>

            @if ($unverifiedPayments->isEmpty())
                <p class="p-6 text-gray-500">Tidak ada transaksi yang menunggu verifikasi saat ini. Pekerjaan bagus! 👍</p>
            @else
                <div class="overflow-x-auto hidden sm:block">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Grup & Putaran</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Anggota</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Iuran</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl. Kirim</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($unverifiedPayments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-indigo-700">{{ $payment->round->group->name }}</p>
                                    <p class="text-xs text-gray-500">Putaran Ke-{{ $payment->round->round_number }} (Jatuh Tempo: {{ $payment->round->draw_date->translatedFormat('d M') }})</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-medium text-gray-900">{{ $payment->groupMember->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->groupMember->user->email }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->created_at->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ $payment->proof_path ? \Illuminate\Support\Facades\Storage::url($payment->proof_path) : '#' }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-semibold" title="Lihat Bukti Transfer">
                                        Bukti
                                    </a>
                                    <button wire:click="verifyPayment({{ $payment->id }})"
                                            wire:confirm="Yakin ingin memverifikasi pembayaran ini?"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150">
                                        Verifikasi
                                    </button>
                                    <button wire:click="rejectPayment({{ $payment->id }})"
                                            wire:confirm="Yakin ingin MENOLAK pembayaran ini? Anggota akan diberi notifikasi untuk mengunggah ulang."
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                                        Tolak
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="sm:hidden p-4 space-y-4">
                    @foreach ($unverifiedPayments as $payment)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start border-b pb-2 mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Anggota: **{{ $payment->groupMember->user->name }}**</p>
                                    <p class="text-xs text-gray-500">{{ $payment->round->group->name }} (Putaran Ke-{{ $payment->round->round_number }})</p>
                                </div>
                                <p class="text-lg font-bold text-red-600">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-500 mb-3">
                                <p>Tgl. Kirim: {{ $payment->created_at->translatedFormat('d M Y H:i') }}</p>
                                <p>Jatuh Tempo: {{ $payment->round->draw_date->translatedFormat('d M') }}</p>
                            </div>
                            <div class="flex flex-wrap justify-end gap-2 text-sm font-medium">
                                <a href="{{ $payment->proof_path ? \Illuminate\Support\Facades\Storage::url($payment->proof_path) : '#' }}" target="_blank" class="flex-1 text-center py-1.5 text-indigo-600 border border-indigo-600 rounded-md hover:bg-indigo-50" title="Lihat Bukti Transfer">
                                    Lihat Bukti
                                </a>
                                <button wire:click="verifyPayment({{ $payment->id }})"
                                            wire:confirm="Yakin ingin memverifikasi pembayaran ini?"
                                            class="flex-1 py-1.5 text-white bg-green-600 hover:bg-green-700 rounded-md transition duration-150">
                                    Verifikasi
                                </button>
                                <button wire:click="rejectPayment({{ $payment->id }})"
                                            wire:confirm="Yakin ingin MENOLAK pembayaran ini?"
                                            class="flex-1 py-1.5 text-white bg-red-600 hover:bg-red-700 rounded-md transition duration-150">
                                    Tolak
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SECTION 3: STATUS KELENGKAPAN GRUP --}}
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    Kepatuhan Pembayaran Grup Aktif
                </h3>
                <p class="text-sm text-gray-500 mt-1">Lacak progres pembayaran iuran di grup yang Anda kelola.</p>
            </div>

            @if ($groupStatuses->count() === 0)
                <p class="p-6 text-gray-500">Anda belum menjadi Bendahara di grup yang aktif.</p>
            @else
                <div class="overflow-x-auto hidden sm:block">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Grup</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Putaran Aktif</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status Pengundian</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($groupStatuses as $group)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $group->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Ke-{{ $group->current_round }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $group->draw_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $group->paid_count }} / {{ $group->quota }} Anggota Sudah Bayar</div>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                                        @php
                                            $percentage = $group->quota > 0 ? ($group->paid_count / $group->quota) * 100 : 0;
                                            $color = $percentage < 50 ? 'bg-red-500' : ($percentage < 100 ? 'bg-yellow-500' : 'bg-green-500');
                                        @endphp
                                        <div class="h-2.5 rounded-full {{ $color }}" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if ($group->is_ready)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            SIAP DIUNDI
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            MENUNGGU
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="sm:hidden p-4 space-y-4">
                    @foreach ($groupStatuses as $group)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-start border-b pb-2 mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">**{{ $group->name }}**</p>
                                    <p class="text-xs text-gray-500">Putaran Aktif: Ke-{{ $group->current_round }} (Jatuh Tempo: {{ $group->draw_date }})</p>
                                </div>
                                <div class="text-right">
                                    @if ($group->is_ready)
                                        <span class="px-2 py-0.5 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            SIAP DIUNDI
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            MENUNGGU
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900 mb-1">{{ $group->paid_count }} / {{ $group->quota }} Anggota Sudah Bayar</div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    @php
                                        $percentage = $group->quota > 0 ? ($group->paid_count / $group->quota) * 100 : 0;
                                        $color = $percentage < 50 ? 'bg-red-500' : ($percentage < 100 ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    <div class="h-2.5 rounded-full {{ $color }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Script CHART.JS dan Livewire Event Listener --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        let myChartInstance = null;

        function initializeChart() {
            const canvas = document.getElementById('groupComplianceChart');
            if (!canvas) {
                console.warn('Canvas #groupComplianceChart tidak ditemukan.');
                return;
            }

            if (myChartInstance) {
                myChartInstance.destroy();
            }

            const groupData = @json($groupStatuses);
            if (groupData.length === 0) {
                console.warn('Tidak ada data grup untuk chart.');
                return;
            }

            const labels = groupData.map(group => group.name);
            // ✅ PERBAIKAN UTAMA: Gunakan is_ready untuk tentukan 100%
            const percentages = groupData.map(group => {
                if (group.is_ready) {
                    return 100;
                }
                if (group.quota <= 0) {
                    return 0;
                }
                return (group.paid_count / group.quota) * 100;
            });

            const backgroundColors = percentages.map(p => {
                if (p >= 100) return 'rgba(16, 185, 129, 0.8)'; // Hijau
                if (p >= 75) return 'rgba(251, 191, 36, 0.8)';  // Kuning
                return 'rgba(239, 68, 68, 0.8)';                // Merah
            });

            const data = {
                labels: labels,
                datasets: [{
                    label: 'Persentase Anggota Bayar',
                    data: percentages,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(c => c.replace('0.8', '1')),
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.parsed.x;
                                    let displayPercent = (value >= 99.9) ? 100 : Math.round(value);
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    label += displayPercent + '%';
                                    label += ' (' + groupData[context.dataIndex].paid_count + '/' + groupData[context.dataIndex].quota + ')';
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Persentase (%)'
                            }
                        }
                    }
                }
            };

            myChartInstance = new Chart(canvas, config);
        }

        document.addEventListener('livewire:navigated', () => {
            initializeChart();
        });

        Livewire.on('bendaharaDashboardMounted', () => {
            initializeChart();
        });

        Livewire.on('bendaharaDashboardRendered', () => {
            initializeChart();
        });

        if (document.getElementById('groupComplianceChart')) {
            if (typeof Livewire === 'undefined') {
                document.addEventListener('DOMContentLoaded', initializeChart);
            }
        }
    </script>
</div>