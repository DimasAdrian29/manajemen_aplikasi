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
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->foreignId('layanan_id')->nullable()->after('aplikasi_id')->constrained('layanan')->nullOnDelete();
            $table->string('jenis_pengajuan')->nullable()->change();
        });

        Schema::table('master_dokumen_pengajuan', function (Blueprint $table) {
            $table->string('berlaku_untuk')->default('semua')->change();
        });

        Schema::table('project', function (Blueprint $table) {
            $table->string('status')->default('initiated')->change();
            $table->foreignId('current_workflow_id')->nullable()->after('status')->constrained('layanan_workflow')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropForeign(['current_workflow_id']);
            $table->dropColumn('current_workflow_id');
        });

        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropForeign(['layanan_id']);
            $table->dropColumn('layanan_id');
        });
    }
};
