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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_opd_id')->constrained('tugas_opd')->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->string('file_original_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('konfirmasi_fisik')->default(false);
            $table->string('nama_pengantar')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->date('tanggal_penyerahan_fisik')->nullable();
            $table->text('catatan_opd')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->enum('status_verifikasi', [
                'menunggu_verifikasi',
                'diterima',
                'perlu_revisi',
            ])->default('menunggu_verifikasi');
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
