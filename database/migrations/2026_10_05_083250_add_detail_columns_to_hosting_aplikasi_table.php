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
        Schema::table('hosting_aplikasi', function (Blueprint $table) {
            $table->string('url_aplikasi')->nullable()->after('nama_domain');
            $table->string('ip_server', 45)->nullable()->after('url_aplikasi');
            $table->string('nama_server')->nullable()->after('ip_server');
            $table->enum('jenis_hosting', ['vps', 'shared', 'container', 'cloud', 'on_premise'])->default('vps')->after('nama_server');
            $table->string('sistem_operasi')->nullable()->after('jenis_hosting');
            $table->unsignedSmallInteger('cpu_core')->nullable()->after('sistem_operasi');
            $table->unsignedSmallInteger('ram_gb')->nullable()->after('cpu_core');
            $table->unsignedInteger('storage_gb')->nullable()->after('ram_gb');
            $table->string('web_server')->nullable()->after('storage_gb');
            $table->string('database_server')->nullable()->after('web_server');
            $table->boolean('ssl_aktif')->default(false)->after('database_server');
            $table->date('ssl_kadaluarsa')->nullable()->after('ssl_aktif');
            $table->date('tanggal_live')->nullable()->after('ssl_kadaluarsa');
            $table->enum('status_hosting', ['persiapan', 'aktif', 'maintenance', 'nonaktif'])->default('aktif')->after('tanggal_live');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hosting_aplikasi', function (Blueprint $table) {
            $table->dropColumn([
                'url_aplikasi',
                'ip_server',
                'nama_server',
                'jenis_hosting',
                'sistem_operasi',
                'cpu_core',
                'ram_gb',
                'storage_gb',
                'web_server',
                'database_server',
                'ssl_aktif',
                'ssl_kadaluarsa',
                'tanggal_live',
                'status_hosting',
            ]);
        });
    }
};
