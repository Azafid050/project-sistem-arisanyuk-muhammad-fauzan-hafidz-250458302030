<?php

namespace App\Http\Livewire\Payments\Bendahara;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\Round;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class IndexPaymentBendahara extends Component
{
    // Properti untuk List Grup yang Dikelola (dari kode Anda)
    public array $groupsToManage = [];
    public int $bendaharaId;

    // Properti State untuk Detail Verifikasi
    public ?int $selectedGroupId = null; 
    public ?Group $group = null; 
    public ?Round $activeRound = null;
    public int $verifiedPaymentsCount = 0;
    public int $totalQuotaMembers = 0;
    public bool $isReadyToDraw = false;
    public Collection $payments;

    // Untuk Modal Penolakan
    public bool $showRejectModal = false;
    public ?int $paymentIdToReject = null;
    public string $rejectReason = '';
    public ?string $currentProofPath = null; // Path bukti pembayaran untuk modal

    public function mount()
    {
        $this->bendaharaId = Auth::id();
        $this->loadGroups();
        $this->payments = new Collection(); 
    }

    /**
     * Memuat daftar grup yang dikelola oleh bendahara.
     */
    public function loadGroups()
    {
        if (!$this->bendaharaId) {
            $this->groupsToManage = [];
            return;
        }

        $nonPayingRoles = ['bendahara', 'admin', 'owner'];

        $groups = Group::where('status', 'active')
            ->whereHas('approvedMembers', function ($query) {
                $query->where('user_id', $this->bendaharaId)
                    ->where('role', 'bendahara');
            })
            ->with(['rounds' => function ($query) {
                $query->whereIn('status', [Round::STATUS_PENDING_PAYMENT, Round::STATUS_READY_TO_DRAW]);
            }])
            ->get(['id', 'name', 'fee_per_member', 'quota', 'payment_frequency']);

        $this->groupsToManage = $groups->map(function ($group) use ($nonPayingRoles) {
            $activeRound = $group->rounds->first();
            
            $totalPayingMembersQuota = $group->approvedMembers()
                ->whereNotIn('role', $nonPayingRoles) 
                ->count(); 

            $pendingPaymentsCount = 0;
            $verifiedPaymentsCount = 0;
            $isReadyToDraw = false;

            if ($activeRound) {
                $paymentsInRound = $activeRound->payments()->get(['status']);
                $pendingPaymentsCount = $paymentsInRound->where('status', \App\Models\Payment::STATUS_PENDING)->count();
                $verifiedPaymentsCount = $paymentsInRound->where('status', \App\Models\Payment::STATUS_VERIFIED)->count();
                
                $isReadyToDraw = $verifiedPaymentsCount >= $totalPayingMembersQuota; 
            }

            return [
                'id' => $group->id,
                'name' => $group->name,
                'active_round_number' => $activeRound ? $activeRound->round_number : 'N/A',
                'fee_per_member' => $group->fee_per_member,
                'pending_payments_count' => $pendingPaymentsCount,
                'verified_payments_count' => $verifiedPaymentsCount,
                'total_members_quota' => $totalPayingMembersQuota,
                'is_round_active' => (bool) $activeRound,
                'is_fully_paid' => $isReadyToDraw,
            ];
        })->toArray();
    }

    /**
     * Dipanggil saat user mengklik salah satu grup dari daftar.
     */
    public function selectGroup(int $groupId)
    {
        if ($this->selectedGroupId === $groupId) {
            $this->selectedGroupId = null;
            $this->reset(['group', 'activeRound', 'verifiedPaymentsCount', 'totalQuotaMembers', 'isReadyToDraw', 'payments']);
            session()->forget(['warning', 'error']);
        } else {
            $this->selectedGroupId = $groupId;
            $this->loadDetailData();
        }
    }

    /**
     * Memuat atau me-refresh semua data detail verifikasi untuk grup yang dipilih.
     */
    public function loadDetailData(): void
    {
        $this->group = Group::find($this->selectedGroupId);

        if (!$this->group) {
            $this->reset(['group', 'activeRound', 'verifiedPaymentsCount', 'totalQuotaMembers', 'isReadyToDraw', 'payments']);
            session()->flash('error', 'Grup tidak ditemukan.');
            return;
        }

        $nonPayingRoles = ['bendahara', 'admin', 'owner']; 

        $this->activeRound = $this->group->rounds()
                                             ->whereIn('status', [Round::STATUS_PENDING_PAYMENT, Round::STATUS_READY_TO_DRAW])
                                             ->first();

        $this->totalQuotaMembers = $this->group->approvedMembers()
            ->whereNotIn('role', $nonPayingRoles) 
            ->count(); 

        if ($this->activeRound) {
            // Catatan: Pastikan model Payment memiliki relasi 'verifier' yang menunjuk ke kolom 'verified_by'
            $this->payments = $this->activeRound->payments()
                                                     ->with(['groupMember.user', 'verifier']) 
                                                     ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_VERIFIED])
                                                     ->get();

            $this->verifiedPaymentsCount = $this->payments->where('status', 'verified')->count();

            $this->isReadyToDraw = $this->verifiedPaymentsCount >= $this->totalQuotaMembers;
            
            if ($this->isReadyToDraw && $this->activeRound->status !== Round::STATUS_READY_TO_DRAW) {
                 $this->activeRound->update(['status' => Round::STATUS_READY_TO_DRAW]);
            }
        } else {
            $this->payments = new Collection();
            $this->verifiedPaymentsCount = 0;
            $this->isReadyToDraw = false;
            session()->flash('warning', 'Tidak ada putaran aktif yang menanti pembayaran untuk grup ini.');
        }
        
        $this->loadGroups();
    }


    /* --- Logika Interaksi Livewire (Verifikasi/Penolakan) --- */

    public function verifyPayment(int $paymentId)
    {
        $payment = Payment::find($paymentId);

        if ($payment && $payment->status === Payment::STATUS_PENDING) {
            $payment->update([
                'status' => Payment::STATUS_VERIFIED,
                'verified_by' => Auth::id(), // Sesuai skema DB
                'verified_at' => now(), 
            ]);
            session()->flash('success', 'Pembayaran berhasil diverifikasi.');
        } else {
             session()->flash('error', 'Pembayaran tidak ditemukan atau statusnya tidak "pending".');
        }

        $this->loadDetailData(); 
    }

    public function confirmRejectPayment(int $paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if ($payment) {
            $this->paymentIdToReject = $paymentId;
            $this->rejectReason = ''; 
            $this->currentProofPath = $payment->proof_path; // Sesuai skema DB
            $this->showRejectModal = true;
        } else {
            session()->flash('error', 'Pembayaran tidak ditemukan.');
        }
    }

    public function rejectPayment()
{
    $this->validate([
        'rejectReason' => 'required|string|max:255',
    ]);

    try {
        DB::transaction(function () {

            $payment = Payment::findOrFail($this->paymentIdToReject);

            if ($payment->status !== Payment::STATUS_PENDING) {
                throw new \Exception("Pembayaran tidak dalam status pending.");
            }

            $payment->update([
                'status' => Payment::STATUS_REJECTED,
                'rejection_reason' => $this->rejectReason,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            Notification::create([
                'user_id' => $payment->groupMember->user_id,
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran Anda ditolak. Alasan: " . $this->rejectReason,
            ]);

            $this->dispatch('notificationCreated');
        });

        session()->flash('success', 'Pembayaran berhasil ditolak.');

    } catch (\Exception $e) {

        session()->flash('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
    }

    $this->reset(['showRejectModal', 'paymentIdToReject', 'rejectReason', 'currentProofPath']);
    $this->loadDetailData();
}
    
    public function render()
    {
        // Ganti nama view jika berbeda dari yang ini
        return view('payments.bendahara.index_payment_bendahara')->layout('layouts.app');
    }
}