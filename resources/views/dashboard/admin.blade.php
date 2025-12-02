<div class="space-y-8 p-4 sm:p-6 lg:p-8">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Dashboard Administrasi
        </h1>
        <p class="mt-2 text-sm text-gray-500 sm:mt-0">
            Ringkasan data ArisanYuk terkini.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        
        <div class="bg-white overflow-hidden shadow-lg rounded-xl transition duration-300 hover:shadow-xl border border-indigo-100">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Grup Arisan</dt>
                            <dd class="text-3xl font-bold text-gray-900">
                                {{ number_format($summaryData['total_groups'] ?? 0, 0, ',', '.') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl transition duration-300 hover:shadow-xl border border-green-100">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.573-.131-1.14-.38-1.656-1.042-2.193-3.655-3.056-5.856-3.056H4a3 3 0 00-3 3v2h5m-2-7a4 4 0 110-8 4 4 0 010 8zM12 4a4 4 0 100 8 4 4 0 000-8z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Anggota Terdaftar</dt>
                            <dd class="text-3xl font-bold text-gray-900">
                                {{ number_format($summaryData['total_anggota'] ?? 0, 0, ',', '.') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl transition duration-300 hover:shadow-xl border border-orange-100">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2v1h6v-1c0-1.105-1.343-2-3-2zM4 17h16V9H4v8zM3 7h18a1 1 0 011 1v12a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Iuran Terverifikasi</dt>
                            <dd class="text-2xl font-bold text-gray-900 mt-1">
                                Rp{{ number_format($summaryData['total_payments_value'] ?? 0, 0, ',', '.') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl transition duration-300 hover:shadow-xl border border-blue-100">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Jumlah Transaksi (Verified)</dt>
                            <dd class="text-3xl font-bold text-gray-900">
                                {{ number_format($summaryData['total_payments_count'] ?? 0, 0, ',', '.') }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 bg-white shadow-lg rounded-xl p-6 border border-gray-200 flex flex-col items-center">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Distribusi Total Metrik Kunci</h2>
            <p class="text-sm text-gray-500 mb-4">Perbandingan antara Grup Selesai, Putaran Selesai, Bendahara, dan Total Putaran Global.</p>
            
            <div class="h-96 w-full max-w-lg flex items-center justify-center">
                <canvas id="mainDashboardChart"></canvas>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Aktivitas dan Tugas Cepat</h2>
            <ul class="space-y-4">
                <li class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-orange-700 bg-orange-100 rounded-full">
                        {{ number_format($summaryData['payments_pending_count'] ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-700 font-semibold">Pembayaran Menunggu Verifikasi</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-red-700 bg-red-100 rounded-full">
                        {{ number_format($summaryData['groups_pending_count'] ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-700 font-semibold">Grup Menunggu Kuota/Aktivasi</span>
                </li>
                <hr class="border-gray-100">
                <li class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-sky-700 bg-sky-100 rounded-full">
                        {{ number_format($summaryData['groups_completed_count'] ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-700">Total Grup Selesai</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-teal-700 bg-teal-100 rounded-full">
                        {{ number_format($summaryData['rounds_completed_count'] ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-700">Total Putaran Selesai</span>
                </li>
                <hr class="border-gray-100">
                <li class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-purple-700 bg-purple-100 rounded-full">
                        {{ number_format($summaryData['total_bendahara'] ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-700">Total Bendahara (Pengawas)</span>
                </li>
                <li class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium leading-4 text-indigo-700 bg-indigo-100 rounded-full">
                        {{ number_format($summaryData['total_rounds'] ?? 0, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-700">Total Putaran Arisan (Global)</span>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- SCRIPT DENGAN LISTENER LIVEWIRE YANG TEPAT --}}
<script>
    // Variabel global untuk menyimpan instance Chart agar bisa di-destroy dan dibuat ulang
    let mainDashboardChartInstance = null; 

    // Fungsi inisialisasi/render chart
    function initMainDashboardChart(summaryData) {
        const ctx = document.getElementById('mainDashboardChart');
        if (!ctx) return;

        // **1. Hancurkan instance chart yang sudah ada (jika ada)**
        if (mainDashboardChartInstance) {
            mainDashboardChartInstance.destroy();
            mainDashboardChartInstance = null;
        }

        const chartLabels = [
            'Grup Selesai',
            'Putaran Selesai',
            'Total Bendahara',
            'Total Putaran Global'
        ];
        
        const chartDataValues = [
            summaryData.groups_completed_count || 0,
            summaryData.rounds_completed_count || 0,
            summaryData.total_bendahara || 0,
            summaryData.total_rounds || 0,
        ];

        // Hanya buat chart jika ada data > 0
        if (chartDataValues.some(val => val > 0)) {
            mainDashboardChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartDataValues,
                        backgroundColor: [
                            'rgba(6, 182, 212, 0.8)', // sky-500
                            'rgba(20, 184, 166, 0.8)', // teal-500
                            'rgba(168, 85, 247, 0.8)', // purple-500
                            'rgba(99, 102, 241, 0.8)', // indigo-500
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 16
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total ? `(${(context.parsed / total * 100).toFixed(1)}%)` : '';
                                    return `${label}: ${context.parsed} ${percentage}`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Tampilkan pesan jika tidak ada data
            const chartContainer = ctx.parentNode;
            chartContainer.innerHTML = '<p class="text-gray-500 mt-20">Tidak ada data untuk ditampilkan.</p>';
            chartContainer.classList.add('flex', 'items-center', 'justify-center');
            chartContainer.style.height = '384px';
        }
    }

    // **2. Tangkap event yang di-dispatch oleh Livewire (saat komponen selesai dirender)**
    document.addEventListener('admin-dashboard-rendered', function () {
        const summaryData = @json($summaryData); 
        initMainDashboardChart(summaryData);
    });

    // **3. Jalankan saat DOMContentLoaded (Untuk inisialisasi pertama kali saat halaman dimuat)**
    document.addEventListener('DOMContentLoaded', function () {
        const summaryData = @json($summaryData);
        initMainDashboardChart(summaryData);
    });
</script>