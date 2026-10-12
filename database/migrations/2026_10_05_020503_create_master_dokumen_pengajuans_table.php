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
        Schema::create('master_dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->string('kode_dokumen')->unique();
            $table->text('deskripsi')->nullable();
            $table->boolean('wajib')->default(true);
            $table->enum('berlaku_untuk', ['semua', 'new_app', 'app_development', 'app_deactivation'])->default('semua');
            $table->string('format_diizinkan')->default('pdf');
            $table->integer('max_size_kb')->default(10240);
            $table->string('file_template')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_dokumen_pengajuan');
    }
};
