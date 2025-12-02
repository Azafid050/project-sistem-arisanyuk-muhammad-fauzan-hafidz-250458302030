<?php

namespace App\Http\Livewire\GroupMembers;

use App\Models\GroupMember;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinGroup extends Component
{
    // Properti WAJIB: Menerima ID Grup
    public $groupId;

    // Status dinamis: none, pending, approved, rejected
    public $memberStatus = 'none';
    
    // Properti BARU: Menyimpan peran (role) anggota (anggota, bendahara, admin, none)
    public $memberRole = 'none';

    // Objek Grup untuk pemeriksaan owner dan aktivasi
    public $group;
    
    // Properti BARU: Status Kuota Penuh (akan dihitung saat load)
    public $isGroupFull = false; 

    // Menampung pesan jika grup baru saja diaktifkan
    public $activationMessage = ''; 

    public function getIsUserAdminOrOwnerProperty()
    {
        if (!Auth::check() || !$this->group) {
            return false;
        }

        $userId = Auth::id();
        
        // Cek jika pengguna adalah Owner grup (Owner selalu memiliki hak kelola)
        if ($this->group->owner_id === $userId) {
            return true;
        }

        // Cek jika pengguna memiliki role 'bendahara' atau 'admin'
        if (in_array($this->memberRole, ['bendahara', 'admin'])) {
            return true;
        }

        return false;
    }

    public function mount($groupId)
    {
        $this->groupId = $groupId;
        // Memuat objek Group saat mount
        $this->group = Group::find($groupId); 

        // Guard: Jika grup tidak ditemukan
        if (!$this->group) {
            $this->groupId = null;
            return;
        }

        // PENTING: Periksa status penuh saat pertama kali dimuat
        $this->checkGroupFullness(); 
        
        if (Auth::check()) {
            $userId = Auth::id();
            
            // Cek apakah user saat ini sudah memiliki relasi
            $member = GroupMember::where('group_id', $this->groupId)
                ->where('user_id', $userId)
                ->first();

            if ($member) {
                // Set status sesuai yang ada di database ('pending' atau 'approved')
                $this->memberStatus = $member->status;
                // Set role (PENTING untuk admin check)
                $this->memberRole = $member->role;
            }

            // PERBAIKAN ROBUSTNESS: Jika pengguna adalah owner
            if ($this->group->owner_id === $userId) {
                // Asumsi: Owner juga punya peran bendahara jika tidak ada entri GroupMember khusus
                $this->memberRole = $member ? $member->role : 'bendahara'; 
                $this->memberStatus = 'approved'; 
            }
        }
    }

    protected function ensureGroupIsLoaded()
    {
        if (empty($this->groupId)) {
            session()->flash('error', 'Error Livewire: ID Grup tidak terdeteksi.');
            return false;
        }

        if (!$this->group || $this->group->id != $this->groupId) {
            $this->group = Group::find($this->groupId);
        }

        if (!$this->group) {
            session()->flash('error', 'Grup tidak ditemukan.');
            return false;
        }
        
        // PENTING: Periksa status penuh sebelum menjalankan aksi
        $this->checkGroupFullness(); 
        
        return true;
    }
    
    /**
     * FUNGSI: Menghitung anggota yang disetujui dan memperbarui properti isGroupFull.
     * MENGGUNAKAN LOGIKA KUOTA BARU DARI MODEL GROUP.
     */
    protected function checkGroupFullness()
    {
        if ($this->group) {
            // ** PERBAIKAN KRITIS DI SINI: Gunakan getQuotaMembersCount() **
            $quotaMemberCount = $this->group->getQuotaMembersCount(); 
            $this->isGroupFull = $quotaMemberCount >= $this->group->quota;
        } else {
            $this->isGroupFull = false;
        }
    }


    public function joinGroup()
    {
        if (!$this->ensureGroupIsLoaded()) {
            return;
        }
        
        // --- Perbaikan Robustness/Klarifikasi ---
        // 1. Refresh group untuk mengambil status 'active' terbaru dari DB (jika diaktifkan oleh user lain).
        $this->group->refresh(); 
        $this->checkGroupFullness(); 
        // ----------------------------------------

        if (!Auth::check()) {
            session()->flash('error', 'Silakan login untuk bergabung ke grup.');
            return;
        }
        
        // LOGIKA KRITIS: Jika grup sudah penuh, tidak bisa bergabung
        if ($this->isGroupFull) {
            // ** PESAN ERROR HARUS MENGGUNAKAN KUOTA ANGGOTA YANG BENAR **
            $count = $this->group->getQuotaMembersCount();
            if ($this->group->status === 'active') {
                session()->flash('error', 'Grup ini sudah aktif dan mencapai kuota (' . $count . '/' . $this->group->quota . ' anggota kuota). Pendaftaran ditutup.');
            } else {
                session()->flash('error', 'Grup ini sudah mencapai kuota (' . $count . '/' . $this->group->quota . ' anggota kuota). Pendaftaran ditutup.');
            }
            return;
        }

        if ($this->memberStatus !== 'none') {
             session()->flash('error', 'Anda sudah mendaftar di grup ini.');
            return;
        }

        $userId = Auth::id();
        $isOwner = $this->group->owner_id === $userId;

        // Tentukan status dan role berdasarkan apakah pengguna adalah owner
        if ($isOwner) {
            $status = 'approved';
            $role = 'bendahara';
            $message = 'Anda telah bergabung sebagai Bendahara dan status Anda otomatis disetujui.';
        } else {
            $status = 'pending';
            // PERBAIKAN KRITIS DI SINI: Kembali menggunakan 'anggota' sesuai skema ENUM database
            $role = 'anggota'; 
            $message = 'Permintaan Anda untuk bergabung telah dikirim. Menunggu persetujuan Admin/Bendahara.';
        }

        try {
            // Buat entri baru di tabel group_members
            GroupMember::create([
                'group_id' => $this->groupId,
                'user_id' => $userId,
                'status' => $status, 
                'role' => $role,
                'joined_at' => $status === 'approved' ? now() : null, // Hanya isi joined_at jika status langsung approved
            ]);
            
            // Perbarui status tampilan
            $this->memberStatus = $status;
            $this->memberRole = $role;

            session()->flash('success', $message);

            $this->dispatch('membershipUpdated');
            
            // JIKA KEANGGOTAAN DISETUJUI (TERUTAMA OWNER), CEK DAN AKTIFKAN GRUP
            if ($status === 'approved') {
                $activated = $this->group->checkAndActivate(); 
                
                // PENTING: Muat ulang objek grup dan status penuh setelah aktivasi
                $this->group->refresh();
                $this->checkGroupFullness(); 
                
                if ($activated) {
                    session()->flash('success', $message . ' Grup berhasil diaktifkan dan putaran arisan telah dibuat!');
                }
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses permintaan: ' . $e->getMessage());
        }
    }
    
    public function leaveGroup()
    {
        if (!$this->ensureGroupIsLoaded()) {
            return;
        }

        if (!Auth::check()) {
             return; 
        }
        
        $userId = Auth::id();
        
        if ($this->group->owner_id === $userId && $this->memberStatus === 'approved') {
            // ** PERBAIKAN DI SINI: Gunakan getQuotaMembersCount() **
            $quotaMemberCount = $this->group->getQuotaMembersCount();
            if ($quotaMemberCount > 0 || $this->group->status !== 'pending') {
                 session()->flash('error', 'Anda adalah pemilik/bendahara. Anda tidak dapat meninggalkan grup ini saat sudah aktif atau memiliki anggota kuota yang disetujui.');
                 return;
             }
        }

        $oldStatus = $this->memberStatus;

        // Hapus entri GroupMember yang relevan
        $deleted = GroupMember::where('group_id', $this->groupId)
            ->where('user_id', $userId)
            ->delete();

        if ($deleted) {
            $this->memberStatus = 'none';
            $this->memberRole = 'none';
            
            $defaultMessage = 'Anda berhasil membatalkan permintaan atau meninggalkan grup.';
            session()->flash('info', $defaultMessage);
            
            // --- Perbaikan Robustness ---
            $this->group->refresh(); 
            $this->checkGroupFullness(); 
            // ---------------------------

            // LOGIKA DEAKTIVASI GRUP JIKA KEHILANGAN ANGGOTA APPROVED 
            if ($oldStatus === 'approved' && $this->group->status === 'active') {
                // ** PERBAIKAN DI SINI: Gunakan getQuotaMembersCount() **
                $quotaMemberCount = $this->group->getQuotaMembersCount();
                
                if ($quotaMemberCount < $this->group->quota) {
                    $this->group->status = 'pending';
                    $this->group->save();
                    
                    session()->flash('info', 'Anda berhasil meninggalkan grup. PERHATIAN: Grup telah dinonaktifkan sementara (Status: Pending) karena jumlah anggota kuota (' . $quotaMemberCount . ') tidak memenuhi kuota (' . $this->group->quota . ').');
                }
            }

            $this->dispatch('membershipUpdated');

        } else {
            session()->flash('error', 'Gagal meninggalkan grup. Mungkin Anda belum terdaftar.');
        }
    }

    public function render()
    {
        return view('group_members.join');
    }
}