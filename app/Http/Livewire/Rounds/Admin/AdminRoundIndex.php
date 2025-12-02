<?php

namespace App\Http\Livewire\Rounds\Admin;

use Livewire\Component;
use App\Models\Group;
use App\Models\Round;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class AdminRoundIndex extends Component
{
    // Properti ini sekarang akan menyimpan SEMUA grup yang ada
    public $allGroups;

    public function mount()
    {
        // Untuk akses Admin, kita tidak perlu memfilter berdasarkan Auth::id() kecuali ada batasan lain
        // Jika asumsinya ini adalah admin super/global, kita ambil semua grup.

        try {
            // Ambil SEMUA grup yang aktif
            $this->allGroups = Group::with('members')
                ->where('status', '!=', 'archived') // Misalnya, hanya grup yang tidak diarsipkan
                ->get()
                ->map(function ($group) {
                    // Ambil rounds yang relevan (pending payment, ready to draw, completed)
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
                    
                    // Ambil bendahara (untuk info tambahan admin)
                    $bendahara = $group->members->firstWhere('role', 'bendahara');
                    $group->bendaharaName = $bendahara->user->name ?? 'Tidak Ditetapkan';

                    // Ambil 5 putaran terakhir yang sudah selesai
                    $group->completedRounds = $rounds
                        ->where('status', Round::STATUS_COMPLETED)
                        ->sortByDesc('round_number')
                        ->values()
                        ->take(5);

                    // Tentukan status utama grup untuk ditampilkan
                    if ($group->readyToDrawRound) {
                        $group->roundStatus = 'Siap Diundi (Putaran #' . $group->readyToDrawRound->round_number . ')';
                        $group->statusClass = 'bg-green-100 text-green-800 border-green-300';
                    } elseif ($group->pendingPaymentRound) {
                        $group->roundStatus = 'Menunggu Pembayaran (Putaran #' . $group->pendingPaymentRound->round_number . ')';
                        $group->statusClass = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                    } elseif ($group->status === 'completed') {
                        $group->roundStatus = 'Selesai Penuh';
                        $group->statusClass = 'bg-gray-100 text-gray-700 border-gray-300';
                    } else {
                        $group->roundStatus = 'Belum Aktif/Tidak Ada Putaran';
                        $group->statusClass = 'bg-red-100 text-red-800 border-red-300';
                    }

                    return $group;
                });
        } catch (QueryException $e) {
            logger()->error("RoundIndex mount error: " . $e->getMessage());
            $this->allGroups = collect();
            session()->flash('error', 'Gagal memuat data grup arisan.');
        }
    }

    public function render()
    {
        // Menggunakan $this->allGroups di view
        return view('rounds.admin.admin_round_index', [
            'managedGroups' => $this->allGroups,
        ])->layout('layouts.app');
    }
}