<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Round;
use App\Models\Payment;

class GroupMember extends Model
{
    use HasFactory;

    protected $table = 'group_members';
    protected $fillable = [
        'group_id', 
        'user_id', 
        'joined_at',
        'role',
        'status',
    ];
    
    protected $casts = [
        'joined_at' => 'date',
    ];

    // --- RELASI ---
    
    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function payments() {
        // Relasi ke semua pembayaran yang dilakukan anggota ini
        return $this->hasMany(Payment::class, 'group_member_id');
    }

    public function wonRounds() {
        // Relasi ke semua putaran yang dimenangkan anggota ini
        return $this->hasMany(Round::class, 'winner_member_id');
    }

    // --- LOGIKA SINKRONISASI STATUS ---
    protected static function booted()
    {
        // Event dipanggil ketika model GroupMember diupdate (misalnya, perubahan status)
        static::updated(function (GroupMember $groupMember) {

            // Cek hanya jika kolom 'status' adalah yang berubah
            if ($groupMember->isDirty('status')) {

                // Cek aktivasi jika member disetujui
                if ($groupMember->status === 'approved') {
                    
                    // Pastikan joined_at terisi saat pertama kali disetujui.
                    if (is_null($groupMember->joined_at)) {
                        $groupMember->joined_at = Carbon::now();
                        $groupMember->saveQuietly(); 
                    }
                    
                    // Panggil checkAndActivate (yang di dalamnya memanggil refreshStatus)
                    $groupMember->group->checkAndActivate();
                }

                // Logika tambahan: jika status berubah dari 'approved' ke non-approved (misal 'rejected' atau 'left')
                if ($groupMember->getOriginal('status') === 'approved' && $groupMember->status !== 'approved') {
                    $groupMember->joined_at = null;
                    $groupMember->saveQuietly(); 
                }

                // Selalu sinkronkan status grup (Penting untuk menangani status PENDING setelah perubahan)
                $groupMember->group->refreshStatus();
            }
        });

        // Event dipanggil ketika anggota BARU ditambahkan
        static::created(function (GroupMember $gm) {
            // Panggil refreshStatus untuk menguji apakah grup menjadi aktif
            $gm->group->refreshStatus();
        });

        // Event dipanggil ketika anggota DIHAPUS (keluar permanen)
        static::deleted(function (GroupMember $gm) {
            // Panggil refreshStatus untuk menguji apakah grup harus kembali PENDING
            $gm->group->refreshStatus();
        });
    }
}