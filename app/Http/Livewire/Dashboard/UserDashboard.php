<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Payment;
use App\Models\Round;

class UserDashboard extends Component
{
    public $arisanGroups;
    public $stats;
    public $upcomingPayments;

    public function mount()
    {
        $user = Auth::user();

        // Ambil keanggotaan yang disetujui (SEMUA status grup)
        $approvedMemberships = $user->groupMembers()
            ->where('status', 'approved')
            ->with('group')
            ->get();

        $this->arisanGroups = $approvedMemberships->map(function ($membership) {
            $group = $membership->group;

            // Ambil putaran aktif (pending_payment atau ready_to_draw)
            $activeRound = $group->rounds()
                ->whereIn('status', [Round::STATUS_PENDING_PAYMENT, Round::STATUS_READY_TO_DRAW])
                ->first();

            // Cek apakah sudah bayar di putaran aktif
            // Jika grup tidak 'active' (misal 'completed'), maka is_paid dianggap true (tidak ada kewajiban bayar)
            $isPaid = true; 
            if ($group->status === Group::STATUS_ACTIVE && $activeRound) { // <--- KUNCI: Hanya cek pembayaran jika grup ACTIVE
                $payment = $membership->payments()
                    ->where('round_id', $activeRound->id)
                    ->where('status', Payment::STATUS_VERIFIED)
                    ->first();
                $isPaid = $payment !== null;
            }

            // Tambahkan atribut dinamis
            $group->setAttribute('members_count', $group->quotaMembers()->count());
            $group->setAttribute('current_round', $activeRound?->round_number ?? '-');
            $group->setAttribute('is_paid', $isPaid);
            $group->setAttribute('amount', $group->fee_per_member);
            $group->setAttribute('status_label', ucfirst($group->status));

            return $group;
        });

        // Hitung statistik
        // HANYA hitung grup yang statusnya 'active' untuk statistik
        $activeGroupsCollection = $this->arisanGroups->where('status', Group::STATUS_ACTIVE);
        
        $this->stats = [
            'active_groups' => $activeGroupsCollection->count(), // Hanya hitung yang ACTIVE
            'unpaid' => $activeGroupsCollection->where('is_paid', false)->count(), // Hanya hitung unpaid dari grup ACTIVE
            'wins' => $user->roundsWon()->count(),
        ];

        // Pembayaran jatuh tempo dalam 7 hari (status = pending)
        $this->upcomingPayments = Payment::whereHas('groupMember', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereHas('round', function ($q) {
                $q->whereBetween('draw_date', [now(), now()->addDays(7)]);
            })
            ->where('status', Payment::STATUS_PENDING)
            ->with('groupMember.group')
            ->get()
            ->map(function ($payment) {
                return (object) [
                    'group_name' => $payment->groupMember->group->name,
                    'due_date' => $payment->round->draw_date,
                ];
            });
    }

    public function render()
    {
        return view('dashboard.anggota')->layout('layouts.app');
    }
}