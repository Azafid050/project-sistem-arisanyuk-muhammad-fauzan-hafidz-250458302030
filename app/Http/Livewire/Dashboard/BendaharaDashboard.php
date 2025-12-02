<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Payment;
use App\Models\Round;
use App\Models\Notification;

class BendaharaDashboard extends Component
{
    // Livewire Public Properties
    public $stats;
    public $unverifiedPayments;
    public $groupStatuses;
    public $totalNotifications;
    public $unreadNotifications;
    public $chartData = [];

    protected $treasurerGroupIds;

    public function mount()
    {
        $this->loadData();
        $this->dispatch('bendaharaDashboardMounted');
    }

    protected function loadData()
    {
        $user = Auth::user();

        // 1. Ambil grup di mana user adalah bendahara
        $this->treasurerGroupIds = GroupMember::where('user_id', $user->id)
            ->where('role', 'bendahara')
            ->pluck('group_id');

        if ($this->treasurerGroupIds->isEmpty()) {
            $this->initializeEmptyState();
            return;
        }

        // 2. Pembayaran pending
        $this->unverifiedPayments = Payment::where('status', Payment::STATUS_PENDING)
            ->whereHas('round', function ($query) {
                $query->whereIn('group_id', $this->treasurerGroupIds)
                      ->where('status', Round::STATUS_PENDING_PAYMENT);
            })
            ->with(['groupMember.user', 'round.group'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 3. Status grup (hanya yang punya putaran relevan)
        $this->groupStatuses = Group::whereIn('id', $this->treasurerGroupIds)
            ->where('status', Group::STATUS_ACTIVE)
            ->get()
            ->map(function ($group) {
                // Ambil putaran terakhir yang relevan
                $round = $group->rounds()
                    ->whereIn('status', [
                        Round::STATUS_PENDING_PAYMENT,
                        Round::STATUS_READY_TO_DRAW
                    ])
                    ->orderBy('round_number', 'desc')
                    ->first();

                if (!$round) {
                    return null; // Skip grup tanpa putaran
                }

                // Hitung anggota yang WAJIB BAYAR
                $quota = $group->members()
                    ->whereNotIn('role', ['owner', 'admin', 'bendahara'])
                    ->count();

                // Hitung pembayaran terverifikasi
                $paidCount = $round->payments()
                    ->where('status', Payment::STATUS_VERIFIED)
                    ->whereHas('groupMember', fn ($q) => $q->whereNotIn('role', ['owner', 'admin', 'bendahara']))
                    ->count();

                $isReady = ($paidCount >= $quota);

                return (object) [
                    'id' => $group->id,
                    'name' => $group->name,
                    'current_round' => $round->round_number,
                    'draw_date' => $round->draw_date?->translatedFormat('d M Y') ?? 'N/A',
                    'paid_count' => $paidCount,
                    'quota' => $quota,
                    'is_ready' => $isReady,
                ];
            })
            ->filter() // Hapus null
            ->values();

        // 4. Hitung statistik & chart data
        $this->calculateStats($this->treasurerGroupIds);
        $this->prepareChartData($this->treasurerGroupIds);

        // 5. Notifikasi
        $this->totalNotifications = Notification::where('user_id', $user->id)->count();
        $this->unreadNotifications = Notification::where('user_id', $user->id)->where('is_read', false)->count();
    }

    protected function initializeEmptyState()
    {
        $this->stats = $this->getEmptyStats();
        $this->unverifiedPayments = collect();
        $this->groupStatuses = collect();
        $this->totalNotifications = 0;
        $this->unreadNotifications = 0;
        $this->chartData = [];
    }

    protected function calculateStats($treasurerGroupIds)
    {
        $unverifiedAmount = $this->unverifiedPayments->sum('amount');

        $verifiedAmount = Payment::where('status', Payment::STATUS_VERIFIED)
            ->whereHas('groupMember', function ($query) use ($treasurerGroupIds) {
                $query->whereIn('group_id', $treasurerGroupIds)
                      ->whereNotIn('role', ['owner', 'admin', 'bendahara']); // ✅ Konsisten
            })
            ->sum('amount');

        $readyToDrawCount = Round::whereIn('group_id', $treasurerGroupIds)
            ->where('status', Round::STATUS_READY_TO_DRAW)
            ->count();

        $this->stats = [
            'total_verified_amount' => $verifiedAmount,
            'total_unverified_amount' => $unverifiedAmount,
            'pending_verifications_count' => $this->unverifiedPayments->count(),
            'ready_to_draw_count' => $readyToDrawCount,
        ];
    }

    protected function prepareChartData($treasurerGroupIds)
    {
        $months = [];
        $amounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $totalAmount = Payment::where('status', Payment::STATUS_VERIFIED)
                ->whereHas('groupMember', function ($q) use ($treasurerGroupIds) {
                    $q->whereIn('group_id', $treasurerGroupIds)
                      ->whereNotIn('role', ['owner', 'admin', 'bendahara']); // ✅ Konsisten
                })
                ->whereYear('verified_at', $date->year)
                ->whereMonth('verified_at', $date->month)
                ->sum('amount');

            $amounts[] = $totalAmount;
        }

        $this->chartData = [
            'labels' => $months,
            'datasets' => [
                [
                    'label' => 'Total Dana Terkumpul (6 Bulan)',
                    'data' => $amounts,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.6)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ]
            ]
        ];
    }

    protected function getEmptyStats()
    {
        return [
            'total_verified_amount' => 0,
            'total_unverified_amount' => 0,
            'pending_verifications_count' => 0,
            'ready_to_draw_count' => 0,
        ];
    }

    public function verifyPayment(int $paymentId)
    {
        if (is_null($this->treasurerGroupIds)) {
            $this->loadData();
        }

        try {
            DB::beginTransaction();

            $payment = Payment::where('id', $paymentId)
                ->where('status', Payment::STATUS_PENDING)
                ->whereHas('round.group', function ($query) {
                    $query->whereIn('id', $this->treasurerGroupIds);
                })
                ->firstOrFail();

            $payment->update([
                'status' => Payment::STATUS_VERIFIED,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            $payment->round->checkIfReadyToDraw();
            DB::commit();

            $this->loadData();
            $this->dispatch('bendaharaDashboardRendered'); // Refresh chart

            session()->flash('success', "Pembayaran dari {$payment->groupMember->user->name} untuk grup {$payment->round->group->name} berhasil diverifikasi!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->loadData();
            session()->flash('error', 'Gagal memverifikasi pembayaran. Coba lagi.');
        }
    }

    public function rejectPayment(int $paymentId, string $reason = "Bukti transfer tidak valid/jelas.")
    {
        if (is_null($this->treasurerGroupIds)) {
            $this->loadData();
        }

        try {
            DB::beginTransaction();

            $payment = Payment::where('id', $paymentId)
                ->where('status', Payment::STATUS_PENDING)
                ->whereHas('round.group', function ($query) {
                    $query->whereIn('id', $this->treasurerGroupIds);
                })
                ->firstOrFail();

            $payment->update([
                'status' => Payment::STATUS_REJECTED,
                'rejected_by' => Auth::id(),
                'rejected_at' => now(),
                'rejection_reason' => $reason
            ]);

            Notification::create([
                'user_id' => $payment->groupMember->user_id,
                'type' => 'payment_rejected',
                'title' => 'Pembayaran Ditolak',
                'content' => "Pembayaran Anda untuk grup '{$payment->round->group->name}' putaran ke-{$payment->round->round_number} ditolak. Alasan: {$reason}",
                'link' => route('user.payments')
            ]);

            DB::commit();
            $this->loadData();
            $this->dispatch('bendaharaDashboardRendered');

            session()->flash('success', "Pembayaran dari {$payment->groupMember->user->name} berhasil ditolak.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->loadData();
            session()->flash('error', 'Gagal menolak pembayaran. Coba lagi.');
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        $this->unreadNotifications = 0;
    }

    public function render()
    {
        return view('dashboard.bendahara')
            ->layout('layouts.app', ['header' => 'Dasbor Bendahara']);
    }
}