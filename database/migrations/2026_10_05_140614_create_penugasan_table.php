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
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemberi_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_kegiatan');
            $table->date('batas_laporan')->nullable();
            $table->enum('status_penugasan', ['belum_dikerjakan', 'sedang_dikerjakan', 'selesai', 'dibatalkan'])->default('belum_dikerjakan');
            $table->enum('status_laporan', ['belum_dilaporkan', 'sudah_dilaporkan'])->default('belum_dilaporkan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};
