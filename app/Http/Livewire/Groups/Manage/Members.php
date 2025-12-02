<?php

namespace App\Http\Livewire\Groups\Manage;

use App\Models\Group;
use App\Models\GroupMember;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Members extends Component
{
    // Properti publik diisi dari rute atau parent component
    public Group $group;

    // Data anggota
    public Collection $approvedMembers; // Menggunakan Collection untuk type hinting yang lebih baik
    public Collection $pendingMembers; // Menggunakan Collection untuk type hinting yang lebih baik

    // Status Otorisasi (Bendahara atau Admin)
    public bool $isAuthorized = false;
    
    // Properti publik baru untuk menyimpan peran spesifik pengguna saat ini
    public ?string $currentUserGroupRole = null;

    // Untuk notifikasi
    public ?array $message = null;

    // Inisialisasi properti koleksi di construct
    public function __construct()
    {
        $this->approvedMembers = collect();
        $this->pendingMembers = collect();
    }


    /**
     * Jalankan saat komponen diinisialisasi
     */
    public function mount(Group $group)
    {
        $this->group = $group;
        $this->loadGroupAndMembers();
    }

    /**
     * Memuat data grup dan anggota serta memeriksa otorisasi.
     */
    public function loadGroupAndMembers()
    {
        if (!$this->group) {
            $this->isAuthorized = false;
            return;
        }

        $userId = Auth::id();
        $this->isAuthorized = false; // Reset status otorisasi
        $this->currentUserGroupRole = null;

        // =========================================================
        // LOGIKA OTORISASI & SUPER ADMIN BYPASS
        // =========================================================
        $memberData = GroupMember::where('group_id', $this->group->id)
                             ->where('user_id', $userId)
                             ->where('status', 'approved')
                             ->first();

        // Cek jika user adalah Owner (berdasarkan Group::owner_id)
        if ($this->group->owner_id === $userId) {
            $this->isAuthorized = true;
            $this->currentUserGroupRole = 'owner';
        }
        
        // Cek jika user adalah Admin/Bendahara lokal
        if ($memberData && in_array($memberData->role, ['admin', 'bendahara'])) {
            $this->isAuthorized = true;
            $this->currentUserGroupRole = $memberData->role; 
        }

        // Cek jika user adalah Super Admin (ID 1)
        if ($userId === 1) {
            $this->isAuthorized = true;
            $this->currentUserGroupRole = 'super_admin'; 
        }


        // Jika tidak berwenang, berhenti di sini
        if (!$this->isAuthorized) {
            $this->approvedMembers = collect();
            $this->pendingMembers = collect();
            return;
        }

        // --- PENGAMBILAN DATA ANGGOTA (Hanya dijalankan jika Authorized) ---
        
        // Mengambil semua anggota yang disetujui (Approved)
        $this->approvedMembers = GroupMember::where('group_id', $this->group->id)
                                             ->where('status', 'approved')
                                             ->with('user')
                                             ->get();

        // Mengambil semua permintaan anggota yang tertunda (Pending)
        $this->pendingMembers = GroupMember::where('group_id', $this->group->id)
                                           ->where('status', 'pending')
                                           ->with('user')
                                           ->get();
    }
    
    /**
     * Fungsi untuk menyetujui anggota.
     * @param int $memberId ID dari GroupMember
     */
    public function approveMember(int $memberId)
    {
        if (!$this->isAuthorized) {
            $this->message = ['type' => 'error', 'text' => 'Akses Ditolak: Anda tidak memiliki izin untuk menyetujui anggota.'];
            return;
        }

        $member = GroupMember::find($memberId);
        
        if ($member && $member->group_id === $this->group->id && $member->status === 'pending') {
            
            // =========================================================
            // PERBAIKAN KRITIS: PENGECEKAN KUOTA ANGGOTA (Eksklusi Admin/Bendahara)
            // =========================================================
            $excludedRoles = ['admin', 'bendahara'];
            $quotaLimit = (int) $this->group->max_members; // Batas kuota (sesuai field yang digunakan di sini)
            
            // Hitung anggota yang BENAR-BENAR MENGKONSUMSI KUOTA 
            // (yaitu, anggota yang PERANNYA bukan admin atau bendahara)
            $quotaConsumingCount = $this->approvedMembers
                ->filter(fn($m) => !in_array($m->role, $excludedRoles))
                ->count();

            // Cek jika kuota adalah angka positif dan sudah terpenuhi
            // Kita menggunakan ">=" karena menyetujui anggota ini akan menambah count menjadi $quotaConsumingCount + 1
            if ($quotaLimit > 0 && $quotaConsumingCount >= $quotaLimit) {
                $this->message = [
                    'type' => 'error', 
                    'text' => "Gagal: Kuota anggota grup sudah penuh ({$quotaLimit} anggota biasa maksimum). Anda harus meningkatkan batas kuota atau menghapus anggota biasa yang ada."
                ];
                $this->loadGroupAndMembers(); 
                return;
            }
            // =========================================================

            // Mengambil nama user sebelum update untuk notifikasi yang lebih baik
            $userName = $member->user->name ?? 'Anggota';
            
            $member->update([
                'status' => 'approved',
                'role' => 'anggota', // Asumsikan peran default adalah 'anggota'
                'joined_at' => now(), // Catat tanggal bergabung
            ]);
            $this->message = ['type' => 'success', 'text' => "Anggota {$userName} berhasil disetujui."];
        } else {
            $this->message = ['type' => 'error', 'text' => 'Permintaan anggota tidak ditemukan atau sudah diproses.'];
        }
        
        $this->loadGroupAndMembers(); // Muat ulang data setelah perubahan
    }

    /**
     * Fungsi untuk menolak atau menghapus anggota.
     * @param int $memberId ID dari GroupMember
     */
    public function deleteMember(int $memberId)
    {
        if (!$this->isAuthorized) {
            $this->message = ['type' => 'error', 'text' => 'Akses Ditolak: Anda tidak memiliki izin untuk menghapus anggota.'];
            return;
        }

        $member = GroupMember::find($memberId);

        if ($member && $member->group_id === $this->group->id) {
            
            // Cek apakah pengguna mencoba menghapus dirinya sendiri jika dia admin/bendahara
            if ($member->user_id === Auth::id() && in_array($member->role, ['admin', 'bendahara'])) {
                 if (Auth::id() !== 1) { // Hanya blokir jika BUKAN User ID 1 (Super Admin)
                     $this->message = ['type' => 'warning', 'text' => 'Anda tidak dapat menghapus diri sendiri saat Anda adalah admin atau bendahara grup.'];
                     return;
                 }
            }
            
            $userName = $member->user->name ?? 'Anggota';
            $member->delete();
            $this->message = ['type' => 'success', 'text' => "Anggota {$userName} berhasil dihapus."];
        } else {
            $this->message = ['type' => 'error', 'text' => 'Anggota tidak ditemukan.'];
        }
        
        $this->loadGroupAndMembers(); // Muat ulang data
    }
    
    /**
     * Render komponen.
     */
    public function render()
    {
        return view('groups.manage.members')->layout('layouts.app');
    }
}