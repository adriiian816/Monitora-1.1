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
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('deskripsi');
            $table->string('file_original_name')->nullable()->after('file_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('file_original_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_original_name', 'file_size']);
        });
    }
};
