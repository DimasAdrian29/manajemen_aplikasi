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
        Schema::table('pengujian_keamanan', function (Blueprint $table) {
            $table->enum('kategori_pengujian', [
                'vulnerability_assessment',
                'penetration_testing',
                'source_code_review',
                'audit_konfigurasi',
            ])->default('penetration_testing')->after('user_id');
            $table->enum('tingkat_risiko', ['aman', 'rendah', 'sedang', 'tinggi', 'kritis'])->default('aman')->after('hasil');
            $table->text('rekomendasi')->nullable()->after('keterangan');
            $table->string('nomor_surat')->nullable()->after('rekomendasi');
            $table->text('catatan_kerentanan')->nullable()->after('nomor_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengujian_keamanan', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_pengujian',
                'tingkat_risiko',
                'rekomendasi',
                'nomor_surat',
                'catatan_kerentanan',
            ]);
        });
    }
};
