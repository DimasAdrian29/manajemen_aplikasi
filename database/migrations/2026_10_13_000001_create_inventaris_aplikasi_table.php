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
        Schema::create('inventaris_aplikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_registrasi')->nullable()->unique();
            $table->string('nama_aplikasi');
            $table->foreignId('instansi_id')->nullable()->constrained('instansi')->nullOnDelete();
            $table->string('status')->nullable();
            $table->foreignId('aplikasi_pengganti_id')->nullable()->constrained('inventaris_aplikasi')->nullOnDelete();
            $table->boolean('is_aplikasi_pemprov')->default(true);
            $table->boolean('is_website_resmi')->default(false);
            $table->string('pic_aplikasi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('fungsi_menu')->nullable();
            $table->string('platform')->nullable();
            $table->string('jenis_layanan')->nullable();
            $table->string('konektivitas')->nullable();
            $table->string('url')->nullable();
            $table->date('tanggal_pemberlakuan')->nullable();
            $table->string('penempatan_server')->nullable();

            // Tim teknis (satu nama per baris)
            $table->text('programmer')->nullable();
            $table->text('pengelola_adm')->nullable();
            $table->text('tim_pengujian_aplikasi')->nullable();
            $table->text('tim_pengujian_infrastruktur')->nullable();
            $table->text('tim_pengujian_keamanan')->nullable();

            $table->string('status_pengembangan')->nullable();

            // Dokumentasi administrasi
            $table->boolean('ada_self_assessment')->default(false);
            $table->boolean('ada_surat_keputusan')->default(false);
            $table->boolean('ada_manual_book')->default(false);
            $table->boolean('ada_bast')->default(false);

            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('jenis_layanan');
            $table->index('status_pengembangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_aplikasi');
    }
};
