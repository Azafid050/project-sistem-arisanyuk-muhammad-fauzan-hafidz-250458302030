<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->integer('quota');
            
            // Kolom Diperbarui: Nama disinkronkan dan tipe data decimal untuk iuran per anggota
            $table->decimal('fee_per_member', 10, 2); 
            
            // Kolom BARU: Menyimpan total pot arisan (quota * fee_per_member)
            $table->bigInteger('group_pot'); 
            
            // Kolom Diperbarui: Nama disinkronkan, FIX enum syntax
            $table->enum('payment_frequency', ['weekly', 'monthly', 'bi-weekly']);
            
            // Catatan: start_date disarankan nullable agar bisa dibuat tanpa tanggal mulai pasti
            $table->date('start_date')->nullable();
            
            // Mengubah default status menjadi 'pending'
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};