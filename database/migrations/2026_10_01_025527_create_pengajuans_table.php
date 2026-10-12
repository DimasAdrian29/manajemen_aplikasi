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
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->foreignId('aplikasi_id')->nullable()->constrained('aplikasi')->nullOnDelete();
            $table->enum('jenis_pengajuan', ['new_app', 'app_development', 'app_deactivation']);
            $table->string('nama_aplikasi')->nullable();
            $table->text('deskripsi_kebutuhan');
            $table->string('nama_pemohon');
            $table->string('no_hp_pemohon');
            $table->string('file_proposal')->nullable();
            $table->string('file_surat_pengantar')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('submitted');
            $table->text('catatan_penilaian')->nullable();
            $table->dateTime('tanggal_penilaian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
