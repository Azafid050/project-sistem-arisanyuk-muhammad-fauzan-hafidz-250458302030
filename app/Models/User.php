<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage; // BARU: Tambahkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role', // admin, bendahara, anggota
        'profile_photo_path',
    ];

    /**
     * Hidden attributes for serialization
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    // ⭐️ BARU: Appends 'profile_photo_url' secara otomatis ke model array/JSON
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    // ===== ACCESSORS & MUTATORS =====
    
    /**
     * Get the URL for the user's profile photo, with a UI Avatar fallback.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return Storage::disk('public')->url($this->profile_photo_path);
        }

        $name = urlencode($this->name ?? 'User');
        $color = 'fff';
        $background = '4F46E5';
        
        return "https://ui-avatars.com/api/?name={$name}&background={$background}&color={$color}&size=128&bold=true";
    }


    // ===== HELPERS =====
    
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    // ... (Fungsi isAdmin, isBendahara, isAnggota tetap sama) ...
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    // ===== RELATIONS (Tetap Sama) =====

    // Grup yang dimiliki (owner)
    public function groups()
    {
        return $this->hasMany(Group::class, 'owner_id');
    }

    // Member record di group_members
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    // Gabung grup melalui pivot table (dibiarkan sesuai permintaan)
    public function joinedGroups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id')
                    ->withPivot('joined_at', 'is_active')
                    ->withTimestamps();
    }

    // Ronde yang dimenangkan user 
    public function roundsWon(): HasManyThrough
    {
        return $this->hasManyThrough(
            Round::class,
            GroupMember::class,
            'user_id',
            'winner_member_id',
            'id',
            'id'
        );
    }

    // Pembayaran yang dilakukan user 
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(
            Payment::class,
            GroupMember::class,
            'user_id',
            'group_member_id',
            'id',
            'id'
        );
    }

    // Pembayaran yang diverifikasi user (bendahara)
    public function verifiedPayments()
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    // Notifikasi user
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // ===== EXTRA HELPERS (Tetap Sama) =====

    // Ambil grup yang masih aktif
    public function activeGroups()
    {
        return $this->joinedGroups()->wherePivot('is_active', true);
    }

    // Pembayaran terakhir
    public function lastPayment()
    {
        return $this->payments()->latest()->first();
    }
}