<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupShow extends Component
{
    /**
     * ID pengguna yang bertindak sebagai Admin Global (misalnya, ID user "Gemini").
     * GANTI NILAI '1' INI DENGAN ID USER ADMIN YANG SEBENARNYA DI DATABASE ANDA.
     */
    protected static $globalAdminId = 1; // <-- PENTING: Ganti dengan ID Admin yang sesuai

    // Properties untuk Route Model Binding dan Data Utama
    public Group $group;
    public $name;
    public $description; 
    public $quota;
    public $frequency;
    public $fee_amount;
    public $group_pot;
    public $start_date;
    
    // Status Tampilan
    public $is_authorized = false; 
    public $group_is_active = false;
    public $current_user_is_owner = false; 
    
    // Data Keanggotaan yang baru ditambahkan
    public $is_member = false;
    public $members = [];
    public $member_count = 0;
    
    // Listener: Menerima event dari komponen anak setelah Join/Leave
    protected $listeners = ['membershipUpdated' => 'loadMembershipData'];

    /**
     * Mount component dan load data awal.
     */
    public function mount(Group $group)
    {
        $this->group = $group;

        // Pre-populate data dari model Group
        $this->name = $group->name;
        $this->description = $group->description;
        $this->quota = $group->quota;
        $this->frequency = $group->payment_frequency;
        $this->fee_amount = $group->fee_per_member;
        $this->group_pot = $group->group_pot; 
        $this->group_is_active = $group->status === 'active';
        $this->start_date = $group->start_date instanceof \DateTimeInterface ? $group->start_date->format('Y-m-d') : $group->start_date;

        // Memuat semua data keanggotaan
        $this->loadMembershipData();
        
        if (empty($this->group_pot)) {
            $this->calculateGroupPot();
        }
    }
    
    /**
     * Memuat dan menetapkan data keanggotaan, dipanggil saat mount dan setelah ada update.
     */
    public function loadMembershipData()
    {
        // RELOAD GROUP DENGAN MEMBER BARU JIKA TERJADI PERUBAHAN
        $this->group->load('members');
        
        $user = Auth::user();
        $is_authenticated = !empty($user);
        
        if ($is_authenticated) {
            // Logika baru: Cek apakah user adalah pemilik sebenarnya ATAU admin global
            $isActualOwner = ($user->id === $this->group->owner_id);
            $isGlobalAdmin = ($user->id === self::$globalAdminId);

            // Jika pemilik sebenarnya ATAU Admin Global, maka dianggap sebagai pemilik
            $this->current_user_is_owner = $isActualOwner || $isGlobalAdmin;
            
            // Cek apakah user saat ini adalah anggota grup
            // Catatan: Admin global tidak otomatis ditambahkan sebagai "anggota" kecuali mereka join
            // Namun untuk tujuan tampilan, kita bisa asumsikan Admin punya otorisasi penuh.
            $this->is_member = $this->group->members->contains($user->id);
            $this->is_authorized = true; 
        } else {
            // Pengguna tidak terautentikasi (Guest)
            $this->current_user_is_owner = false;
            $this->is_member = false;
            $this->is_authorized = false;
        }

        // Hitung jumlah anggota
        $this->member_count = $this->group->members->count();

        // Jika user adalah pemilik (termasuk Admin Global), muat data anggota lengkap untuk tampilan admin
        if ($this->current_user_is_owner) {
            $this->members = $this->group->members->map(function ($member) use ($user) {
                // Tambahkan label "Anda/Pemilik"
                $name_display = $member->name;
                if ($member->id === $user->id) {
                    $name_display .= ' (Anda/Pemilik)';
                }
                return [
                    'id' => $member->id,
                    'name' => $name_display,
                    'email' => $member->email,
                ];
            })->toArray();
        }
    }

    /**
     * Hitung ulang group_pot.
     */
    protected function calculateGroupPot()
    {
        $quota = intval($this->quota);
        $fee = floatval($this->fee_amount);
        
        $validQuota = max(2, $quota); 
        
        $this->group_pot = $validQuota * $fee;
    }

    /**
     * Helper untuk format Rupiah.
     */
    public function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    /**
     * Helper untuk mapping Frekuensi.
     */
    public function getFrequencyText()
    {
        $map = [
            'weekly' => 'Minggu',
            'bi-weekly' => '2 Minggu',
            'monthly' => 'Bulan',
        ];
        return $map[$this->frequency] ?? 'Periode';
    }

    public function render()
    {
        return view('groups.show')->layout('layouts.app');
    }
}