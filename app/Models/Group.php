<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Mengganti \Log menjadi Log
use Carbon\Carbon; 
use App\Models\User; 
use App\Models\Round; 
use App\Models\GroupMember; 
use Illuminate\Database\Eloquent\Relations\HasOne; 
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'description', 'owner_id', 'quota', 'fee_per_member', 'group_pot', 'payment_frequency', 'start_date', 'status'
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fee_per_member' => 'decimal:2', 
        'group_pot' => 'integer', 
        'start_date' => 'date', 
        'quota' => 'integer', 
    ];

    // --- KONSTANTA STATUS GROUP ---
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING = 'pending'; 
    public const STATUS_COMPLETED = 'completed';
    // --- AKHIR KONSTANTA STATUS GROUP ---

    /**
     * DAFTAR PERAN ADMINISTRATIF
     * Peran-peran ini tidak dihitung dalam kuota putaran (Group::quota).
     *
     * @var array<int, string>
     */
    protected const ADMINISTRATIVE_ROLES = [
        'owner', 
        'admin', 
        'bendahara'
    ];
    
    // --- RELASI ---

    /**
     * Relasi ke pemilik grup.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    
    /**
     * Relasi ke GroupMember model (Anggota secara umum, termasuk pending).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }
    
    /**
     * Relasi ALIAS: Digunakan oleh komponen Livewire, mengarahkan ke relasi 'members' dasar.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupMembers(): HasMany
    {
        return $this->members();
    }
    
    /**
     * Relasi ke anggota yang statusnya 'approved'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approvedMembers(): HasMany
    {
        // BUG FIX: Menggunakan 'members' sebagai relasi dasar untuk konsistensi
        return $this->members()->where('status', 'approved');
    }
    
    /**
     * Relasi ke semua putaran (Round).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rounds(): HasMany
    {
        return $this->hasMany(Round::class);
    }

    /**
     * Relasi ke putaran (Round) yang saat ini aktif 
     * (pending payment atau siap diundi).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activeRound(): HasOne
    {
        // Asumsi Round::STATUS_PENDING_PAYMENT dan Round::STATUS_READY_TO_DRAW ada di model Round.
        // Jika tidak ada, ganti dengan string hardcoded 'pending_payment' dan 'ready_to_draw'.
        // Untuk contoh ini, saya akan tetap menggunakan string untuk menghindari error undefined constant.
        return $this->hasOne(Round::class)
            ->whereIn('status', ['pending_payment', 'ready_to_draw']); 
    }
    
    /**
     * Mendefinisikan relasi ke anggota yang sudah disetujui (approved) DAN
     * Perannya BUKAN peran administratif. Inilah anggota kuota yang sebenarnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotaMembers(): HasMany
    {
        return $this->approvedMembers() // Mulai dari anggota yang sudah disetujui
                    ->whereNotIn('role', self::ADMINISTRATIVE_ROLES); // Kecualikan peran admin
    }
    
    // --- LOGIKA PENGHITUNGAN KUOTA & PUTARAN ---
    
    /**
     * MENGHITUNG jumlah anggota yang HANYA memiliki peran 'member' (Anggota Kuota).
     *
     * @return int
     */
    public function getQuotaMembersCount(): int
    {
        return $this->quotaMembers()->count();
    }
    
    /**
     * Accessor untuk mendapatkan jumlah Anggota Kuota secara dinamis (convenience).
     * Dapat dipanggil di Livewire/Blade sebagai $group->quota_members_count
     *
     * @return int
     */
    protected function getQuotaMembersCountAttribute(): int
    {
        return $this->getQuotaMembersCount();
    }
    
    /**
     * Mendapatkan array ID anggota GroupMember yang memenuhi kuota (Siap diundi/bayar).
     *
     * @return array<int>
     */
    public function getActiveQuotaMemberIds(): array
    {
        return $this->quotaMembers()
                    ->pluck('id')
                    ->all();
    }
    
    /**
     * Mendapatkan putaran (Round) yang saat ini sedang berjalan atau menunggu pembayaran.
     *
     * @return \App\Models\Round|null
     */
    public function getCurrentRound(): ?Round
    {
        // MENGHILANGKAN $this->activeRound ?? ... karena activeRound() adalah relasi HasOne.
        // Relasi HasOne akan mengembalikan model atau null saat di-access sebagai properti ($this->activeRound).
        // Jika belum di eager load, panggil $this->activeRound()->first()
        return $this->activeRound ?? $this->activeRound()->first(); 
    }
    
    /**
     * Accessor untuk mendapatkan nomor putaran saat ini.
     *
     * @return int|null
     */
    protected function getCurrentRoundNumberAttribute(): ?int
    {
        return $this->getCurrentRound()?->round_number;
    }

    // --- FUNGSI INTI ---

    /**
     * Sinkronisasi status grup ('active' atau 'pending') berdasarkan jumlah anggota kuota
     * dan kuota yang ditetapkan.
     */
    public function refreshStatus(): void
    {
        // Tidak perlu $this->refresh() di sini, karena model sudah segar atau akan di-load ulang
        // saat mengambil $this->quotaMembers().
        
        $quotaMemberCount = $this->getQuotaMembersCount(); 
        
        // Memastikan status 'completed' tidak tertimpa jika grup sudah selesai.
        if ($this->status === self::STATUS_COMPLETED) {
            return;
        }

        $targetStatus = $quotaMemberCount >= $this->quota ? self::STATUS_ACTIVE : self::STATUS_PENDING;
        
        if ($this->status !== $targetStatus) {
            $this->status = $targetStatus;
            $this->saveQuietly(); 
        }
    }
    
    /**
     * Memeriksa dan mengaktifkan grup: Memanggil refreshStatus, lalu membuat putaran
     * jika status 'active' dan putaran belum ada.
     *
     * @return bool
     */
    public function checkAndActivate(): bool
    {
        $this->refreshStatus(); 
        
        // Memeriksa status dan apakah putaran sudah ada
        if ($this->status === self::STATUS_ACTIVE && !$this->rounds()->exists()) {
            try {
                DB::beginTransaction();
                
                $this->generateRounds(); 
                
                DB::commit(); 
                return true;
            } catch (\Exception $e) {
                DB::rollBack(); 
                // Menggunakan Log::error (dengan facade Log) untuk konsistensi.
                Log::error("Gagal membuat putaran untuk Grup #{$this->id}: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * Membuat semua putaran arisan berdasarkan kuota grup.
     */
    protected function generateRounds(): void
    {
        if ($this->rounds()->exists() || $this->quota < 1) {
            return;
        }
        
        $roundsToCreate = $this->quota;
        
        // Menggunakan Carbon::parse jika $this->start_date mungkin null atau string.
        // Karena di-cast ke 'date', ini aman.
        $currentDate = $this->start_date
            ? $this->start_date->copy()->startOfDay() 
            : Carbon::now()->startOfDay(); 

        // Array untuk operasi createMany (Lebih efisien)
        $roundsData = [];
            
        for ($i = 1; $i <= $roundsToCreate; $i++) {
            if ($i > 1) {
                // BUG FIX: Panggil calculateNextDrawDate dengan $currentDate baru sebagai baseDate
                $currentDate = $this->calculateNextDrawDate($currentDate, $this->payment_frequency);
            }
            
            $status = $i === 1 ? 'pending_payment' : 'inactive'; 

            $roundsData[] = [
                'round_number' => $i,
                'draw_date' => $currentDate->toDateString(), 
                'winner_member_id' => null, 
                'status' => $status, 
                'payout_date' => null,
                'created_at' => now(), // Tambahkan timestamp
                'updated_at' => now(), // Tambahkan timestamp
            ];
        }

        // Mass insert untuk efisiensi
        $this->rounds()->createMany($roundsData);
    }

    /**
     * Menghitung tanggal pengundian berikutnya berdasarkan frekuensi pembayaran.
     *
     * @param \Carbon\Carbon $baseDate Tanggal dasar untuk perhitungan.
     * @param string $frequency Frekuensi pembayaran ('weekly', 'monthly', 'bi-weekly').
     * @return \Carbon\Carbon
     */
    protected function calculateNextDrawDate(Carbon $baseDate, string $frequency): Carbon
    {
        // PENTING: Gunakan $baseDate->copy() untuk memastikan tanggal dasar tidak berubah
        $date = $baseDate->copy()->startOfDay();
        
        return match ($frequency) {
            'weekly' => $date->addWeek(),
            'monthly' => $date->addMonth(),
            'bi-weekly' => $date->addWeeks(2),
            default => $date->addWeek(), // Default ke mingguan jika frekuensi tidak dikenali
        };
    }
}