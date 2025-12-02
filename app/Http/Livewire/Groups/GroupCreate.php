<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupCreate extends Component
{
    // Properti untuk menyimpan input formulir
    public $name = '';
    public $description = ''; 
    public $quota = 5; // Default: 5
    public $fee_per_member = 20000; // Default: 20000 agar Group Pot minimal 100.000 saat load
    public $group_pot = 0; 
    public $payment_frequency = 'monthly'; 
    public $start_date = ''; 

    // Aturan validasi
    protected $rules = [
        'name' => 'required|string|min:5|max:100',
        'description' => 'nullable|string|max:255',
        'quota' => 'required|integer|min:2|max:30', 
        'fee_per_member' => 'required|numeric|min:1000|max:10000000', 
        // KRITIS: Perhitungan ini harus valid
        'group_pot' => 'required|integer|min:100000|max:100000000', 
        'payment_frequency' => 'required|in:weekly,monthly,bi-weekly', 
        'start_date' => 'nullable|date|after:today',
    ];

    /**
     * Dijalankan saat komponen dimuat. Menghitung nilai awal $group_pot.
     */
    public function mount()
    {
        // Memastikan pot arisan dihitung saat komponen pertama kali dimuat
        $this->calculateGroupPot();
    }

    /**
     * Dijalankan saat properti di-update dari form (karena wire:model.live).
     */
    public function updated($propertyName)
    {
        // KRITIS: Jika quota atau fee_per_member berubah, hitung ulang pot arisan
        if (in_array($propertyName, ['quota', 'fee_per_member'])) {
            $this->calculateGroupPot();
        }
    }

    /**
     * Fungsi untuk menghitung total pot arisan (quota * fee_per_member).
     */
    public function calculateGroupPot()
    {
        // Menggunakan intval/floatval untuk konversi aman dari input yang mungkin kosong
        $quota = intval($this->quota);
        $fee = floatval($this->fee_per_member);
        
        // Pastikan kuota minimal adalah 2 untuk perhitungan
        $validQuota = max(2, $quota); 
        
        // Hitung Pot Arisan dan set properti
        $this->group_pot = $validQuota * $fee;
    }

    /**
     * Metode yang dipanggil saat formulir disubmit.
     */
    public function save()
    {
        // KRITIS: Pastikan perhitungan terakhir terjadi sebelum validasi
        $this->calculateGroupPot();
        
        // Jalankan validasi
        $this->validate();

        // Menggunakan transaksi untuk memastikan atomisitas
        DB::beginTransaction();

        try {
            // 1. Buat grup baru
            $group = Group::create([
                'name' => $this->name,
                'description' => $this->description,
                'owner_id' => Auth::id(), 
                'quota' => $this->quota,
                'fee_per_member' => $this->fee_per_member, 
                'group_pot' => $this->group_pot, 
                'payment_frequency' => $this->payment_frequency, 
                'start_date' => $this->start_date ?: now(), 
                // PERBAIKAN: Kembali ke 'pending' karena grup belum penuh/dimulai
                'status' => 'pending', 
            ]);

            // 2. Tambahkan pembuat grup sebagai anggota pertama (admin)
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => Auth::id(), 
                // Status anggota pemilik tetap 'approved'
                'status' => 'approved', 
                'role' => 'admin', 
            ]);

            DB::commit(); // Komit transaksi jika berhasil

            // Reset hanya properti form setelah penyimpanan berhasil
            $this->reset(['name', 'description', 'quota', 'fee_per_member', 'payment_frequency', 'start_date']);
            
            // Panggil calculateGroupPot lagi untuk menginisialisasi ulang $group_pot setelah reset
            $this->calculateGroupPot(); 
            
            // Kirim notifikasi sukses ke sesi
            session()->flash('success', 'Grup Arisan ' . $group->name . ' berhasil dibuat! Anda sekarang adalah admin grup.');

            // Menggunakan fungsi bawaan Livewire untuk redirect
            return $this->redirectRoute('groups.index', ['group' => $group->id], navigate: true);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error
            
            \Log::error('Gagal membuat grup (Livewire): ' . $e->getMessage());
            
            // Tangani error dan kirim notifikasi error
            session()->flash('error', 'Kesalahan! Gagal membuat grup: ' . $e->getMessage());
        }
    }

    /**
     * Render tampilan.
     */
    public function render()
    {
        return view('groups.create')->layout('layouts.app'); 
    }
}