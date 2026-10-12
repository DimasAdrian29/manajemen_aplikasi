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
        Schema::create('testing_aplikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('project')->cascadeOnDelete();
            $table->foreignId('penguji_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_hasil')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal_testing')->nullable();
            $table->enum('status_validasi', ['lulus', 'tidak_lulus', 'perbaikan'])->default('perbaikan');
            $table->text('catatan_validasi')->nullable();
            $table->dateTime('tanggal_validasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_aplikasi');
    }
};
