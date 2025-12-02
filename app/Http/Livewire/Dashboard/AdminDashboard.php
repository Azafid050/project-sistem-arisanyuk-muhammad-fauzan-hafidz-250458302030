<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Group;
use App\Models\Round;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; // Import Carbon untuk manipulasi tanggal

/**
 * Komponen Livewire untuk Dashboard Administrator.
 * Menyediakan data ringkasan dan data untuk grafik.
 */
class AdminDashboard extends Component
{
    // Properti untuk menyimpan data ringkasan
    public $summaryData = [];

    // Properti untuk data grafik
    public $chartData = [];

    // Menggunakan konstanta lokal untuk status
    const PAYMENT_STATUS_VERIFIED = 'verified';
    const PAYMENT_STATUS_PENDING = 'pending'; // Status Pembayaran Menunggu Verifikasi
    const GROUP_STATUS_PENDING = 'pending';   // Status Grup Menunggu Kuota/Aktivasi
    const STATUS_COMPLETED = 'completed';     // Status Selesai (untuk Grup dan Putaran)

    public function mount()
    {
        // Memuat data ringkasan metrik
        $this->loadSummaryData();

        // Memuat data untuk grafik (DIREVISI: Sekarang memuat data pertumbuhan anggota dan putaran selesai yang AKTUAL)
        $this->loadChartData();
    }

    /**
     * Mengambil data ringkasan utama dari database.
     */
    protected function loadSummaryData()
    {
        // Mengambil data metrik dari database
        $this->summaryData = [
            'total_anggota' => User::where('role', 'anggota')->count(),
            'total_bendahara' => User::where('role', 'bendahara')->count(),
            'total_groups' => Group::count(),
            'total_rounds' => Round::count(), // Total Putaran Arisan (Global)

            // Mengambil nilai dan jumlah transaksi yang sudah diverifikasi
            'total_payments_value' => Payment::where('status', self::PAYMENT_STATUS_VERIFIED)->sum('amount') ?? 0,
            'total_payments_count' => Payment::where('status', self::PAYMENT_STATUS_VERIFIED)->count(),
            
            // --- METRIK BARU BERDASARKAN PERMINTAAN PENGGUNA ---
            // Pembayaran Menunggu Verifikasi
            'payments_pending_count' => Payment::where('status', self::PAYMENT_STATUS_PENDING)->count(),
            
            // Grup Menunggu Kuota/Aktivasi
            'groups_pending_count' => Group::where('status', self::GROUP_STATUS_PENDING)->count(),
            
            // Total Grup Selesai
            'groups_completed_count' => Group::where('status', self::STATUS_COMPLETED)->count(),
            
            // Total Putaran Selesai
            'rounds_completed_count' => Round::where('status', self::STATUS_COMPLETED)->count(),
        ];
    }

    /**
     * Menyiapkan data untuk grafik.
     * Termasuk Pertumbuhan Anggota Bulanan dan Putaran Selesai Bulanan (6 bulan terakhir).
     *
     * CATATAN: Kueri ini mengasumsikan kolom `created_at` ada di tabel `users` dan 
     * `status` 'completed' ditandai oleh `updated_at` di tabel `rounds`.
     */
    protected function loadChartData()
    {
        $months = [];
        $anggotaCounts = [];
        $roundsCompletedCounts = [];

        // Ambil 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::now()->subMonths($i)->endOfMonth();

            // Format label bulan (misalnya: "Nov")
            $months[] = $startOfMonth->translatedFormat('M');

            // 1. Hitung jumlah anggota baru
            $anggotaCounts[] = User::where('role', 'anggota')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
            
            // 2. Hitung jumlah putaran selesai
            $roundsCompletedCounts[] = Round::where('status', self::STATUS_COMPLETED)
                // Asumsi: Gunakan `updated_at` untuk menandai kapan status menjadi 'completed'
                ->whereBetween('updated_at', [$startOfMonth, $endOfMonth]) 
                ->count();
        }

        $this->chartData = [
            'labels' => $months, // Label bulan yang dinamis
            'anggotaCount' => $anggotaCounts, // Jumlah anggota baru per bulan (Grafik Pertumbuhan Anggota)
            'roundsCompletedCount' => $roundsCompletedCounts, // Jumlah putaran selesai per bulan (Grafik Putaran Selesai)
        ];
    }

    public function render()
{
    // 🔥 Tambahkan baris ini
    $this->dispatch('admin-dashboard-rendered');

    return view('dashboard.admin')
        ->layout('layouts.app', ['header' => 'Admin Panel Utama']);
}
}