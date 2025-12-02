<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Round; // Untuk mengambil konstanta status

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->integer('round_number');
            
            // Kolom disinkronkan: Menggunakan 'draw_date'
            $table->date('draw_date'); 
            
            // Kolom Status disinkronkan: Menggunakan 4 konstanta status dari model Round
            $table->enum('status', [
                Round::STATUS_INACTIVE, 
                Round::STATUS_PENDING_PAYMENT, 
                Round::STATUS_READY_TO_DRAW, 
                Round::STATUS_COMPLETED
            ])->default(Round::STATUS_INACTIVE);
            
            // Kolom Pemenang disinkronkan: Merujuk ke GroupMember, bukan User
            $table->foreignId('winner_member_id')
                  ->nullable()
                  ->constrained('group_members')
                  ->onDelete('set null');
            
            // Kolom Tanggal Pencairan Dana (Payout Date)
            $table->date('payout_date')->nullable(); 

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
        Schema::dropIfExists('rounds');
    }
};