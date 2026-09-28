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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp', 25)->nullable()->after('email');
        });

        // Ensure columns in opds are nullable if table already migrated
        Schema::table('opds', function (Blueprint $table) {
            $table->string('nama_pic')->nullable()->change();
            $table->string('whatsapp_pic')->nullable()->change();
            $table->string('email_pic')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });
    }
};
