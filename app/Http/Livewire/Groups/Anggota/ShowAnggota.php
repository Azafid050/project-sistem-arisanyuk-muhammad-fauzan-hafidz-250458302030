<?php

namespace App\Http\Livewire\Groups\Anggota;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use DateTimeInterface;

class ShowAnggota extends Component
{
    public Group $group;

    // Data yang akan ditampilkan
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
    public $current_user_is_bendahara = false; // Mengubah dari is_owner menjadi is_bendahara

    /**
     * Mount component dan load data.
     */
    public function mount(Group $group)
    {
        $this->group = $group;

        // Cek apakah user saat ini adalah pemilik/bendahara grup
        $this->current_user_is_bendahara = (Auth::id() === $this->group->owner_id);
        
        $this->is_authorized = true; 
        
        // Tentukan status aktif grup
        $this->group_is_active = $group->status === 'active';

        // Pre-populate data dari model Group
        $this->name = $group->name;
        $this->description = $group->description;
        $this->quota = $group->quota;
        $this->frequency = $group->payment_frequency;
        $this->fee_amount = $group->fee_per_member;
        $this->group_pot = $group->group_pot; 
        
        // Penanganan Tanggal
        if ($group->start_date instanceof \DateTimeInterface) {
            $this->start_date = $group->start_date->format('Y-m-d');
        } else {
            $this->start_date = $group->start_date; 
        }

        // Jika group_pot belum terhitung (misal: grup baru dibuat), hitung sekarang.
        if (empty($this->group_pot)) {
            $this->calculateGroupPot();
        }
    }

    /**
     * Hitung ulang group_pot (hanya untuk memastikan nilai ditampilkan).
     */
    protected function calculateGroupPot()
    {
        $quota = intval($this->quota);
        $fee = floatval($this->fee_amount);
        
        // Pastikan nilai minimal kuota adalah 2 (atau sesuai aturan bisnis Anda)
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
     * Ini adalah method yang dicari oleh view.
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
        return view('groups.anggota.show_anggota')->layout('layouts.app');
    }
}