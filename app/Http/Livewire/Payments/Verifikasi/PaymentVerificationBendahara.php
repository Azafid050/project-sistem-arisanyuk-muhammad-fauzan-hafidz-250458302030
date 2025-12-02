<?php  

namespace App\Http\Livewire\Payments\Verifikasi;  

use App\Models\Group;
use App\Models\Payment;
use App\Models\Round;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PaymentVerificationBendahara extends Component 
{
    public Group $group;
    public $activeRound;
    public $payments;

    public $verifiedPaymentsCount = 0;
    public $totalQuotaMembers = 0;
    public $isReadyToDraw = false;

    public $showRejectModal = false;
    public $paymentIdToReject = null;
    public $rejectReason = '';
    public ?string $currentProofPath = null;

    public function mount(Group $group)
    {
        $this->group = $group;
        $this->loadData();
    }

    public function loadData()
    {
        $this->activeRound = Round::where('group_id', $this->group->id)
            ->whereIn('status', [Round::STATUS_PENDING_PAYMENT, Round::STATUS_READY_TO_DRAW])
            ->first();

        $nonPayingRoles = ['bendahara', 'admin', 'owner'];

        $this->totalQuotaMembers = $this->group->approvedMembers()
            ->whereNotIn('role', $nonPayingRoles)
            ->count();

        if (!$this->activeRound) {
            $this->payments = collect();
            $this->verifiedPaymentsCount = 0;
            $this->isReadyToDraw = false;
            return;
        }

        $this->payments = Payment::where('round_id', $this->activeRound->id)
            ->with(['groupMember.user', 'verifier'])
            ->get();

        $this->verifiedPaymentsCount = $this->payments->where('status', 'verified')->count();

        $this->isReadyToDraw = $this->verifiedPaymentsCount >= $this->totalQuotaMembers;

        if ($this->isReadyToDraw && $this->activeRound->status !== Round::STATUS_READY_TO_DRAW) {
            $this->activeRound->update(['status' => Round::STATUS_READY_TO_DRAW]);
        }
    }

    /** VERIFIKASI PEMBAYARAN */
    public function verifyPayment($paymentId)
    {
        try {
            DB::transaction(function () use ($paymentId) {

                $payment = Payment::findOrFail($paymentId);

                if ($payment->status !== Payment::STATUS_PENDING) {
                    throw new \Exception("Pembayaran sudah diproses sebelumnya.");
                }

                $payment->update([
                    'status' => Payment::STATUS_VERIFIED,
                    'verified_by' => Auth::id(), // FIXED
                    'verified_at' => now(),
                ]);

                Notification::create([
                    'user_id' => $payment->groupMember->user_id,
                    'title' => 'Pembayaran Diverifikasi',
                    'message' => 'Pembayaran Anda untuk putaran ' . $this->activeRound->round_number . ' telah diverifikasi.',
                ]);

                $this->dispatch('notificationCreated');
            });

            session()->flash('success', 'Pembayaran berhasil diverifikasi.');

        } catch (\Exception $e) {

            session()->flash('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }

        $this->loadData();
    }

    /** BUKA MODAL TOLAK */
    public function confirmRejectPayment($paymentId)
    {
        $payment = Payment::find($paymentId);

        if ($payment) {
            $this->paymentIdToReject = $paymentId;
            $this->rejectReason = '';
            $this->currentProofPath = $payment->proof_path;
            $this->showRejectModal = true;
        } else {
            session()->flash('error', 'Pembayaran tidak ditemukan.');
        }
    }

    /** TOLAK PEMBAYARAN */
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
                    'verified_by' => Auth::id(), // FIXED
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
        $this->loadData();
    }

    public function render()
    {
        return view('payments.verifikasi.payment_verification_bendahara')
            ->layout('layouts.app');
    }
}
