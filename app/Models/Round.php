<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupMember; 
use App\Models\Payment; // Pastikan model Payment di-import

class Round extends Model
{
    use HasFactory;

    /**
     * DAFTAR KONSTANTA STATUS PUTARAN
     */
    public const STATUS_INACTIVE = 'inactive'; // Putaran di masa depan
    public const STATUS_PENDING_PAYMENT = 'pending_payment'; // Putaran aktif, menunggu iuran (target putaran saat ini)
    public const STATUS_READY_TO_DRAW = 'ready_to_draw'; // Semua pembayaran diverifikasi, siap diundi
    public const STATUS_COMPLETED = 'completed'; // Pemenang sudah diundi

    protected $fillable = [
        'group_id', 
        'round_number', 
        'draw_date',
        'winner_member_id',
        'status',
        'payout_date',
    ];

    /**
     * Casts untuk tipe data.
     */
    protected $casts = [
        'draw_date' => 'date',
        'payout_date' => 'date',
    ];

    // --- RELASI ---

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function winnerMember() {
        // Relasi ke Anggota Grup (GroupMember) yang memenangkan putaran ini
        return $this->belongsTo(GroupMember::class, 'winner_member_id');
    }

    public function payments() {
        // Semua pembayaran yang terkait dengan putaran ini
        return $this->hasMany(Payment::class);
    }
    
    // --- FUNGSI BARU ---
    
    /**
     * Memeriksa apakah semua anggota kuota di putaran ini sudah membayar dan
     * memperbarui status putaran menjadi 'ready_to_draw' jika ya.
     */
    public function checkIfReadyToDraw(): void
    {
        // 1. Ambil jumlah anggota kuota yang disetujui
        // Asumsi model Group memiliki relasi quotaMembers() dan dihitung berdasarkan kuota arisan (bukan admin/bendahara).
        // Fungsi ini bergantung pada relasi Group::quotaMembers() yang sudah kita definisikan sebelumnya.
        $quotaCount = $this->group->quotaMembers()->count();
        
        // 2. Ambil jumlah pembayaran verified untuk putaran ini dari anggota kuota
        $verifiedPaymentsCount = $this->payments()
                                      ->where('status', Payment::STATUS_VERIFIED)
                                      ->whereHas('groupMember', function ($q) {
                                            // Memastikan hanya pembayaran dari anggota kuota (bukan owner/admin/bendahara) yang dihitung
                                            $q->where('status', 'approved') 
                                              ->whereNotIn('role', ['owner', 'admin', 'bendahara']); 
                                      })
                                      ->count();

        // 3. Bandingkan dan perbarui status
        if ($quotaCount > 0 && $verifiedPaymentsCount >= $quotaCount && $this->status === self::STATUS_PENDING_PAYMENT) {
            $this->status = self::STATUS_READY_TO_DRAW;
            $this->save();
        }
    }
}