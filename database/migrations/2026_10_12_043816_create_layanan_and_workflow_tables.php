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
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('ikon')->default('category');
            $table->string('warna')->default('#0D47A1');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('workflow', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('ikon')->nullable();
            $table->timestamps();
        });

        Schema::create('layanan_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('workflow')->cascadeOnDelete();
            $table->integer('urutan')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['layanan_id', 'workflow_id']);
        });

        Schema::create('workflow_field', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflow')->cascadeOnDelete();
            $table->string('label');
            $table->string('tipe'); // checklist, text, file_upload
            $table->foreignId('master_dokumen_id')->nullable()->constrained('master_dokumen_pengajuan')->nullOnDelete();
            $table->boolean('wajib')->default(false);
            $table->text('keterangan')->nullable();
            $table->json('opsi')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('pengajuan_workflow_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();
            $table->foreignId('layanan_workflow_id')->constrained('layanan_workflow')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, in_progress, completed, skipped, rejected
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('completed_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pengajuan_field_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_workflow_data_id')->constrained('pengajuan_workflow_data')->cascadeOnDelete();
            $table->foreignId('workflow_field_id')->constrained('workflow_field')->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->string('file_path')->nullable();
            $table->string('nama_file_asli')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_field_data');
        Schema::dropIfExists('pengajuan_workflow_data');
        Schema::dropIfExists('workflow_field');
        Schema::dropIfExists('layanan_workflow');
        Schema::dropIfExists('workflow');
        Schema::dropIfExists('layanan');
    }
};
