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
        Schema::table('peserta_rapat', function (Blueprint $table) {
            $table->dateTime('reminder_dikirim_at')->nullable()->after('notifikasi_dikirim_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peserta_rapat', function (Blueprint $table) {
            $table->dropColumn('reminder_dikirim_at');
        });
    }
};
