<?php

namespace App\Http\Livewire\Rounds;

use Livewire\Component;
use App\Models\Group;
use App\Models\Round;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class RoundIndex extends Component
{
    public $managedGroups;

    public function mount()
    {
        $userId = Auth::id();

        try {
            // Ambil grup yang user jadi bendahara
            $groupIds = Group::whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('role', 'bendahara');
            })->pluck('id');

            $this->managedGroups = Group::with('members') // hanya untuk relasi dasar
                ->whereIn('id', $groupIds)
                ->get()
                ->map(function ($group) {
                    // Ambil rounds **langsung per grup** (bukan lewat eager load global)
                    $rounds = Round::where('group_id', $group->id)
                        ->whereIn('status', [
                            Round::STATUS_READY_TO_DRAW,
                            Round::STATUS_PENDING_PAYMENT,
                            Round::STATUS_COMPLETED
                        ])
                        ->with('winnerMember.user')
                        ->get();

                    $group->readyToDrawRound = $rounds->firstWhere('status', Round::STATUS_READY_TO_DRAW);
                    $group->pendingPaymentRound = $rounds->firstWhere('status', Round::STATUS_PENDING_PAYMENT);
                    $group->completedRounds = $rounds
                        ->where('status', Round::STATUS_COMPLETED)
                        ->sortByDesc('round_number')
                        ->values()
                        ->take(5);

                    if ($group->readyToDrawRound) {
                        $group->roundStatus = 'Siap Diundi (Putaran #' . $group->readyToDrawRound->round_number . ')';
                    } elseif ($group->pendingPaymentRound) {
                        $group->roundStatus = 'Menunggu Pembayaran (Putaran #' . $group->pendingPaymentRound->round_number . ')';
                    } elseif ($group->status === 'completed') {
                        $group->roundStatus = 'Selesai';
                    } else {
                        $group->roundStatus = 'Tidak Aktif';
                    }

                    return $group;
                });
        } catch (QueryException $e) {
            logger()->error("RoundIndex mount error: " . $e->getMessage());
            $this->managedGroups = collect();
            session()->flash('error', 'Gagal memuat data grup.');
        }
    }

    public function render()
    {
        return view('rounds.round_index')->layout('layouts.app');
    }
}