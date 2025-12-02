<?php

namespace App\Http\Livewire\Rounds;

use Livewire\Component;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Round;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class RoundRoulette extends Component
{
    public Group $group;
    public $activeRound;
    public $potentialWinners = []; 
    public $winner = null;
    public $rouletteFinished = false;

    public function mount(Group $group)
    {
        $this->group = $group;
        
        // Memastikan ini mengambil Round aktif yang benar dari relasi.
        $this->activeRound = $group->activeRound; 

        if (!$this->activeRound || $this->activeRound->status !== Round::STATUS_READY_TO_DRAW) {
            session()->flash('error', 'Putaran belum siap diundi atau tidak aktif.');
            
            // Menggunakan array asosiatif eksplisit untuk parameter 'group'.
            return redirect()->route('payments.verifikasi.payment_verification_bendahara', ['group' => $group->id]);
        }

        $this->loadPotentialWinners();
    }

    /**
     * Memuat daftar anggota kuota yang BELUM PERNAH menang.
     */
    public function loadPotentialWinners()
    {
        // Dapatkan semua ID GroupMember yang sudah menang di putaran sebelumnya
        $winnersIds = $this->group->rounds()->whereNotNull('winner_member_id')->pluck('winner_member_id')->toArray();
        
        // Dapatkan Anggota Kuota (yang approved dan non-admin) yang belum menang
        $this->potentialWinners = $this->group->quotaMembers()
                                              ->whereNotIn('id', $winnersIds)
                                              ->with('user')
                                              ->get()
                                              ->map(fn($gm) => ['id' => $gm->id, 'name' => $gm->user->name])
                                              ->toArray();
        
        if (empty($this->potentialWinners)) {
             session()->flash('error', 'Semua anggota sudah menang! Grup ini sudah selesai.');
             $this->group->update(['status' => 'completed']);
        }
    }

    /**
     * Menjalankan undian. Dipanggil oleh JS setelah animasi roda selesai.
     * @param int $winnerId ID GroupMember yang ditentukan oleh roda.
     */
    public function runRoulette($winnerId)
    {
        if ($this->rouletteFinished) return;

        // 1. Ambil pemenang dari ID yang dikirim JS
        $winnerMember = GroupMember::findOrFail($winnerId);

        DB::beginTransaction();
        try {
            // 2. Tandai putaran ini sebagai selesai
            $this->activeRound->update([
                'status' => Round::STATUS_COMPLETED,
                'winner_member_id' => $winnerMember->id,
                'payout_date' => now()->toDateString(), // Asumsi dana cair hari ini
            ]);

            // 3. Notifikasi Pemenang
            Notification::create([
                'user_id' => $winnerMember->user_id,
                'title' => 'SELAMAT! Anda Memenangkan Arisan!',
                'message' => "Anda adalah pemenang Arisan Grup '{$this->group->name}' Putaran #{$this->activeRound->round_number}. Uang tunai sebesar Rp " . number_format($this->group->group_pot, 0, ',', '.') . " akan dicairkan.",
                'is_read' => false,
            ]);

            // 4. Lanjut ke Putaran Berikutnya (jika ada)
            $nextRound = $this->group->rounds()
                                     ->where('round_number', $this->activeRound->round_number + 1)
                                     ->first();
                                     
            if ($nextRound) {
                $nextRound->update(['status' => Round::STATUS_PENDING_PAYMENT]);
                session()->flash('info', "Putaran #{$nextRound->round_number} telah dimulai, semua anggota wajib membayar iuran.");
            } else {
                // Semua putaran selesai
                $this->group->update(['status' => 'completed']);
            }

            DB::commit();
            
            // Set winner dan finished untuk Livewire re-render
            $this->winner = ['id' => $winnerMember->id, 'name' => $winnerMember->user->name];
            $this->rouletteFinished = true;
            $this->loadPotentialWinners(); // Memuat ulang untuk tampilan (walaupun sudah selesai)

        } catch (\Exception $e) {
            DB::rollBack();
            error_log('Roulette failed: ' . $e->getMessage()); 
            session()->flash('error', 'Gagal menjalankan undian. Coba lagi: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('rounds.round_roulette')->layout('layouts.app');
    }
}