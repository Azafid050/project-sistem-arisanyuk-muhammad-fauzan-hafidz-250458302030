<?php

namespace App\Http\Livewire\Payments\Anggota;

use Livewire\Component;
use Livewire\WithFileUploads; 
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GroupMemberPaymentStatus extends Component
{
    use WithFileUploads;

    public Group $group;
    public string $paymentSubmissionStatus = 'none'; // 'none', 'pending', 'verified', 'rejected'
    public ?Payment $currentPayment = null; 
    public $activeRound; 
    public GroupMember $groupMember; // Gunakan type hint untuk konsistensi

    // Properti untuk Modal Pengiriman Pembayaran
    public bool $showPaymentModal = false;
    public $amount;
    public $proof_image; // File Upload

    public function mount(Group $group)
    {
        $this->group = $group->load('activeRound'); // Eager load activeRound saat mount
        
        // Memastikan pengguna adalah anggota yang disetujui (approved)
        $this->groupMember = $group->approvedMembers()
                                   ->where('user_id', Auth::id())
                                   ->firstOrFail();
        
        // Akses relasi activeRound yang sudah di-load
        $this->activeRound = $group->activeRound; 
        
        if ($this->activeRound) {
            $this->getPaymentStatus();
        }
        
        // Atur jumlah iuran default
        $this->amount = $group->fee_per_member;
    }

    /**
     * Mencari ID pengguna Bendahara atau Owner Grup.
     * @return int|null
     */
    protected function getTreasurerUserId(): ?int
    {
        // 1. Cari Bendahara ('bendahara') atau Admin ('admin')
        $treasurerMember = GroupMember::where('group_id', $this->group->id)
            ->whereIn('role', ['bendahara', 'admin'])
            ->first();

        if ($treasurerMember) {
            return $treasurerMember->user_id;
        }

        // 2. Jika tidak ada, fallback ke Owner Grup
        return $this->group->owner_id;
    }

    /**
     * Logika utama untuk menentukan status pembayaran user yang sedang login.
     */
    public function getPaymentStatus()
    {
        if (!$this->activeRound) {
            $this->paymentSubmissionStatus = 'none';
            $this->currentPayment = null;
            return;
        }

        // Cari Payment terakhir untuk GroupMember ini di round aktif
        $payment = Payment::where('group_member_id', $this->groupMember->id)
                            ->where('round_id', $this->activeRound->id)
                            ->latest()
                            ->first();

        $this->currentPayment = $payment;

        if (!$payment) {
            $this->paymentSubmissionStatus = 'none';
        } else {
            $this->paymentSubmissionStatus = $payment->status;
        }
    }

    public function submitPayment()
    {
        if (!$this->activeRound) {
            session()->flash('error', 'Tidak ada putaran aktif untuk pembayaran.');
            return;
        }
        
        $isResubmission = $this->paymentSubmissionStatus === 'rejected'; // Asumsi Payment::STATUS_REJECTED = 'rejected'

        $this->validate([
            // Wajib jika resubmit atau belum ada pembayaran
            'proof_image' => [
                'required', 
                'image', 
                'max:2048', 
                Rule::requiredIf($isResubmission || !$this->currentPayment) 
            ], 
            'amount' => 'required|numeric|min:' . $this->group->fee_per_member,
        ]);

        try {
            $proofPath = $this->proof_image->store('proofs/payments', 'public');
            $treasurerId = $this->getTreasurerUserId(); // Dapatkan ID Bendahara/Admin
            
            DB::beginTransaction();
            
            // 1. Perbarui pembayaran REJECTED sebelumnya
            if ($this->currentPayment && $this->currentPayment->status === 'rejected') {
                $this->currentPayment->update([
                    'amount' => $this->amount,
                    'proof_path' => $proofPath,
                    'status' => 'pending', // Asumsi Payment::STATUS_PENDING = 'pending'
                    'verified_by' => null,
                    'verified_at' => null,
                ]);
            } else {
                // 2. Buat pembayaran baru
                $this->groupMember->payments()->create([
                    'round_id' => $this->activeRound->id,
                    'amount' => $this->amount,
                    'proof_path' => $proofPath,
                    'status' => 'pending', // Asumsi Payment::STATUS_PENDING = 'pending'
                ]);
            }

            // Kirim notifikasi ke Bendahara/Admin (menggunakan logika yang diperbaiki)
            if ($treasurerId) {
                Notification::create([
                    'user_id' => $treasurerId, 
                    'title' => 'Pembayaran Baru Masuk!',
                    'message' => "Anggota {$this->groupMember->user->name} mengirimkan bukti iuran untuk Grup '{$this->group->name}' Putaran #{$this->activeRound->round_number}.",
                    'is_read' => false,
                ]);
            }
            
            DB::commit();
            
            $this->showPaymentModal = false;
            $this->reset(['proof_image']);
            $this->getPaymentStatus();

            session()->flash('success', 'Pembayaran berhasil dikirim dan menunggu verifikasi bendahara.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment submission failed: ' . $e->getMessage());
            session()->flash('error', 'Gagal mengirim pembayaran. Silakan coba lagi. ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('payments.anggota.group_member_payment_status')->layout('layouts.app');
    }
}