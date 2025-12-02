<?php

namespace App\Http\Livewire\Groups;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use DateTimeInterface;

class GroupEdit extends Component
{
    public Group $group;

    // Properti yang disinkronkan dengan formulir
    public $name;
    public $description; 
    public $quota;
    public $frequency; // -> payment_frequency
    public $fee_amount; // -> fee_per_member
    public $group_pot; // KRITIS: Tambahkan properti untuk menyimpan nilai hitungan
    public $start_date; 
    
    public $is_authorized = false;
    public $group_is_active = false;

    /**
     * Helper untuk mengecek apakah user adalah Super Admin (ID 1).
     */
    private function isSuperAdmin(): bool
    {
        return Auth::id() === 1;
    }

    /**
     * Mount component dan lakukan otorisasi.
     */
    public function mount(Group $group)
    {
        $this->group = $group;
        $userId = Auth::id();

        // --- Perubahan Otorisasi di sini ---
        // Otorisasi: Izinkan jika Owner ID cocok ATAU jika user adalah Super Admin (ID 1)
        if ($userId !== $this->group->owner_id && !$this->isSuperAdmin()) {
            
            // Tambahan: Cek juga apakah user adalah Admin/Bendahara member, 
            // agar mereka juga bisa mengedit properti non-kunci jika grup belum aktif.
            $isManager = $group->members->contains(function ($member) use ($userId) {
                return $member->user_id === $userId && in_array($member->role, ['admin', 'bendahara']);
            });

            if (!$isManager) {
                $this->is_authorized = false;
                session()->flash('error', 'Anda tidak memiliki izin untuk mengedit grup ini.');
                // Jika tidak diizinkan, keluar lebih awal
                return;
            }
        }
        // --- Akhir Perubahan Otorisasi ---

        $this->is_authorized = true;
        $this->group_is_active = $group->status === 'active';

        // Pre-populate data dari model Group
        $this->name = $group->name;
        $this->description = $group->description;
        $this->quota = $group->quota;
        $this->frequency = $group->payment_frequency;
        $this->fee_amount = $group->fee_per_member;
        $this->group_pot = $group->group_pot; // Inisialisasi Pot Arisan
        
        // Penanganan Tanggal
        if ($group->start_date instanceof \DateTimeInterface) {
            $this->start_date = $group->start_date->format('Y-m-d');
        } else {
            $this->start_date = ''; 
        }
    }

    /**
     * Hitung ulang group_pot.
     */
    protected function calculateGroupPot()
    {
        // Hanya hitung ulang jika grup belum aktif (karena hanya saat itu nilai boleh diubah)
        if (!$this->group_is_active) {
            $quota = intval($this->quota);
            $fee = floatval($this->fee_amount);
            
            // Pastikan nilai minimal kuota adalah 2
            $validQuota = max(2, $quota); 
            
            $this->group_pot = $validQuota * $fee;
        }
    }

    /**
     * Listener untuk update properti.
     */
    public function updated($propertyName)
    {
        // Hanya hitung ulang jika grup belum aktif DAN properti yang diubah adalah quota atau fee
        if (!$this->group_is_active && in_array($propertyName, ['quota', 'fee_amount'])) {
            $this->calculateGroupPot();
        }
    }

    /**
     * Aturan validasi (dinamis berdasarkan status grup)
     */
    protected function rules()
    {
        $commonRules = [
            'name' => 'required|string|min:5|max:100',
            'description' => 'nullable|string|max:255', 
        ];

        // Dapatkan nilai start_date yang diformat dari model (atau dari properti Livewire)
        $startDateValue = null;
        if ($this->group->start_date && ($this->group->start_date instanceof \DateTimeInterface)) {
            $startDateValue = $this->group->start_date->format('Y-m-d');
        }

        if ($this->group_is_active) {
            return array_merge($commonRules, [
                // Kunci semua nilai yang mempengaruhi perhitungan saat aktif.
                // Note: Super Admin tetap terikat aturan validasi ini, tapi mereka lolos otorisasi mount().
                'quota' => ['required', 'integer', Rule::in([$this->group->quota])],
                'frequency' => ['required', Rule::in([$this->group->payment_frequency])], 
                'fee_amount' => ['required', 'numeric', Rule::in([$this->group->fee_per_member])], 
                'group_pot' => ['required', 'numeric', Rule::in([$this->group->group_pot])],
                'start_date' => ['nullable', 'date', Rule::in([$startDateValue])], 
            ]);
        } else {
            // Jika belum aktif, tambahkan aturan validasi standar, termasuk group_pot
            return array_merge($commonRules, [
                'quota' => 'required|integer|min:2|max:30',
                'frequency' => 'required|in:weekly,monthly,bi-weekly', 
                'fee_amount' => 'required|numeric|min:1000|max:10000000', 
                'group_pot' => 'required|integer|min:100000|max:100000000', // Wajib ada
                'start_date' => 'nullable|date|after_or_equal:today', 
            ]);
        }
    }

    /**
     * Simpan perubahan grup.
     */
    public function updateGroup()
    {
        // --- Perubahan Otorisasi di sini ---
        // Otorisasi: Izinkan jika is_authorized TRUE ATAU jika Super Admin (ID 1)
        if (!$this->is_authorized && !$this->isSuperAdmin()) {
            // Karena otorisasi sudah di cek di mount, ini hanya cek terakhir.
            session()->flash('error', 'Akses ditolak saat menyimpan.');
            return; 
        }
        // --- Akhir Perubahan Otorisasi ---


        // KRITIS: Lakukan perhitungan pot terakhir sebelum validasi (hanya jika belum aktif)
        if (!$this->group_is_active) {
            $this->calculateGroupPot();
        }
        
        $this->validate();

        try {
            // Update grup
            $this->group->update([
                'name' => $this->name,
                'description' => $this->description, 
                // Catatan: Jika grup aktif, nilai-nilai di bawah ini (quota, freq, fee_amount, group_pot)
                // akan dikunci oleh aturan validasi `Rule::in()`, sehingga hanya name/description yang dapat diubah.
                'quota' => $this->quota,
                'payment_frequency' => $this->frequency,
                'fee_per_member' => $this->fee_amount,
                'group_pot' => $this->group_pot,
                'start_date' => $this->start_date ?: $this->group->start_date, 
            ]);

            session()->flash('success', 'Detail Grup ' . $this->name . ' berhasil diperbarui!');
            
            // Redirect Livewire
            return $this->redirectRoute('groups.index', navigate: true); 

        } catch (\Exception $e) {
            \Log::error('Update Group Error: ' . $e->getMessage(), ['group_id' => $this->group->id, 'user_id' => Auth::id()]);
            session()->flash('error', 'Gagal memperbarui grup. Silakan coba lagi. (Kode Error: ' . $e->getMessage() . ')');
        }
    }

    public function render()
    {

        return view('groups.edit')->layout('layouts.app');
    }
}