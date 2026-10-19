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
        Schema::table('workflow', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('deskripsi')->constrained('roles')->nullOnDelete();
        });

        // Sinkronisasi baseline role untuk tahapan alur default
        $roleMapping = [
            'pengajuan-berkas' => 'opd',
            'telaah-kelayakan' => 'admin_aptika',
            'in_development' => 'ketua_tim',
            'functional_testing' => 'admin_aptika',
            'security_testing' => 'admin_persandian',
            'deployment' => 'admin_infrastruktur',
            'decommissioning' => 'admin_infrastruktur',
        ];

        foreach ($roleMapping as $slug => $roleName) {
            $roleId = DB::table('roles')->where('name', $roleName)->value('id');
            if ($roleId) {
                DB::table('workflow')->where('slug', $slug)->update(['role_id' => $roleId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
