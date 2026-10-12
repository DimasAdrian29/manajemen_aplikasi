<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 10 Poin Modul Resmi SIMBANGDA beserta katalog hak akses granular
        $modules = [
            'rbac' => [
                'label' => 'Modul 1: Manajemen Akses & Keamanan Sistem (RBAC)',
                'permissions' => [
                    'rbac.role.view' => 'Lihat Daftar Peran (Roles)',
                    'rbac.role.create' => 'Tambah Peran Baru',
                    'rbac.role.edit' => 'Ubah Nama Peran',
                    'rbac.role.delete' => 'Hapus Peran Kustom',
                    'rbac.matrix.manage' => 'Kelola Matriks Hak Akses Peran',
                    'rbac.user.view' => 'Lihat Daftar Pengguna',
                    'rbac.user.create' => 'Tambah Pengguna Baru',
                    'rbac.user.edit' => 'Ubah Data Pengguna & Penugasan Peran',
                    'rbac.user.delete' => 'Hapus / Nonaktifkan Pengguna',
                ],
            ],
            'master_data' => [
                'label' => 'Modul 2: Master Data & Regulasi SPBE',
                'permissions' => [
                    'master.instansi.view' => 'Lihat Data Instansi / OPD',
                    'master.instansi.create' => 'Tambah Instansi Baru',
                    'master.instansi.edit' => 'Ubah Data Instansi',
                    'master.instansi.delete' => 'Hapus Instansi',
                    'master.aplikasi.view' => 'Lihat Master Katalog Aplikasi',
                    'master.aplikasi.create' => 'Tambah Master Aplikasi',
                    'master.aplikasi.edit' => 'Ubah Master Aplikasi',
                    'master.aplikasi.delete' => 'Hapus Master Aplikasi',
                    'master.dokumen.view' => 'Lihat Syarat Regulasi Dokumen SPBE',
                    'master.dokumen.create' => 'Tambah Syarat Dokumen SPBE',
                    'master.dokumen.edit' => 'Ubah Syarat Dokumen SPBE',
                    'master.dokumen.delete' => 'Hapus Syarat Dokumen SPBE',
                ],
            ],
            'pengajuan' => [
                'label' => 'Modul 3: Layanan Usulan & Pengajuan OPD',
                'permissions' => [
                    'pengajuan.view' => 'Lihat Daftar & Detail Usulan Pengajuan',
                    'pengajuan.create' => 'Buat Permohonan Usulan Aplikasi Baru',
                    'pengajuan.edit' => 'Ubah Data Usulan (Status Draft / Revisi)',
                    'pengajuan.delete' => 'Hapus / Batalkan Usulan Pengajuan',
                    'pengajuan.review' => 'Telaah Usulan (Setujui / Tolak / Disposisi)',
                    'pengajuan.export' => 'Cetak Lembar Disposisi & Rekap Usulan',
                ],
            ],
            'project' => [
                'label' => 'Modul 4: Manajemen Proyek Pengembangan & Tim',
                'permissions' => [
                    'project.view' => 'Lihat Portofolio & Lembar Kerja Proyek',
                    'project.create' => 'Inisiasi Proyek Baru dari Usulan Sah',
                    'project.edit' => 'Ubah Informasi Umum Proyek',
                    'project.delete' => 'Hapus Data Proyek',
                    'project.manage_tim' => 'Bentuk & Atur Susunan Tim Pengembang',
                    'project.update_progres' => 'Input & Perbarui Progres Milestone Sprint',
                    'project.upload_dokumen' => 'Unggah Dokumen Perancangan (SRS/ERD/Figma)',
                    'project.review_dokumen' => 'Telaah & Evaluasi Dokumen Perancangan',
                    'project.ajukan_penundaan' => 'Ajukan Permohonan Penundaan Timeline Proyek',
                ],
            ],
            'testing_fungsional' => [
                'label' => 'Modul 5: Kendali Mutu & Uji Fungsional Aplikasi',
                'permissions' => [
                    'testing.fungsional.view' => 'Lihat Lembar & Riwayat Uji Fungsional',
                    'testing.fungsional.create' => 'Catat Sesi Uji Fungsional Baru',
                    'testing.fungsional.edit' => 'Ubah Hasil & Status Uji Fungsional',
                    'testing.fungsional.delete' => 'Hapus Catatan Uji Fungsional',
                    'testing.fungsional.upload' => 'Unggah Berkas Laporan Hasil Uji Fungsional',
                ],
            ],
            'security_csirt' => [
                'label' => 'Modul 6: Audit Keamanan Informasi & CSIRT',
                'permissions' => [
                    'security.audit.view' => 'Lihat Antrean & Riwayat Audit Keamanan',
                    'security.audit.create' => 'Catat Pengujian Kerentanan & Audit Pentest',
                    'security.audit.edit' => 'Ubah Rekomendasi Penanganan Keamanan',
                    'security.audit.delete' => 'Hapus Catatan Audit Keamanan',
                    'security.audit.issue_cert' => 'Terbitkan Surat & Rekomendasi CSIRT',
                ],
            ],
            'infrastruktur' => [
                'label' => 'Modul 7: Infrastruktur, Server Hosting & Decommissioning',
                'permissions' => [
                    'infra.deployment.view' => 'Lihat Antrean Proyek Siap Go-Live',
                    'infra.deployment.execute' => 'Eksekusi Go-Live & Alokasi Server Hosting',
                    'infra.hosting.view' => 'Lihat Katalog Hosting & Server Aktif',
                    'infra.hosting.edit' => 'Ubah Spesifikasi Hosting, VM & Jadwal Backup',
                    'infra.decommission.view' => 'Lihat Antrean Penonaktifan Sistem',
                    'infra.decommission.execute' => 'Eksekusi Decommissioning & Penutupan Hosting',
                ],
            ],
            'rapat' => [
                'label' => 'Modul 8: Rapat Koordinasi, Presensi & Notulensi Proyek',
                'permissions' => [
                    'rapat.view' => 'Lihat Agenda & Jadwal Rapat Proyek',
                    'rapat.create' => 'Jadwalkan Rapat & Unggah Surat Undangan',
                    'rapat.edit' => 'Ubah Agenda & Jadwal Rapat',
                    'rapat.delete' => 'Batalkan Jadwal Rapat',
                    'rapat.presensi' => 'Konfirmasi Presensi Kehadiran Peserta',
                    'rapat.notulensi' => 'Unggah Notulensi & Dokumentasi Foto Rapat',
                ],
            ],
            'eksekutif' => [
                'label' => 'Modul 9: Dasbor Eksekutif & Keputusan Strategis',
                'permissions' => [
                    'eksekutif.dashboard.view' => 'Akses Dasbor Makro Eksekutif SPBE',
                    'eksekutif.penundaan.review' => 'Setujui / Tolak Permohonan Penundaan Proyek',
                    'eksekutif.laporan.view' => 'Akses & Cetak Laporan Eksekutif Daerah',
                ],
            ],
            'audit_log' => [
                'label' => 'Modul 10: Audit Log, Monitoring & Notifikasi Sistem',
                'permissions' => [
                    'audit.trail.view' => 'Akses Jejak Rekam Aktivitas (Audit Trail)',
                    'notification.view' => 'Lihat & Kelola Notifikasi Sistem',
                ],
            ],
            'layanan_workflow' => [
                'label' => 'Modul 11: Konfigurasi Layanan & Workflow Dinamis',
                'permissions' => [
                    'layanan.view' => 'Lihat Daftar & Detail Layanan',
                    'layanan.create' => 'Tambah Layanan Baru',
                    'layanan.edit' => 'Ubah Data Layanan & Susunan Workflow',
                    'layanan.delete' => 'Hapus Layanan',
                    'workflow.view' => 'Lihat Master Workflow & Field',
                    'workflow.create' => 'Tambah Master Workflow Baru',
                    'workflow.edit' => 'Ubah Master Workflow & Konfigurasi Field',
                    'workflow.delete' => 'Hapus Master Workflow',
                ],
            ],
        ];

        // 1. Buat / Perbarui seluruh Permission di Database
        foreach ($modules as $moduleKey => $moduleData) {
            foreach ($moduleData['permissions'] as $permName => $displayName) {
                Permission::firstOrCreate(
                    ['name' => $permName, 'guard_name' => 'web'],
                    [
                        'module_group' => $moduleKey,
                        'display_name' => $displayName,
                    ]
                );
            }
        }

        // 2. Baseline Assignment Hak Akses ke Peran Standar Bawaan Sistem
        $rolesBaseline = [
            'superadmin' => Permission::all()->pluck('name')->toArray(),

            'admin_aptika' => [
                'pengajuan.view', 'pengajuan.review', 'pengajuan.export',
                'project.view', 'project.create', 'project.edit', 'project.manage_tim', 'project.review_dokumen',
                'testing.fungsional.view', 'testing.fungsional.create', 'testing.fungsional.edit', 'testing.fungsional.upload',
                'rapat.view', 'rapat.create', 'rapat.edit', 'rapat.presensi', 'rapat.notulensi',
                'security.audit.view', 'infra.hosting.view', 'infra.deployment.view',
                'master.instansi.view', 'master.aplikasi.view', 'notification.view',
                'layanan.view', 'layanan.create', 'layanan.edit', 'layanan.delete',
                'workflow.view', 'workflow.create', 'workflow.edit', 'workflow.delete',
            ],

            'admin_infrastruktur' => [
                'infra.deployment.view', 'infra.deployment.execute',
                'infra.hosting.view', 'infra.hosting.edit',
                'infra.decommission.view', 'infra.decommission.execute',
                'project.view', 'master.aplikasi.view', 'notification.view',
            ],

            'admin_persandian' => [
                'security.audit.view', 'security.audit.create', 'security.audit.edit', 'security.audit.issue_cert',
                'project.view', 'testing.fungsional.view', 'master.aplikasi.view', 'notification.view',
            ],

            'opd' => [
                'pengajuan.view', 'pengajuan.create', 'pengajuan.edit', 'pengajuan.delete',
                'master.aplikasi.view', 'notification.view',
            ],

            'ketua_tim' => [
                'project.view', 'project.update_progres', 'project.upload_dokumen', 'project.ajukan_penundaan',
                'rapat.view', 'rapat.create', 'rapat.edit', 'rapat.presensi', 'rapat.notulensi',
                'notification.view',
            ],

            'anggota_tim' => [
                'project.view', 'project.update_progres', 'project.upload_dokumen',
                'rapat.view', 'rapat.presensi',
                'notification.view',
            ],

            'kepala_bidang' => [
                'eksekutif.dashboard.view', 'eksekutif.penundaan.review', 'eksekutif.laporan.view',
                'project.view', 'pengajuan.view', 'master.aplikasi.view', 'notification.view',
            ],

            'kepala_dinas' => [
                'eksekutif.dashboard.view', 'eksekutif.laporan.view',
                'project.view', 'pengajuan.view', 'master.aplikasi.view', 'infra.hosting.view', 'notification.view',
            ],
        ];

        foreach ($rolesBaseline as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
