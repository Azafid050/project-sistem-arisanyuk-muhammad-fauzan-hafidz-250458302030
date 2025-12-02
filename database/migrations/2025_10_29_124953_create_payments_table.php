<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Payment; // Import model untuk mendapatkan konstanta status

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Kritis: Merujuk ke Putaran (Round) mana pembayaran ini dilakukan
            $table->foreignId('round_id')->constrained('rounds')->onDelete('cascade');
            
            // Kritis: Relasi ke Anggota Grup (GroupMember) yang melakukan pembayaran
            $table->foreignId('group_member_id')
                  ->constrained('group_members')
                  ->onDelete('cascade');

            $table->decimal('amount', 10, 2);
            
            // Mengganti 'proof' menjadi 'proof_path' (sesuai Livewire)
            $table->string('proof_path')->nullable(); 
            
            // Status: Menggunakan ENUM (sesuai konstanta model)
            $table->enum('status', [
                Payment::STATUS_PENDING, 
                Payment::STATUS_VERIFIED, 
                Payment::STATUS_REJECTED
            ])->default(Payment::STATUS_PENDING);

            // Kolom untuk Bendahara (User) yang memverifikasi
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            // Kolom Baru: Alasan Penolakan (HARUS ADA untuk komponen bendahara)
            $table->text('rejection_reason')->nullable(); // <<< PERBAIKAN INI
            
            // Kolom Baru: Waktu verifikasi
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};