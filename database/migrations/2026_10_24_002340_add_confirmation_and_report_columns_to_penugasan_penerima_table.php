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
        Schema::table('penugasan_penerima', function (Blueprint $table) {
            $table->enum('status_konfirmasi', ['menunggu_konfirmasi', 'dikonfirmasi', 'ditolak'])
                ->default('menunggu_konfirmasi')
                ->after('status_penugasan');
            $table->text('catatan_konfirmasi')->nullable()->after('status_konfirmasi');
            $table->timestamp('dikonfirmasi_pada')->nullable()->after('catatan_konfirmasi');

            $table->string('file_laporan')->nullable()->after('status_laporan');
            $table->string('file_laporan_name')->nullable()->after('file_laporan');
            $table->unsignedBigInteger('file_laporan_size')->nullable()->after('file_laporan_name');
            $table->text('isi_laporan')->nullable()->after('file_laporan_size');
            $table->string('link_dokumen')->nullable()->after('isi_laporan');
            $table->timestamp('dilaporkan_pada')->nullable()->after('link_dokumen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasan_penerima', function (Blueprint $table) {
            $table->dropColumn([
                'status_konfirmasi',
                'catatan_konfirmasi',
                'dikonfirmasi_pada',
                'file_laporan',
                'file_laporan_name',
                'file_laporan_size',
                'isi_laporan',
                'link_dokumen',
                'dilaporkan_pada',
            ]);
        });
    }
};
