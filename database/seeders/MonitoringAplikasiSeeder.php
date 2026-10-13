<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Hak akses Monitoring Daftar Aplikasi untuk Kepala Bidang beserta data awalnya.
 *
 * Jalankan: php artisan db:seed --class=MonitoringAplikasiSeeder
 */
class MonitoringAplikasiSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'inventaris.view' => 'Lihat Monitoring Daftar Aplikasi',
            'inventaris.create' => 'Tambah Data Aplikasi ke Daftar Monitoring',
            'inventaris.edit' => 'Ubah Data Aplikasi pada Daftar Monitoring',
            'inventaris.delete' => 'Hapus Data Aplikasi dari Daftar Monitoring',
        ];

        foreach ($permissions as $name => $displayName) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['module_group' => 'inventaris_aplikasi', 'display_name' => $displayName]
            );
        }

        // Ditambahkan tanpa menghapus hak akses kepala_bidang yang sudah ada
        Role::firstOrCreate(['name' => 'kepala_bidang', 'guard_name' => 'web'])
            ->givePermissionTo(array_keys($permissions));

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call(InventarisAplikasiSeeder::class);
    }
}
