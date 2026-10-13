<?php

namespace Database\Seeders;

use App\Models\Aplikasi;
use App\Models\DokumenPengajuan;
use App\Models\DokumenPerancangan;
use App\Models\HostingAplikasi;
use App\Models\Instansi;
use App\Models\Layanan;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\PengujianKeamanan;
use App\Models\Penundaan;
use App\Models\PesertaRapat;
use App\Models\Project;
use App\Models\RapatProject;
use App\Models\RiwayatHosting;
use App\Models\TahapanProject;
use App\Models\TestingAplikasi;
use App\Models\TimProject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive lifecycle conditions.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. Roles & Permissions (Spatie)
        // =========================================================================
        $this->call(PermissionSeeder::class);

        // =========================================================================
        // 2. Master Instansi OPD Pemerintah Provinsi Riau
        // =========================================================================
        $diskominfo = Instansi::firstOrCreate(
            ['nama_instansi' => 'Dinas Komunikasi, Informatika dan Statistik Provinsi Riau'],
            [
                'email' => 'diskominfo@riau.go.id',
                'no_telepon' => '0761-23456',
                'alamat' => 'Jl. Jend. Sudirman No. 460, Pekanbaru',
            ]
        );

        $dinkes = Instansi::firstOrCreate(
            ['nama_instansi' => 'Dinas Kesehatan Provinsi Riau'],
            [
                'email' => 'dinkes@riau.go.id',
                'no_telepon' => '0761-34567',
                'alamat' => 'Jl. Cut Nyak Dien No. 3, Pekanbaru',
            ]
        );

        $disdik = Instansi::firstOrCreate(
            ['nama_instansi' => 'Dinas Pendidikan Provinsi Riau'],
            [
                'email' => 'disdik@riau.go.id',
                'no_telepon' => '0761-45678',
                'alamat' => 'Jl. Cut Nyak Dien No. 5, Pekanbaru',
            ]
        );

        $bapenda = Instansi::firstOrCreate(
            ['nama_instansi' => 'Badan Pendapatan Daerah Provinsi Riau'],
            [
                'email' => 'bapenda@riau.go.id',
                'no_telepon' => '0761-56789',
                'alamat' => 'Jl. Jend. Sudirman No. 200, Pekanbaru',
            ]
        );

        $rsud = Instansi::firstOrCreate(
            ['nama_instansi' => 'RSUD Arifin Achmad Provinsi Riau'],
            [
                'email' => 'rsud.arifinachmad@riau.go.id',
                'no_telepon' => '0761-21618',
                'alamat' => 'Jl. Diponegoro No. 2, Pekanbaru',
            ]
        );

        $pupr = Instansi::firstOrCreate(
            ['nama_instansi' => 'Dinas Pekerjaan Umum, Penataan Ruang, Perumahan, Kawasan Permukiman dan Pertanahan'],
            [
                'email' => 'puprpkpp@riau.go.id',
                'no_telepon' => '0761-32145',
                'alamat' => 'Jl. SM. Amin No. 92, Pekanbaru',
            ]
        );

        $dlhk = Instansi::firstOrCreate(
            ['nama_instansi' => 'Dinas Lingkungan Hidup dan Kehutanan Provinsi Riau'],
            [
                'email' => 'dlhk@riau.go.id',
                'no_telepon' => '0761-85632',
                'alamat' => 'Jl. Jend. Sudirman No. 468, Pekanbaru',
            ]
        );

        // =========================================================================
        // 3. Pengguna Sistem (Akun Demo Semua Peran)
        // =========================================================================
        $defaultPassword = Hash::make('password123');

        // Superadmin Utama
        $superadmin = User::firstOrCreate(
            ['username' => 'kaido'],
            [
                'name' => 'Kaido Superadministrator',
                'email' => 'kaido@admin.com',
                'no_hp' => '081200000001',
                'jabatan' => 'Superadministrator Sistem Utama',
                'password' => Hash::make('wanokuni123'),
                'instansi_id' => $diskominfo->id,
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        // Admin APTIKA
        $adminAptika = User::firstOrCreate(
            ['username' => 'admin_aptika'],
            [
                'name' => 'Aisyah Putri, S.Kom',
                'email' => 'aptika@riau.go.id',
                'no_hp' => '081200000002',
                'jabatan' => 'Pranata Komputer Ahli Muda - APTIKA',
                'password' => $defaultPassword,
                'instansi_id' => $diskominfo->id,
            ]
        );
        $adminAptika->syncRoles(['admin_aptika']);

        // Admin Infrastruktur (TIK & Server)
        $adminInfrastruktur = User::firstOrCreate(
            ['username' => 'admin_infrastruktur'],
            [
                'name' => 'Budi Wicaksono, ST',
                'email' => 'infrastruktur@riau.go.id',
                'no_hp' => '081200000003',
                'jabatan' => 'Administrator Jaringan & Server TIK',
                'password' => $defaultPassword,
                'instansi_id' => $diskominfo->id,
            ]
        );
        $adminInfrastruktur->syncRoles(['admin_infrastruktur']);

        // Admin Persandian & Keamanan (CSIRT)
        $adminPersandian = User::firstOrCreate(
            ['username' => 'admin_persandian'],
            [
                'name' => 'Sandi Pratama, M.Kom',
                'email' => 'persandian@riau.go.id',
                'no_hp' => '081200000004',
                'jabatan' => 'Analis CSIRT / Sandiman Ahli Muda',
                'password' => $defaultPassword,
                'instansi_id' => $diskominfo->id,
            ]
        );
        $adminPersandian->syncRoles(['admin_persandian']);

        // Operator OPD
        $opdDinkes = User::firstOrCreate(
            ['username' => 'opd_dinkes'],
            [
                'name' => 'drg. Rahmat Hidayat',
                'email' => 'opd.dinkes@riau.go.id',
                'no_hp' => '081200000005',
                'jabatan' => 'Pranata Komputer Dinas Kesehatan',
                'password' => $defaultPassword,
                'instansi_id' => $dinkes->id,
            ]
        );
        $opdDinkes->syncRoles(['opd']);

        $opdBapenda = User::firstOrCreate(
            ['username' => 'opd_bapenda'],
            [
                'name' => 'Hendra Kusuma, SE',
                'email' => 'opd.bapenda@riau.go.id',
                'no_hp' => '081200000006',
                'jabatan' => 'Kasubag TI Badan Pendapatan Daerah',
                'password' => $defaultPassword,
                'instansi_id' => $bapenda->id,
            ]
        );
        $opdBapenda->syncRoles(['opd']);

        $opdDisdik = User::firstOrCreate(
            ['username' => 'opd_disdik'],
            [
                'name' => 'Dewi Sartika, M.Pd',
                'email' => 'opd.disdik@riau.go.id',
                'no_hp' => '081200000007',
                'jabatan' => 'Tim Pengelola SIM Dinas Pendidikan',
                'password' => $defaultPassword,
                'instansi_id' => $disdik->id,
            ]
        );
        $opdDisdik->syncRoles(['opd']);

        $opdPupr = User::firstOrCreate(
            ['username' => 'opd_pupr'],
            [
                'name' => 'Ir. Doni Prasetya',
                'email' => 'opd.pupr@riau.go.id',
                'no_hp' => '081200000011',
                'jabatan' => 'Staf TI Dinas PUPR Riau',
                'password' => $defaultPassword,
                'instansi_id' => $pupr->id,
            ]
        );
        $opdPupr->syncRoles(['opd']);

        $opdDlhk = User::firstOrCreate(
            ['username' => 'opd_dlhk'],
            [
                'name' => 'Fajar Nugroho, S.Hut',
                'email' => 'opd.dlhk@riau.go.id',
                'no_hp' => '081200000012',
                'jabatan' => 'Pengelola Data Digital DLHK Riau',
                'password' => $defaultPassword,
                'instansi_id' => $dlhk->id,
            ]
        );
        $opdDlhk->syncRoles(['opd']);

        $opdRsud = User::firstOrCreate(
            ['username' => 'opd_rsud'],
            [
                'name' => 'dr. Maya Indah, Sp.A',
                'email' => 'opd.rsud@riau.go.id',
                'no_hp' => '081200000013',
                'jabatan' => 'Kepala Unit SIMRS Arifin Achmad',
                'password' => $defaultPassword,
                'instansi_id' => $rsud->id,
            ]
        );
        $opdRsud->syncRoles(['opd']);

        // Tenaga Teknis Pengembang (Universal Developer Pool - role: anggota_tim)
        $devAhmad = User::firstOrCreate(
            ['username' => 'ahmad_lead'],
            [
                'name' => 'Ahmad Fauzi, S.Kom',
                'email' => 'ahmad@riau.go.id',
                'no_hp' => '081200000008',
                'jabatan' => 'Lead Fullstack Developer / PM',
                'password' => $defaultPassword,
                'instansi_id' => null,
            ]
        );
        $devAhmad->syncRoles(['anggota_tim']);

        $devBambang = User::firstOrCreate(
            ['username' => 'bambang_dev'],
            [
                'name' => 'Bambang Sudiro, ST',
                'email' => 'bambang@riau.go.id',
                'no_hp' => '081200000009',
                'jabatan' => 'Backend Developer (PHP/Laravel)',
                'password' => $defaultPassword,
                'instansi_id' => null,
            ]
        );
        $devBambang->syncRoles(['anggota_tim']);

        $devRian = User::firstOrCreate(
            ['username' => 'rian_dev'],
            [
                'name' => 'Rian Pratama',
                'email' => 'rian@riau.go.id',
                'no_hp' => '081200000014',
                'jabatan' => 'Frontend Developer (Tailwind/Vue)',
                'password' => $defaultPassword,
                'instansi_id' => null,
            ]
        );
        $devRian->syncRoles(['anggota_tim']);

        $devSiti = User::firstOrCreate(
            ['username' => 'siti_analyst'],
            [
                'name' => 'Siti Rahma, S.Kom',
                'email' => 'siti@riau.go.id',
                'no_hp' => '081200000015',
                'jabatan' => 'System Analyst & UI Designer',
                'password' => $defaultPassword,
                'instansi_id' => null,
            ]
        );
        $devSiti->syncRoles(['anggota_tim']);

        $devDoni = User::firstOrCreate(
            ['username' => 'doni_qa'],
            [
                'name' => 'Doni Saputra, ST',
                'email' => 'doni@riau.go.id',
                'no_hp' => '081200000016',
                'jabatan' => 'QA & Software Tester',
                'password' => $defaultPassword,
                'instansi_id' => null,
            ]
        );
        $devDoni->syncRoles(['anggota_tim']);

        $devDimz = User::firstOrCreate(
            ['username' => 'dimz'],
            [
                'name' => 'dimz',
                'email' => 'dimasadriangg069@gmail.com',
                'no_hp' => '0899',
                'jabatan' => 'Frontend Developer (Tailwind/Vue/React)',
                'password' => $defaultPassword,
                'instansi_id' => null,
            ]
        );
        $devDimz->syncRoles(['anggota_tim']);

        // Akun Pimpinan Eksekutif (Pending sesuai permintaan)
        $kabidAptika = User::firstOrCreate(
            ['username' => 'kabid_aptika'],
            [
                'name' => 'Kepala Bidang APTIKA',
                'email' => 'kabid.aptika@riau.go.id',
                'no_hp' => '081200000017',
                'jabatan' => 'Kepala Bidang Aptika',
                'password' => $defaultPassword,
                'instansi_id' => $diskominfo->id,
            ]
        );
        $kabidAptika->syncRoles(['kepala_bidang']);

        $kadisKominfo = User::firstOrCreate(
            ['username' => 'kadis_kominfo'],
            [
                'name' => 'Kepala Dinas Kominfo',
                'email' => 'kadin@riau.go.id',
                'no_hp' => '081200000018',
                'jabatan' => 'Kepala Dinas Kominfo Riau',
                'password' => $defaultPassword,
                'instansi_id' => $diskominfo->id,
            ]
        );
        $kadisKominfo->syncRoles(['kepala_dinas']);

        // =========================================================================
        // 4. Master Dokumen Regulasi SPBE
        // =========================================================================
        $masterDokumen = [
            [
                'nama_dokumen' => 'Surat Pengantar Kepala Dinas',
                'kode_dokumen' => 'surat_pengantar',
                'deskripsi' => 'Surat permohonan resmi bertandatangan Kepala Dinas / Pejabat Berwenang dan berstempel dinas resmi.',
                'wajib' => true,
                'berlaku_untuk' => 'semua',
                'format_diizinkan' => 'pdf',
                'max_size_kb' => 10240,
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'nama_dokumen' => 'Proposal Teknis / KAK (Kerangka Acuan Kerja)',
                'kode_dokumen' => 'proposal_teknis',
                'deskripsi' => 'Dokumen KAK memuat latar belakang, maksud & tujuan, ruang lingkup proses bisnis, serta arsitektur usulan aplikasi.',
                'wajib' => true,
                'berlaku_untuk' => 'new_app',
                'format_diizinkan' => 'pdf',
                'max_size_kb' => 15360,
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'nama_dokumen' => 'Surat Pernyataan Tanggung Jawab Mutlak (SPTJM)',
                'kode_dokumen' => 'sptjm',
                'deskripsi' => 'SPTJM kesiapan sumber daya manusia pengelola sistem, komitmen input data, dan pemeliharaan aplikasi.',
                'wajib' => true,
                'berlaku_untuk' => 'semua',
                'format_diizinkan' => 'pdf',
                'max_size_kb' => 5120,
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'nama_dokumen' => 'Kajian Urgensi & Evaluasi Sistem Berjalan',
                'kode_dokumen' => 'kajian_pengembangan',
                'deskripsi' => 'Dokumen evaluasi kelemahan fitur sistem eksisting dan rincian kebutuhan modul pembaruan yang diajukan.',
                'wajib' => true,
                'berlaku_untuk' => 'app_development',
                'format_diizinkan' => 'pdf',
                'max_size_kb' => 10240,
                'is_active' => true,
                'urutan' => 4,
            ],
            [
                'nama_dokumen' => 'Berita Acara Justifikasi Penonaktifan Aplikasi',
                'kode_dokumen' => 'rekomendasi_penonaktifan',
                'deskripsi' => 'Surat justifikasi penghentian operasional, integrasi/migrasi data ke sistem pengganti, dan penutupan domain.',
                'wajib' => true,
                'berlaku_untuk' => 'app_deactivation',
                'format_diizinkan' => 'pdf',
                'max_size_kb' => 10240,
                'is_active' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($masterDokumen as $dok) {
            MasterDokumenPengajuan::firstOrCreate(['kode_dokumen' => $dok['kode_dokumen']], $dok);
        }

        $this->call(LayananWorkflowSeeder::class);
        $this->call(InventarisAplikasiSeeder::class);

        $layananBaru = Layanan::where('slug', 'pembangunan-aplikasi-baru')->first();
        $layananPengembangan = Layanan::where('slug', 'pengembangan-aplikasi-eksisting')->first();
        $layananPenonaktifan = Layanan::where('slug', 'penonaktifan-aplikasi')->first();

        // =========================================================================
        // 5. Master Direktori Aplikasi
        // =========================================================================
        $appEpuskesmas = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'e-Puskesmas Riau'],
            [
                'instansi_id' => $dinkes->id,
                'deskripsi' => 'Sistem rekam medis elektronik dan pencatatan pelayanan puskesmas se-Provinsi Riau.',
                'url_aplikasi' => 'https://epuskesmas.riau.go.id',
                'status' => 'pengembangan',
            ]
        );

        $appSatuData = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'Portal Satu Data Riau'],
            [
                'instansi_id' => $diskominfo->id,
                'deskripsi' => 'Portal integrasi data statistik sektoral dan interoperabilitas data daerah.',
                'url_aplikasi' => 'https://satudata.riau.go.id',
                'status' => 'pengembangan',
            ]
        );

        $appEsamsat = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'e-Samsat & Pajak Daerah Riau'],
            [
                'instansi_id' => $bapenda->id,
                'deskripsi' => 'Layanan pembayaran PKB, BBNKB, dan pajak retribusi daerah terpadu secara elektronik.',
                'url_aplikasi' => 'https://esamsat.riau.go.id',
                'status' => 'pengembangan',
            ]
        );

        $appPpdb = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'PPDB Online SMA/SMK Negeri Riau'],
            [
                'instansi_id' => $disdik->id,
                'deskripsi' => 'Penerimaan Peserta Didik Baru jalur zonasi, afirmasi, prestasi, dan perpindahan tugas.',
                'url_aplikasi' => 'https://ppdb.riau.go.id',
                'status' => 'pengembangan',
            ]
        );

        $appSigawat = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'SI-Gawat Darurat 119 Riau'],
            [
                'instansi_id' => $dinkes->id,
                'deskripsi' => 'Sistem penanganan panggilan kegawatdaruratan medis dan dispatch ambulans terpadu.',
                'url_aplikasi' => 'https://sigawat.riau.go.id',
                'status' => 'aktif',
            ]
        );

        $appEjalan = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'e-Jalan Riau (GIS Pemeliharaan Jalan)'],
            [
                'instansi_id' => $pupr->id,
                'deskripsi' => 'Sistem informasi geografis pemantauan kondisi mantap dan laporan kerusakan jalan provinsi.',
                'url_aplikasi' => 'https://ejalan.riau.go.id',
                'status' => 'pengembangan',
            ]
        );

        $appAmdal = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'AMDAL Online Riau Hijau'],
            [
                'instansi_id' => $dlhk->id,
                'deskripsi' => 'Sistem permohonan dan pelacakan izin kelayakan lingkungan hidup terintegrasi.',
                'url_aplikasi' => 'https://amdal.riau.go.id',
                'status' => 'pengembangan',
            ]
        );

        $appSimpadLama = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'Sistem Informasi Manajemen Pajak Daerah (SIMPAD Lama v1.0)'],
            [
                'instansi_id' => $bapenda->id,
                'deskripsi' => 'Sistem desktop legacy monolitik yang telah digantikan oleh e-Samsat Terpadu.',
                'url_aplikasi' => 'http://simpad-old.riau.go.id',
                'status' => 'nonaktif',
            ]
        );

        // =========================================================================
        // 6. Usulan Pengajuan OPD (Mencakup Segala Kondisi dan Siklus Hidup)
        // =========================================================================

        // Usulan 1: Approved -> Menjadi Proyek 1 (in_development)
        $pengajuan1 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-001'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => $appEpuskesmas->id,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'e-Puskesmas Riau',
                'deskripsi_kebutuhan' => 'Pembangunan aplikasi sistem informasi rekam medis elektronik puskesmas se-Provinsi Riau sesuai standar SatuSehat Kemenkes.',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081277889901',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Usulan sangat mendesak untuk transformasi digital layanan kesehatan primer. Disetujui dibangun internal APTIKA.',
                'tanggal_penilaian' => now()->subDays(12),
            ]
        );

        // Usulan 2: Approved -> Menjadi Proyek 2 (in_development)
        $pengajuan2 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-002'],
            [
                'instansi_id' => $diskominfo->id,
                'aplikasi_id' => $appSatuData->id,
                'layanan_id' => $layananPengembangan->id,
                'jenis_pengajuan' => 'app_development',
                'nama_aplikasi' => 'Portal Satu Data Riau',
                'deskripsi_kebutuhan' => 'Peningkatan modul integrasi REST API dan dasbor analitik Satu Data Riau untuk koneksi ke SDI Nasional.',
                'nama_pemohon' => 'Ir. Faisal Arifin',
                'no_hp_pemohon' => '081366554402',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Pengembangan modul integrasi API disetujui sesuai regulasi Perpres 39/2019.',
                'tanggal_penilaian' => now()->subDays(10),
            ]
        );

        // Usulan 3: Approved -> Menjadi Proyek 3 (security_testing)
        $pengajuan3 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-003'],
            [
                'instansi_id' => $bapenda->id,
                'aplikasi_id' => $appEsamsat->id,
                'layanan_id' => $layananPengembangan->id,
                'jenis_pengajuan' => 'app_development',
                'nama_aplikasi' => 'e-Samsat & Pajak Daerah Riau',
                'deskripsi_kebutuhan' => 'Integrasi gateway pembayaran QRIS, Virtual Account Bank Riau Kepri Syariah, dan validasi data Korlantas Polri.',
                'nama_pemohon' => 'Hendra Kusuma, SE',
                'no_hp_pemohon' => '081266778803',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Kebutuhan integrasi pendapatan asli daerah disetujui dengan prioritas tinggi.',
                'tanggal_penilaian' => now()->subDays(25),
            ]
        );

        // Usulan 4: Approved -> Menjadi Proyek 4 (deployment)
        $pengajuan4 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-004'],
            [
                'instansi_id' => $disdik->id,
                'aplikasi_id' => $appPpdb->id,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'PPDB Online SMA/SMK Negeri Riau',
                'deskripsi_kebutuhan' => 'Pembangunan sistem seleksi penerimaan siswa baru terpusat dengan komputasi jalur zonasi berbasis geolokasi.',
                'nama_pemohon' => 'Dewi Sartika, M.Pd',
                'no_hp_pemohon' => '081255443304',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Proposal lengkap, arsitektur microservices disetujui untuk mengantisipasi lonjakan trafik pendaftaran.',
                'tanggal_penilaian' => now()->subDays(40),
            ]
        );

        // Usulan 5: Approved -> Menjadi Proyek 5 (live)
        $pengajuan5 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-005'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => $appSigawat->id,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'SI-Gawat Darurat 119 Riau',
                'deskripsi_kebutuhan' => 'Sistem telekomunikasi dan respon cepat penanganan pasien gawat darurat terhubung ke seluruh RSUD rujukan.',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081277889905',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Disetujui. Layanan publik esensial kesehatan masyarakat.',
                'tanggal_penilaian' => now()->subDays(60),
            ]
        );

        // Usulan 6: Approved -> Menjadi Proyek 6 (delayed)
        $pengajuan6 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-006'],
            [
                'instansi_id' => $pupr->id,
                'aplikasi_id' => $appEjalan->id,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'e-Jalan Riau (GIS Pemeliharaan Jalan)',
                'deskripsi_kebutuhan' => 'Peta interaktif kondisi jalan dan jembatan provinsi dengan pelaporan foto koordinat GPS dari masyarakat.',
                'nama_pemohon' => 'Ir. Doni Prasetya',
                'no_hp_pemohon' => '081244332206',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Disetujui untuk percepatan penanganan infrastruktur jalan provinsi.',
                'tanggal_penilaian' => now()->subDays(30),
            ]
        );

        // Usulan 7: Approved -> Menjadi Proyek 7 (functional_testing)
        $pengajuan7 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-007'],
            [
                'instansi_id' => $dlhk->id,
                'aplikasi_id' => $appAmdal->id,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'AMDAL Online Riau Hijau',
                'deskripsi_kebutuhan' => 'Digitalisasi alur rekomendasi persetujuan lingkungan hidup dan transparansi uji kelayakan publik.',
                'nama_pemohon' => 'Fajar Nugroho, S.Hut',
                'no_hp_pemohon' => '081233221107',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Disetujui, sesuai rencana aksi Riau Hijau.',
                'tanggal_penilaian' => now()->subDays(20),
            ]
        );

        // Usulan 8: Approved (BELUM ADA PROYEK) -> Muncul di dropdown inisiasi proyek baru APTIKA
        $pengajuan8 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-008'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => null,
                'layanan_id' => $layananPengembangan->id,
                'jenis_pengajuan' => 'app_development',
                'nama_aplikasi' => 'Sistem Logistik Farmasi & Gudang Obat Terpadu',
                'deskripsi_kebutuhan' => 'Pembaruan modul stok opname obat, peringatan kadaluarsa vaksin, dan distribusi logistik ke 12 kabupaten/kota.',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081277889908',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Telah lolos telaah arsitektur. Siap diinisiasi pembentukan tim pengembang oleh Bidang APTIKA.',
                'tanggal_penilaian' => now()->subDays(2),
            ]
        );

        // Usulan 9: Under Review -> Sedang ditelaah Admin APTIKA
        $pengajuan9 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-009'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => null,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'SIPosyandu - Pemantauan Gizi & Stunting Balita',
                'deskripsi_kebutuhan' => 'Pencatatan data antropometri balita tingkat posyandu untuk intervensi dini penanganan stunting daerah.',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081277889909',
                'status' => 'under_review',
                'dinilai_oleh' => null,
                'catatan_penilaian' => null,
            ]
        );

        // Usulan 10: Submitted -> Usulan baru masuk menunggu telaah
        $pengajuan10 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-010'],
            [
                'instansi_id' => $rsud->id,
                'aplikasi_id' => null,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'Sistem Antrean Poliklinik & Telekonsultasi RSUD',
                'deskripsi_kebutuhan' => 'Pendaftaran antrean pasien BPJS dan non-BPJS secara mobile serta konsultasi online dokter spesialis.',
                'nama_pemohon' => 'dr. Maya Indah, Sp.A',
                'no_hp_pemohon' => '081288990010',
                'status' => 'submitted',
            ]
        );

        // Usulan 11: Rejected -> Usulan ditolak oleh APTIKA
        $pengajuan11 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-011'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => null,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'Aplikasi Game Edukasi Gizi Anak Balita',
                'deskripsi_kebutuhan' => 'Aplikasi permainan mini interaktif pengenalan makanan bergizi untuk anak usia dini.',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081277889911',
                'status' => 'rejected',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Ditolak: Tidak sesuai dengan prioritas proses bisnis SPBE Provinsi Riau. Sarana edukasi dianjurkan memanfaatkan portal dan konten media sosial yang telah ada.',
                'tanggal_penilaian' => now()->subDays(5),
            ]
        );

        // Usulan 12: Draft -> Masih draf di OPD Dinkes
        $pengajuan12 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-012'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => null,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'Sistem Pencatatan Vaksinasi Rabies Terpadu',
                'deskripsi_kebutuhan' => 'Pemetaan wilayah endemik dan ketersediaan vaksin anti-rabies di puskesmas perbatasan.',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081277889912',
                'status' => 'draft',
            ]
        );

        // Usulan 13: Approved Decommissioning -> Muncul di Antrean Penonaktifan Infrastruktur
        $pengajuan13 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-202610-013'],
            [
                'instansi_id' => $bapenda->id,
                'aplikasi_id' => $appSimpadLama->id,
                'layanan_id' => $layananPenonaktifan->id,
                'jenis_pengajuan' => 'app_deactivation',
                'nama_aplikasi' => 'Sistem Informasi Manajemen Pajak Daerah (SIMPAD Lama v1.0)',
                'deskripsi_kebutuhan' => 'Penutupan operasional server dan pengalihan domain simpad-old.riau.go.id karena seluruh basis data telah termigrasi 100% ke e-Samsat Terpadu.',
                'nama_pemohon' => 'Hendra Kusuma, SE',
                'no_hp_pemohon' => '081266778813',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Permohonan penonaktifan telah diverifikasi lengkap. Berita acara pemindahan data sah. Diteruskan ke Bidang Infrastruktur untuk eksekusi decommissioning server.',
                'tanggal_penilaian' => now()->subDays(3),
            ]
        );

        // Lampirkan contoh Dokumen Pengajuan
        $masterSurat = MasterDokumenPengajuan::where('kode_dokumen', 'surat_pengantar')->first();
        $masterKak = MasterDokumenPengajuan::where('kode_dokumen', 'proposal_teknis')->first();
        $masterSptjm = MasterDokumenPengajuan::where('kode_dokumen', 'sptjm')->first();

        foreach ([$pengajuan1, $pengajuan2, $pengajuan3, $pengajuan4, $pengajuan8, $pengajuan10] as $p) {
            if ($masterSurat) {
                DokumenPengajuan::firstOrCreate(
                    ['pengajuan_id' => $p->id, 'master_dokumen_id' => $masterSurat->id],
                    [
                        'nama_file_asli' => 'Surat_Pengantar_Resmi_'.$p->kode_pengajuan.'.pdf',
                        'file_path' => 'dokumen_pengajuan/surat_pengantar_dummy.pdf',
                        'ukuran_file' => 1024 * 350,
                        'catatan' => 'Surat bertandatangan Kepala Dinas dan stempel dinas.',
                    ]
                );
            }
            if ($masterKak) {
                DokumenPengajuan::firstOrCreate(
                    ['pengajuan_id' => $p->id, 'master_dokumen_id' => $masterKak->id],
                    [
                        'nama_file_asli' => 'KAK_Proposal_Teknis_'.$p->kode_pengajuan.'.pdf',
                        'file_path' => 'dokumen_pengajuan/kak_proposal_dummy.pdf',
                        'ukuran_file' => 1024 * 1250,
                        'catatan' => 'Kerangka Acuan Kerja memuat kebutuhan fungsional & arsitektur.',
                    ]
                );
            }
            if ($masterSptjm) {
                DokumenPengajuan::firstOrCreate(
                    ['pengajuan_id' => $p->id, 'master_dokumen_id' => $masterSptjm->id],
                    [
                        'nama_file_asli' => 'SPTJM_Komitmen_SDM_'.$p->kode_pengajuan.'.pdf',
                        'file_path' => 'dokumen_pengajuan/sptjm_dummy.pdf',
                        'ukuran_file' => 1024 * 180,
                        'catatan' => 'Surat Tanggung Jawab Mutlak kesiapan admin OPD.',
                    ]
                );
            }
        }

        // =========================================================================
        // 7. Proyek Pengembangan Aplikasi (Menampilkan Segala Siklus Hidup)
        // =========================================================================

        // -------------------------------------------------------------------------
        // PROYEK 1: e-Puskesmas Riau -> Status: in_development (Progres 40%)
        // Ahmad: KETUA TIM | Bambang: ANGGOTA | Rian: ANGGOTA
        // -------------------------------------------------------------------------
        $project1 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan1->id],
            [
                'aplikasi_id' => $appEpuskesmas->id,
                'nama_project' => 'Pembangunan Sistem Informasi e-Puskesmas Terintegrasi',
                'tanggal_mulai' => now()->subDays(10)->toDateString(),
                'tanggal_selesai' => now()->addMonths(2)->toDateString(),
                'progres_total' => 40,
                'status' => 'in_development',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project1->id, 'user_id' => $devAhmad->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project1->id, 'user_id' => $devBambang->id], ['peran' => 'anggota']);
        TimProject::firstOrCreate(['project_id' => $project1->id, 'user_id' => $devRian->id], ['peran' => 'anggota']);

        // Tahapan Sprint Proyek 1
        TahapanProject::firstOrCreate(
            ['project_id' => $project1->id, 'urutan' => 1],
            ['nama_tahapan' => 'Analisis Kebutuhan & Dokumen SRS', 'persentase' => 100, 'status' => 'selesai']
        );
        TahapanProject::firstOrCreate(
            ['project_id' => $project1->id, 'urutan' => 2],
            ['nama_tahapan' => 'Desain Arsitektur Basis Data & Prototipe UI/UX', 'persentase' => 100, 'status' => 'selesai']
        );
        TahapanProject::firstOrCreate(
            ['project_id' => $project1->id, 'urutan' => 3],
            ['nama_tahapan' => 'Pengembangan Modul Pelayanan & API SatuSehat', 'persentase' => 50, 'status' => 'dalam_pengerjaan']
        );
        TahapanProject::firstOrCreate(
            ['project_id' => $project1->id, 'urutan' => 4],
            ['nama_tahapan' => 'Integrasi Antarmuka Web Rekam Medis', 'persentase' => 10, 'status' => 'dalam_pengerjaan']
        );

        // Dokumen Perancangan Proyek 1
        DokumenPerancangan::firstOrCreate(
            ['project_id' => $project1->id, 'jenis_dokumen' => 'srs'],
            [
                'dinilai_oleh' => $adminAptika->id,
                'file_dokumen' => 'perancangan/srs_epuskesmas.pdf',
                'status' => 'disetujui',
                'catatan' => 'Spesifikasi kebutuhan telah diverifikasi bersama analis.',
                'tanggal_penilaian' => now()->subDays(6),
            ]
        );
        DokumenPerancangan::firstOrCreate(
            ['project_id' => $project1->id, 'jenis_dokumen' => 'erd'],
            [
                'dinilai_oleh' => $adminAptika->id,
                'file_dokumen' => 'perancangan/erd_epuskesmas.pdf',
                'status' => 'disetujui',
                'catatan' => 'Normalisasi skema relasi database teruji baik.',
                'tanggal_penilaian' => now()->subDays(5),
            ]
        );
        DokumenPerancangan::firstOrCreate(
            ['project_id' => $project1->id, 'jenis_dokumen' => 'wireframe'],
            [
                'dinilai_oleh' => null,
                'file_dokumen' => 'https://www.figma.com/design/sample-epuskesmas-riau-prototype',
                'status' => 'menunggu_review',
                'catatan' => 'Prototipe UI/UX antarmuka rekam medis & pendaftaran puskesmas.',
            ]
        );

        // -------------------------------------------------------------------------
        // PROYEK 2: Satu Data Riau API -> Status: in_development (Progres 25%)
        // Bambang: KETUA TIM | Ahmad: ANGGOTA | Siti: ANGGOTA (Role Fleksibel/Silang)
        // -------------------------------------------------------------------------
        $project2 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan2->id],
            [
                'aplikasi_id' => $appSatuData->id,
                'nama_project' => 'Integrasi API & Modul Analitik Satu Data Riau',
                'tanggal_mulai' => now()->subDays(8)->toDateString(),
                'tanggal_selesai' => now()->addMonths(3)->toDateString(),
                'progres_total' => 25,
                'status' => 'in_development',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project2->id, 'user_id' => $devBambang->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project2->id, 'user_id' => $devAhmad->id], ['peran' => 'anggota']);
        TimProject::firstOrCreate(['project_id' => $project2->id, 'user_id' => $devSiti->id], ['peran' => 'anggota']);

        TahapanProject::firstOrCreate(
            ['project_id' => $project2->id, 'urutan' => 1],
            ['nama_tahapan' => 'Harmonisasi Standar Metadata Data Sektoral', 'persentase' => 100, 'status' => 'selesai']
        );
        TahapanProject::firstOrCreate(
            ['project_id' => $project2->id, 'urutan' => 2],
            ['nama_tahapan' => 'Arsitektur REST API Gateway & Autentikasi OAuth2', 'persentase' => 50, 'status' => 'dalam_pengerjaan']
        );

        // Dokumen Perancangan Proyek 2
        DokumenPerancangan::firstOrCreate(
            ['project_id' => $project2->id, 'jenis_dokumen' => 'srs'],
            [
                'dinilai_oleh' => null,
                'file_dokumen' => 'https://docs.google.com/document/d/sample-srs-satudata-riau',
                'status' => 'menunggu_review',
                'catatan' => 'Draft spesifikasi teknis interoperabilitas Open API Satu Data Riau.',
            ]
        );
        DokumenPerancangan::firstOrCreate(
            ['project_id' => $project2->id, 'jenis_dokumen' => 'arsitektur'],
            [
                'dinilai_oleh' => $adminAptika->id,
                'file_dokumen' => 'perancangan/arsitektur_satudata.pdf',
                'status' => 'disetujui',
                'catatan' => 'Diagram topologi API gateway & broker event Kafka telah disetujui.',
                'tanggal_penilaian' => now()->subDays(3),
            ]
        );

        // -------------------------------------------------------------------------
        // PROYEK 3: e-Samsat Riau -> Status: security_testing (Progres 75%)
        // Muncul di Pengujian Keamanan Persandian (CSIRT)
        // -------------------------------------------------------------------------
        $project3 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan3->id],
            [
                'aplikasi_id' => $appEsamsat->id,
                'nama_project' => 'Modernisasi e-Samsat & Pembayaran Pajak Daerah Terpadu',
                'tanggal_mulai' => now()->subDays(20)->toDateString(),
                'tanggal_selesai' => now()->addMonth()->toDateString(),
                'progres_total' => 75,
                'status' => 'security_testing',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project3->id, 'user_id' => $devAhmad->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project3->id, 'user_id' => $devBambang->id], ['peran' => 'anggota']);
        TimProject::firstOrCreate(['project_id' => $project3->id, 'user_id' => $devDoni->id], ['peran' => 'anggota']);

        // Uji Fungsional APTIKA (Sudah Lulus)
        TestingAplikasi::firstOrCreate(
            ['project_id' => $project3->id, 'penguji_id' => $adminAptika->id],
            [
                'file_hasil' => 'testing/uji_fungsional_esamsat.pdf',
                'keterangan' => 'Pengujian fitur billing perbankan, generate QRIS dinamis, dan rekonsiliasi kas daerah.',
                'tanggal_testing' => now()->subDays(7),
                'status_validasi' => 'lulus',
                'catatan_validasi' => 'Semua modul transaksi telah diuji coba dengan 50 kasus transaksi perbankan dan dinyatakan valid.',
                'tanggal_validasi' => now()->subDays(6),
            ]
        );

        // Uji Keamanan CSIRT Persandian (Sedang dalam status Perbaikan)
        PengujianKeamanan::firstOrCreate(
            ['project_id' => $project3->id, 'user_id' => $adminPersandian->id],
            [
                'kategori_pengujian' => 'penetration_testing',
                'file_hasil' => 'security/laporan_pentest_esamsat.pdf',
                'hasil' => 'perbaikan',
                'tingkat_risiko' => 'sedang',
                'nomor_surat' => '480/CSIRT-RIAU/AUDIT/2026/041',
                'keterangan' => 'Pengujian penetrasi kerentanan web application pada endpoint pengecekan tagihan NIK & No Polisi.',
                'catatan_kerentanan' => 'Ditemukan indikasi Time-based Blind SQL Injection pada parameter filter NIK, serta header HTTP Content-Security-Policy belum diaktifkan.',
                'rekomendasi' => 'Segera gunakan PDO parameter binding dan aktifkan Rate Limiting maksimal 30 request/menit untuk mencegah brute-force NIK.',
                'tanggal_uji' => now()->subDays(3),
            ]
        );

        // -------------------------------------------------------------------------
        // PROYEK 4: PPDB Online Riau -> Status: deployment (Progres 90%)
        // Muncul di Antrean Deployment Infrastruktur
        // -------------------------------------------------------------------------
        $project4 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan4->id],
            [
                'aplikasi_id' => $appPpdb->id,
                'nama_project' => 'Pembangunan Sistem PPDB Online SMA/SMK Negeri se-Riau',
                'tanggal_mulai' => now()->subDays(35)->toDateString(),
                'tanggal_selesai' => now()->addDays(10)->toDateString(),
                'progres_total' => 90,
                'status' => 'deployment',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project4->id, 'user_id' => $devBambang->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project4->id, 'user_id' => $devRian->id], ['peran' => 'anggota']);
        TimProject::firstOrCreate(['project_id' => $project4->id, 'user_id' => $devDoni->id], ['peran' => 'anggota']);

        // Uji Fungsional (Lulus)
        TestingAplikasi::firstOrCreate(
            ['project_id' => $project4->id, 'penguji_id' => $adminAptika->id],
            [
                'file_hasil' => 'testing/uji_fungsional_ppdb.pdf',
                'keterangan' => 'Uji akurasi penentuan koordinat jarak rumah ke sekolah jalur zonasi.',
                'tanggal_testing' => now()->subDays(15),
                'status_validasi' => 'lulus',
                'catatan_validasi' => 'Algoritma Haversine formula teruji akurat dengan toleransi deviasi 0.01%.',
                'tanggal_validasi' => now()->subDays(14),
            ]
        );

        // Uji Keamanan CSIRT (Lulus & Bersertifikat Laik Operasi)
        PengujianKeamanan::firstOrCreate(
            ['project_id' => $project4->id, 'user_id' => $adminPersandian->id],
            [
                'kategori_pengujian' => 'penetration_testing',
                'file_hasil' => 'security/sertifikat_laik_ppdb.pdf',
                'hasil' => 'lulus',
                'tingkat_risiko' => 'aman',
                'nomor_surat' => '480/CSIRT-RIAU/X/2026/088',
                'keterangan' => 'Audit keamanan komprehensif OWASP Web Top 10 dan stress-test anti DDoS.',
                'catatan_kerentanan' => 'Tidak ditemukan kerentanan kritis atau tinggi. Semua parameter input telah tersanitasi.',
                'rekomendasi' => 'Sistem dinyatakan LAIK OPERASI untuk proses pendaftaran siswa baru skala provinsi.',
                'tanggal_uji' => now()->subDays(5),
            ]
        );

        // Hosting Aplikasi: Status 'persiapan' (Antrean Deployment Infrastruktur)
        HostingAplikasi::firstOrCreate(
            ['project_id' => $project4->id],
            [
                'user_id' => $adminInfrastruktur->id,
                'nama_domain' => 'ppdb.riau.go.id',
                'url_aplikasi' => 'https://ppdb.riau.go.id',
                'ip_server' => '103.144.22.50',
                'nama_server' => 'SRV-APP-PROD-03',
                'jenis_hosting' => 'vps',
                'sistem_operasi' => 'Ubuntu 24.04 LTS Server 64-bit',
                'cpu_core' => 8,
                'ram_gb' => 16,
                'storage_gb' => 200,
                'web_server' => 'NGINX Reverse Proxy + PHP 8.3-FPM',
                'database_server' => 'MySQL 8.0 Enterprise Cluster',
                'ssl_aktif' => true,
                'ssl_kadaluarsa' => now()->addYear()->toDateString(),
                'status_hosting' => 'persiapan',
                'spesifikasi_hosting' => 'Cluster VM High-Availability untuk menampung 50.000 pendaftar serentak.',
                'keterangan' => 'Sedang dalam proses konfigurasi Load Balancer & firewall sebelum peluncuran resmi.',
            ]
        );

        // -------------------------------------------------------------------------
        // PROYEK 5: SI-Gawat Darurat 119 -> Status: live (Progres 100%)
        // Muncul di Inventaris Hosting & Server Aktif Infrastruktur
        // -------------------------------------------------------------------------
        $project5 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan5->id],
            [
                'aplikasi_id' => $appSigawat->id,
                'nama_project' => 'Pengembangan Sistem SI-Gawat Darurat 119 Riau Tanggap',
                'tanggal_mulai' => now()->subDays(55)->toDateString(),
                'tanggal_selesai' => now()->subDays(5)->toDateString(),
                'progres_total' => 100,
                'status' => 'live',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project5->id, 'user_id' => $devAhmad->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project5->id, 'user_id' => $devRian->id], ['peran' => 'anggota']);

        // Hosting Aplikasi: Status 'aktif'
        $hostingSigawat = HostingAplikasi::firstOrCreate(
            ['project_id' => $project5->id],
            [
                'user_id' => $adminInfrastruktur->id,
                'nama_domain' => 'sigawat.riau.go.id',
                'url_aplikasi' => 'https://sigawat.riau.go.id',
                'ip_server' => '103.144.22.45',
                'nama_server' => 'SRV-WEB-PROD-01',
                'jenis_hosting' => 'vps',
                'sistem_operasi' => 'Ubuntu 24.04 LTS Server',
                'cpu_core' => 4,
                'ram_gb' => 8,
                'storage_gb' => 100,
                'web_server' => 'NGINX 1.24 + Node.js Socket.io',
                'database_server' => 'PostgreSQL 16 + PostGIS',
                'ssl_aktif' => true,
                'ssl_kadaluarsa' => now()->addMonths(8)->toDateString(),
                'tanggal_live' => now()->subDays(5)->toDateString(),
                'status_hosting' => 'aktif',
                'spesifikasi_hosting' => 'Server Realtime WebSocket Dispatcher Ambulans 24 Jam.',
                'keterangan' => 'Beroperasi normal dengan pemantauan uptime TIK 99.9%.',
            ]
        );

        // Riwayat Perubahan Hosting (Peningkatan RAM)
        RiwayatHosting::firstOrCreate(
            ['hosting_aplikasi_id' => $hostingSigawat->id],
            [
                'user_id' => $adminInfrastruktur->id,
                'data_sebelum' => [
                    'cpu_core' => 2,
                    'ram_gb' => 4,
                    'storage_gb' => 50,
                ],
                'alasan_perubahan' => 'Peningkatan kapasitas RAM dari 4 GB ke 8 GB untuk mendukung lonjakan koneksi WebSocket realtime.',
            ]
        );

        // -------------------------------------------------------------------------
        // PROYEK 6: e-Jalan Riau GIS -> Status: delayed (Progres 40%)
        // Memiliki Log Penundaan Proyek
        // -------------------------------------------------------------------------
        $project6 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan6->id],
            [
                'aplikasi_id' => $appEjalan->id,
                'nama_project' => 'Pembangunan Sistem Informasi Geografis e-Jalan Riau',
                'tanggal_mulai' => now()->subDays(25)->toDateString(),
                'tanggal_selesai' => now()->addMonths(2)->toDateString(),
                'progres_total' => 40,
                'status' => 'delayed',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project6->id, 'user_id' => $devBambang->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project6->id, 'user_id' => $devSiti->id], ['peran' => 'anggota']);

        // Log Penundaan Proyek
        Penundaan::firstOrCreate(
            ['project_id' => $project6->id],
            [
                'diajukan_oleh' => $devBambang->id,
                'alasan' => 'Keterlambatan penyediaan API peta citra satelit resolusi tinggi dari penyedia geospasial eksternal. Sprint integrasi modul pemetaan dihentikan sementara selama 14 hari kalender.',
                'file_surat' => 'penundaan/surat_permohonan_delay_ejalan.pdf',
                'status' => 'diajukan',
            ]
        );

        // -------------------------------------------------------------------------
        // PROYEK 7: AMDAL Online DLHK -> Status: functional_testing (Progres 60%)
        // Muncul di Menu Uji Fungsional APTIKA
        // -------------------------------------------------------------------------
        $project7 = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuan7->id],
            [
                'aplikasi_id' => $appAmdal->id,
                'nama_project' => 'Pengembangan Sistem Pengawasan AMDAL Online Riau Hijau',
                'tanggal_mulai' => now()->subDays(18)->toDateString(),
                'tanggal_selesai' => now()->addMonth()->toDateString(),
                'progres_total' => 60,
                'status' => 'functional_testing',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $project7->id, 'user_id' => $devAhmad->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $project7->id, 'user_id' => $devDoni->id], ['peran' => 'anggota']);

        // Uji Fungsional APTIKA (Status Perbaikan - ada temuan yang harus diperbaiki programmer)
        TestingAplikasi::firstOrCreate(
            ['project_id' => $project7->id, 'penguji_id' => $adminAptika->id],
            [
                'file_hasil' => 'testing/audit_fungsional_amdal.pdf',
                'keterangan' => 'Audit form unggah berkas analisis lingkungan hidup dan verifikasi tanda tangan digital kepala dinas.',
                'tanggal_testing' => now()->subDays(2),
                'status_validasi' => 'perbaikan',
                'catatan_validasi' => 'Ditemukan galat saat mengunggah dokumen AMDAL berukuran lebih dari 10 MB, form upload ter-reset otomatis.',
                'tanggal_validasi' => now()->subDays(1),
            ]
        );

        // =========================================================================
        // 8. Rapat Koordinasi & Notulensi Proyek (rapat_project & peserta_rapat)
        // =========================================================================

        // Rapat 1: Kick-off Sprint e-Puskesmas (Selesai, lengkap notulensi)
        $rapat1 = RapatProject::firstOrCreate(
            ['project_id' => $project1->id, 'nama_rapat' => 'Rapat Kick-off Sprint & Penyelarasan Kebutuhan e-Puskesmas'],
            [
                'dibuat_oleh' => $adminAptika->id,
                'waktu_rapat' => now()->subDays(8)->setTime(9, 30),
                'tempat_rapat' => 'Ruang Rapat Bidang APTIKA Lt. 2 Diskominfotik Riau',
                'deskripsi' => 'Pembahasan ruang lingkup sprint 1 modul pendaftaran pasien dan rekam medis rawat jalan.',
                'notulensi' => "HASIL NOTULENSI RAPAT:\n1. Tim pengembang internal (Ahmad & Bambang) menyepakati tenggat waktu SRS selama 5 hari kerja.\n2. Dinas Kesehatan akan menunjuk 3 puskesmas percontohan (Puskesmas Harapan Raya, Rumbai, dan Simpang Tiga) untuk uji coba tahap pertama.\n3. Integrasi SatuSehat menggunakan Sandbox Kemenkes.",
                'status' => 'selesai',
            ]
        );

        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat1->id, 'user_id' => $adminAptika->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat1->id, 'user_id' => $devAhmad->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat1->id, 'user_id' => $devBambang->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat1->id, 'user_id' => $opdDinkes->id], ['status_kehadiran' => 'hadir']);

        // Rapat 2: Evaluasi Temuan Uji Fungsional AMDAL Online (Dijadwalkan untuk besok)
        $rapat2 = RapatProject::firstOrCreate(
            ['project_id' => $project7->id, 'nama_rapat' => 'Evaluasi Hasil Uji Fungsional & Perbaikan Form Unggah AMDAL'],
            [
                'dibuat_oleh' => $adminAptika->id,
                'waktu_rapat' => now()->addDay()->setTime(10, 0),
                'tempat_rapat' => 'Ruang Rapat Command Center Diskominfotik Riau & Hybrid Zoom',
                'deskripsi' => 'Membahas solusi penanganan berkas PDF berukuran besar dan optimasi chunked-upload pada modul AMDAL.',
                'status' => 'dijadwalkan',
            ]
        );

        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat2->id, 'user_id' => $adminAptika->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat2->id, 'user_id' => $devAhmad->id], ['status_kehadiran' => 'menunggu']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat2->id, 'user_id' => $opdDlhk->id], ['status_kehadiran' => 'menunggu']);

        // Rapat 3: Koordinasi Deployment PPDB Online dengan Infrastruktur (Selesai)
        $rapat3 = RapatProject::firstOrCreate(
            ['project_id' => $project4->id, 'nama_rapat' => 'Koordinasi Kesiapan Server & Konfigurasi Bandwidth PPDB Online'],
            [
                'dibuat_oleh' => $adminAptika->id,
                'waktu_rapat' => now()->subDays(2)->setTime(14, 0),
                'tempat_rapat' => 'Ruang Server NOC Bidang Infrastruktur Diskominfotik',
                'deskripsi' => 'Penyelarasan arsitektur server, alokasi IP publik, dan konfigurasi Cloudflare CDN.',
                'notulensi' => "HASIL KESEPAKATAN:\n1. Bidang Infrastruktur menyiapkan IP Publik 103.144.22.50 pada server SRV-APP-PROD-03.\n2. Tim CSIRT telah menerbitkan rekomendasi laik operasi nomor 480/CSIRT-RIAU/X/2026/088.\n3. Rilis live dijadwalkan setelah gladi resik tanggal 15 Oktober 2026.",
                'status' => 'selesai',
            ]
        );

        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat3->id, 'user_id' => $adminAptika->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat3->id, 'user_id' => $adminInfrastruktur->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat3->id, 'user_id' => $adminPersandian->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat3->id, 'user_id' => $devBambang->id], ['status_kehadiran' => 'hadir']);
        PesertaRapat::firstOrCreate(['rapat_project_id' => $rapat3->id, 'user_id' => $opdDisdik->id], ['status_kehadiran' => 'hadir']);

        // =========================================================================
        // 8. Usulan & Proyek Tambahan (Sistem test test & aaaaa)
        // =========================================================================
        $appTest = Aplikasi::firstOrCreate(
            ['nama_aplikasi' => 'Sistem test test'],
            [
                'instansi_id' => $dinkes->id,
                'deskripsi' => 'Sistem test adalah test',
                'status' => 'aktif',
            ]
        );

        $pengajuanTest1 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-20261008-0001'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => $appTest->id,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'Sistem test test',
                'deskripsi_kebutuhan' => 'Sistem test adalah test',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081200000005',
                'status' => 'approved',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'Usulan sistem disetujui.',
                'tanggal_penilaian' => now()->subHours(2),
            ]
        );

        $pengajuanTest2 = Pengajuan::firstOrCreate(
            ['kode_pengajuan' => 'REG-20261008-0002'],
            [
                'instansi_id' => $dinkes->id,
                'aplikasi_id' => null,
                'layanan_id' => $layananBaru->id,
                'jenis_pengajuan' => 'new_app',
                'nama_aplikasi' => 'aaaaa',
                'deskripsi_kebutuhan' => 'aaaaasdaddasdadsadadsadsas',
                'nama_pemohon' => 'drg. Rahmat Hidayat',
                'no_hp_pemohon' => '081200000005',
                'status' => 'rejected',
                'dinilai_oleh' => $adminAptika->id,
                'catatan_penilaian' => 'kurang',
                'tanggal_penilaian' => now()->subHour(),
            ]
        );

        $projectTest = Project::firstOrCreate(
            ['pengajuan_id' => $pengajuanTest1->id],
            [
                'aplikasi_id' => $appTest->id,
                'nama_project' => 'Sistem test test',
                'tanggal_mulai' => now()->toDateString(),
                'tanggal_selesai' => now()->addMonths(3)->toDateString(),
                'progres_total' => 50,
                'status' => 'in_development',
            ]
        );

        TimProject::firstOrCreate(['project_id' => $projectTest->id, 'user_id' => $devAhmad->id], ['peran' => 'ketua_tim']);
        TimProject::firstOrCreate(['project_id' => $projectTest->id, 'user_id' => $devDoni->id], ['peran' => 'anggota']);
        TimProject::firstOrCreate(['project_id' => $projectTest->id, 'user_id' => $devDimz->id], ['peran' => 'anggota']);

        TahapanProject::firstOrCreate(
            ['project_id' => $projectTest->id, 'urutan' => 1],
            ['nama_tahapan' => 'Pengembangan perancagan sistem', 'persentase' => 50, 'status' => 'dalam_pengerjaan']
        );
        TahapanProject::firstOrCreate(
            ['project_id' => $projectTest->id, 'urutan' => 2],
            ['nama_tahapan' => 'Implementasi Modul & Uji Coba', 'persentase' => 0, 'status' => 'belum_mulai']
        );

        TestingAplikasi::firstOrCreate(
            ['project_id' => $projectTest->id, 'penguji_id' => $adminAptika->id],
            [
                'file_hasil' => 'testing/project_8/uji_fungsional_1791428435.pdf',
                'keterangan' => 'Pengujian blackbox sudah selesai, fitur aplikasi berjalan baik',
                'tanggal_testing' => now()->toDateString(),
                'status_validasi' => 'lulus',
                'catatan_validasi' => 'Fitur lolos uji fungsional.',
                'tanggal_validasi' => now(),
            ]
        );

        HostingAplikasi::firstOrCreate(
            ['project_id' => $projectTest->id],
            [
                'user_id' => $adminInfrastruktur->id,
                'nama_domain' => 'sistemtesttest.riau.go.id',
                'url_aplikasi' => 'https://sistemtesttest.riau.go.id',
                'ip_server' => '103.150.12.45',
                'nama_server' => 'SRV-TIK-VM01',
                'status_hosting' => 'aktif',
                'sistem_operasi' => 'Ubuntu 22.04 LTS',
                'cpu_core' => 4,
                'ram_gb' => 8,
                'storage_gb' => 100,
                'web_server' => 'Nginx 1.24',
                'database_server' => 'MySQL 8.0',
                'ssl_aktif' => true,
                'keterangan' => 'Aplikasi berhasil dideploy ke server produksi TIK Riau.',
            ]
        );

        // =========================================================================
        // 9. Sinkronisasi Data Pipeline Workflow & Dokumen Seluruh Pengajuan
        // =========================================================================
        LayananWorkflowSeeder::syncAllPengajuanWorkflows();
    }
}
