<?php

namespace App\Http\Livewire\Payments\Admin;

use Livewire\Component;
use App\Models\Group;
use App\Models\Payment;
use Illuminate\Support\Collection; // DITAMBAHKAN: Diperlukan untuk isNotEmpty() dan collect()

class IndexPaymentAdmin extends Component
{
    // Properti Publik
    public $groups = []; // Akan menampung semua grup, diisi di loadGroups()
    public $selectedGroupId;
    public $group;
    public $activeRound;
    public $payments = [];
    public $totalQuotaMembers = 0;
    public $verifiedPaymentsCount = 0;
    public $isReadyToDraw = false;

    /**
     * Inisialisasi komponen, memuat semua grup saat pertama kali dimuat.
     */
    public function mount()
    {
        // Inisialisasi sebagai Collection kosong
        if (!$this->groups instanceof Collection) {
            $this->groups = new Collection();
        }

        $this->loadGroups();
        if ($this->groups->isNotEmpty()) {
            // Pilih grup pertama secara default untuk menampilkan detail
            $this->selectGroup($this->groups->first()['id']);
        }
    }

    /**
     * Memuat daftar semua grup dan Bendahara yang bertanggung jawab.
     */
    public function loadGroups()
    {
        // Admin memuat semua grup. Kita perlu eager load anggota untuk menemukan Bendahara.
        $this->groups = Group::with([
            'groupMembers' => function ($query) {
                // PERBAIKAN: Menggunakan 'status' = 'approved' sesuai skema migrasi
                $query->where('role', 'bendahara')->where('status', 'approved')->with('user');
            },
            'activeRound', // Eager load putaran aktif
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($group) {
            $bendaharaMember = $group->groupMembers->first();
            // Ambil nama Bendahara, atau beri label jika belum ditunjuk
            $bendaharaName = $bendaharaMember->user->name ?? 'Belum Ditunjuk';
            
            $activeRound = $group->activeRound;
            
            // Hitung statistik verifikasi untuk tampilan daftar grup
            $verifiedPaymentsCount = 0;
            $totalMembersQuota = 0;
            $isFullyPaid = false;
            $isRoundActive = (bool)$activeRound;

            if ($activeRound) {
                // Ambil hitungan pembayaran untuk putaran aktif
                $verifiedPaymentsCount = Payment::where('round_id', $activeRound->id)
                    ->where('status', Payment::STATUS_VERIFIED)
                    ->count();
                
                // PERBAIKAN: Asumsi anggota yang wajib bayar adalah role 'anggota'
                $totalMembersQuota = $group->groupMembers()->where('role', 'anggota')->count();
                $isFullyPaid = $verifiedPaymentsCount >= $totalMembersQuota;
            }

            return [
                'id' => $group->id,
                'name' => $group->name,
                'bendahara_name' => $bendaharaName, // Data Bendahara untuk ditampilkan oleh Admin
                'is_fully_paid' => $isFullyPaid,
                'is_round_active' => $isRoundActive,
                'active_round_number' => $activeRound->round_number ?? null,
                'verified_payments_count' => $verifiedPaymentsCount,
                'total_members_quota' => $totalMembersQuota,
            ];
        });
    }

    /**
     * Memilih grup dan memuat detail putaran aktif dan pembayaran.
     */
    public function selectGroup($groupId)
    {
        $this->selectedGroupId = $groupId;
        $this->loadGroupDetails();
    }

    /**
     * Memuat detail pembayaran untuk grup yang dipilih dan putaran aktifnya.
     */
    public function loadGroupDetails()
    {
        if (!$this->selectedGroupId) {
            return;
        }

        $this->group = Group::with('groupMembers.user')->findOrFail($this->selectedGroupId);
        $this->activeRound = $this->group->activeRound;

        if ($this->activeRound) {
            $this->payments = Payment::with('groupMember.user')
                ->where('round_id', $this->activeRound->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $this->verifiedPaymentsCount = $this->payments->where('status', Payment::STATUS_VERIFIED)->count();
            // PERBAIKAN: Hitung total anggota yang seharusnya membayar (role 'anggota')
            $this->totalQuotaMembers = $this->group->groupMembers()->where('role', 'anggota')->count();
            $this->isReadyToDraw = $this->verifiedPaymentsCount >= $this->totalQuotaMembers;

        } else {
            // Reset detail jika tidak ada putaran aktif
            $this->payments = collect();
            $this->verifiedPaymentsCount = 0;
            $this->totalQuotaMembers = 0;
            $this->isReadyToDraw = false;
        }
    }

    public function render()
    {
        // Menggunakan view baru untuk Admin (Path ini dipertahankan sesuai permintaan Anda)
        return view('payments.admin.index_payment_admin')->layout('layouts.app');
    }
}