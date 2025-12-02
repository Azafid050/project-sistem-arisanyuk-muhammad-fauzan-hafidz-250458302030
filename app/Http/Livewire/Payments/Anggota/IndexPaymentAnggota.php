<?php

namespace App\Http\Livewire\Payments\Anggota;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\GroupMember; // Penting: Diperlukan untuk mendapatkan GroupMember ID
use App\Models\Payment; // Penting: Diperlukan untuk mengecek status pembayaran

class IndexPaymentAnggota extends Component
{
    // Properti publik untuk menampung data grup beserta status pembayaran
    public $groupsWithPaymentStatus = [];
    
    // ID anggota yang sedang login
    public $memberId;

    public function mount()
    {
        // Mendapatkan ID pengguna yang sedang login
        $this->memberId = Auth::id();
        // Memuat status grup dan pembayaran
        $this->loadGroupPaymentStatus();
    }

    /**
     * Memuat grup dan menentukan status pembayaran anggota berdasarkan data nyata di DB.
     */
    public function loadGroupPaymentStatus()
    {
        // Pastikan user sudah login
        if (!$this->memberId) {
            $this->groupsWithPaymentStatus = [];
            return;
        }

        try {
            // 1. Mengambil grup aktif di mana anggota terdaftar
            $groups = Group::where('status', 'active')
                
                // Memastikan anggota terdaftar dan disetujui (membutuhkan relasi approvedMembers di Group model)
                ->whereHas('approvedMembers', function ($query) {
                    $query->where('user_id', $this->memberId);
                })
                
                // Eager load GroupMember yang sesuai dengan user yang sedang login (menggunakan relasi 'members' di Group model)
                ->with(['members' => function ($query) {
                    $query->where('user_id', $this->memberId);
                }])
                
                // Menghitung anggota non-administratif untuk kuota
                ->withCount(['members' => function ($query) {
                    // Daftar peran yang TIDAK dihitung sebagai anggota kuota
                    $excludedRoles = ['admin', 'bendahara', 'owner'];
                    $query->whereNotIn('role', $excludedRoles); 
                }]) 
                
                // Memuat putaran aktif grup (membutuhkan relasi activeRound di Group model)
                ->with('activeRound') 

                ->get(['id', 'name', 'fee_per_member', 'capacity', 'payment_frequency']);

            // 2. Memproses setiap grup untuk menentukan status pembayaran nyata
            $this->groupsWithPaymentStatus = $groups->map(function ($group) {
                
                // a. Dapatkan GroupMember ID user yang sedang login di grup ini
                $groupMember = $group->members->first();
                $groupMemberId = $groupMember ? $groupMember->id : null;
                
                // b. Dapatkan Putaran Aktif
                $activeRound = $group->activeRound;
                $activeRoundNumber = $activeRound ? $activeRound->round_number : 0;
                
                $status = 'unpaid'; // Default status jika belum ada pembayaran/putaran

                if ($groupMemberId && $activeRound) {
                    // c. QUERY DATABASE NYATA: Cari pembayaran terbaru untuk anggota di putaran aktif ini
                    $payment = Payment::where('group_member_id', $groupMemberId)
                        ->where('round_id', $activeRound->id)
                        ->latest() // Ambil record terbaru (untuk menangani rejected/upload ulang)
                        ->first();

                    if ($payment) {
                        $status = $payment->status; // verified, pending, rejected
                    }
                }

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'fee_per_member' => $group->fee_per_member ?? 0,
                    'active_round_number' => $activeRoundNumber, 
                    'payment_status' => $status, // verified, pending, rejected, atau unpaid
                    'capacity' => $group->capacity ?? 'N/A', 
                    'payment_frequency' => $group->payment_frequency ?? 'N/A', 
                    'current_members_count' => $group->members_count ?? 0, 
                ];
            })
            ->toArray();
            
        } catch (\Exception $e) {
            \Log::error("Error loading groups for member {$this->memberId}: " . $e->getMessage());
            $this->groupsWithPaymentStatus = [];
            session()->flash('error', 'Gagal memuat data grup. Kesalahan relasi model atau data putaran aktif tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('payments.anggota.index_payment_anggota')->layout('layouts.app');
    }
}