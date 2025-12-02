<?php

namespace App\Http\Livewire\Groups\Bendahara;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use DateTimeInterface;

class Edit extends Component
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
     * Mount component dan lakukan otorisasi (Memastikan pengguna adalah Bendahara).
     */
    public function mount(Group $group)
    {
        $this->group = $group;

        // Otorisasi KRITIS: Hanya Bendahara (owner_id) yang boleh mengedit.
        if (Auth::id() !== $this->group->owner_id) {
            $this->is_authorized = false;
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit grup ini. Hanya **bendahara** grup yang diizinkan.');
            return;
        }

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
        // Hanya hitung ulang jika grup belum aktif (karena hanya saat itu nilai boleh diubah oleh Bendahara)
        if (!$this->group_is_active) {
            $quota = intval($this->quota);
            $fee = floatval($this->fee_amount);
            
            // Pastikan nilai minimal kuota adalah 1 atau 2 sesuai bisnis Anda
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
     * Aturan validasi (dinamis berdasarkan status grup).
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
            // Ketika grup aktif, Bendahara hanya boleh mengedit nama/deskripsi, semua parameter keuangan dikunci.
            return array_merge($commonRules, [
                // Kunci semua nilai yang mempengaruhi perhitungan saat aktif
                'quota' => ['required', 'integer', Rule::in([$this->group->quota])],
                'frequency' => ['required', Rule::in([$this->group->payment_frequency])], 
                'fee_amount' => ['required', 'numeric', Rule::in([$this->group->fee_per_member])], 
                // Kunci juga group_pot (walaupun tidak diedit di form, ia adalah kolom model)
                'group_pot' => ['required', 'numeric', Rule::in([$this->group->group_pot])],
                'start_date' => ['nullable', 'date', Rule::in([$startDateValue])], 
            ]);
        } else {
            // Jika belum aktif, Bendahara dapat mengubah parameter
            return array_merge($commonRules, [
                'quota' => 'required|integer|min:2|max:30',
                'frequency' => 'required|in:weekly,monthly,bi-weekly', 
                'fee_amount' => 'required|numeric|min:1000|max:10000000', 
                'group_pot' => 'required|integer|min:100000|max:100000000', // Wajib ada
                'start_date' => 'nullable|date|after_or_equal:today', // Ubah 'after:today' menjadi 'after_or_equal:today' agar bisa dimulai hari ini
            ]);
        }
    }

    /**
     * Simpan perubahan grup (hanya dapat dilakukan oleh Bendahara).
     */
    public function updateGroup()
    {
        if (!$this->is_authorized) {
            session()->flash('error', 'Anda tidak memiliki izin **Bendahara** untuk melakukan aksi ini.');
            return; 
        }

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
                'quota' => $this->quota,
                'payment_frequency' => $this->frequency, // Mapping ke kolom model
                'fee_per_member' => $this->fee_amount, // Mapping ke kolom model
                'group_pot' => $this->group_pot, // Simpan nilai hitungan
                'start_date' => $this->start_date ?: $this->group->start_date, 
            ]);

            session()->flash('success', 'Detail Grup ' . $this->name . ' berhasil diperbarui oleh Bendahara!');
            
            // Redirect Livewire
            return $this->redirectRoute('groups.bendahara.index', $this->group, navigate: true); 

        } catch (\Exception $e) {
            \Log::error('Update Group Error (Bendahara): ' . $e->getMessage(), ['group_id' => $this->group->id, 'user_id' => Auth::id()]);
            session()->flash('error', 'Gagal memperbarui grup. Silakan coba lagi. (Kode Error: ' . $e->getMessage() . ')');
        }
    }

    public function render()
    {
        return view('groups.bendahara.edit')->layout('layouts.app');
    }
}