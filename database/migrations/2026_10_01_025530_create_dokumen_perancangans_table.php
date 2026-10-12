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
        Schema::create('dokumen_perancangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->cascadeOnDelete();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('jenis_dokumen', ['srs', 'wireframe', 'erd', 'arsitektur', 'lainnya'])->default('srs');
            $table->string('file_dokumen');
            $table->enum('status', ['draft', 'menunggu_review', 'disetujui', 'ditolak'])->default('menunggu_review');
            $table->text('catatan')->nullable();
            $table->dateTime('tanggal_penilaian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_perancangan');
    }
};
