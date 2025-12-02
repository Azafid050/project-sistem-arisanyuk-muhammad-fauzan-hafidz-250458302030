<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Peran User di dalam Grup: 'owner' (pemilik), 'admin', 'bendahara', 'anggota'
            $table->enum('role', ['owner', 'admin', 'bendahara', 'anggota'])->default('anggota'); 

            // Status Keanggotaan: 'approved', 'pending', atau 'rejected'
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('pending');

            // Tanggal ini diisi saat anggota disetujui.
            $table->date('joined_at')->nullable();
            
            $table->timestamps();
            
            // Mencegah duplikasi user dalam satu grup
            $table->unique(['group_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};