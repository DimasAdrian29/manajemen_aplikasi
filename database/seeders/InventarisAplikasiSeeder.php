<?php

namespace Database\Seeders;

use App\Models\Instansi;
use App\Models\InventarisAplikasi;
use Illuminate\Database\Seeder;

/**
 * Data awal Daftar Aplikasi Pemerintah Provinsi Riau
 * (sumber: Airtable Diskominfotik Provinsi Riau, per 30 September 2026).
 */
class InventarisAplikasiSeeder extends Seeder
{
    private const DISKOMINFO = 'Dinas Komunikasi, Informatika dan Statistik';

    private const DPPPA = 'Dinas Pemberdayaan Perempuan, Perlindungan Anak dan Pengendalian Penduduk';

    private const DISPERINDAG = 'Dinas Perindustrian, Perdagangan, Koperasi, Usaha Kecil dan Menengah';

    private const PUPR = 'Dinas Pekerjaan Umum, Penataan Ruang, Perumahan, Kawasan Permukiman dan Pertanahan';

    private const UJI_INFRA = "Tri Julian Indra, S.Kom\nRiady Hanafi, S.T";

    private const UJI_KEAMANAN = "Tiara Mulia Putri, S.Kom\nDebie Nahelda, S.E";

    /** @var array<string, int> */
    private array $instansiCache = [];

    public function run(): void
    {
        $penggantiMap = [];

        foreach ($this->data() as $row) {
            $pengganti = $row['pengganti'] ?? null;
            unset($row['pengganti']);

            $row['instansi_id'] = $this->instansiId($row['opd']);
            unset($row['opd']);

            if (isset($row['dok'])) {
                [$row['ada_self_assessment'], $row['ada_surat_keputusan'], $row['ada_manual_book'], $row['ada_bast']] = $row['dok'];
                unset($row['dok']);
            }

            $aplikasi = InventarisAplikasi::updateOrCreate(
                isset($row['nomor_registrasi'])
                    ? ['nomor_registrasi' => $row['nomor_registrasi']]
                    : ['nama_aplikasi' => $row['nama_aplikasi'], 'instansi_id' => $row['instansi_id']],
                $row
            );

            if ($pengganti) {
                $penggantiMap[$aplikasi->id] = $pengganti;
            }
        }

        foreach ($penggantiMap as $aplikasiId => $nomorPengganti) {
            InventarisAplikasi::whereKey($aplikasiId)->update([
                'aplikasi_pengganti_id' => InventarisAplikasi::where('nomor_registrasi', $nomorPengganti)->value('id'),
            ]);
        }
    }

    /**
     * Cocokkan nama OPD dengan master instansi (termasuk varian berakhiran "Provinsi Riau"), buat jika belum ada.
     */
    private function instansiId(string $nama): int
    {
        return $this->instansiCache[$nama] ??= (
            Instansi::where('nama_instansi', $nama)->orWhere('nama_instansi', "{$nama} Provinsi Riau")->value('id')
            ?? Instansi::create(['nama_instansi' => $nama])->id
        );
    }

    /**
     * Baris aplikasi terdaftar (bernomor) memakai default berikut kecuali ditimpa.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function terdaftar(array $data): array
    {
        return array_merge([
            'status' => 'aktif',
            'is_aplikasi_pemprov' => true,
            'is_website_resmi' => false,
            'platform' => 'web',
            'konektivitas' => 'online',
        ], $data);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function data(): array
    {
        return [
            // ===================== STATUS AKTIF =====================
            $this->terdaftar([
                'nomor_registrasi' => 6, 'nama_aplikasi' => 'Assessment', 'opd' => 'Badan Kepegawaian Daerah',
                'pic_aplikasi' => 'Tenaga IT', 'jenis_layanan' => 'pemerintahan', 'url' => 'assessment.riau.go.id', 'penempatan_server' => 'Diskominfotik',
                'programmer' => 'Miskal, S.Kom', 'pengelola_adm' => 'Harisman, S.Kom', 'tim_pengujian_aplikasi' => "Miskal, S.Kom\nAnggi Novendra, S.T",
                'tim_pengujian_infrastruktur' => self::UJI_INFRA, 'tim_pengujian_keamanan' => self::UJI_KEAMANAN,
                'status_pengembangan' => 'sudah_selesai', 'dok' => [false, true, true, false],
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 1, 'nama_aplikasi' => 'Sistem Informasi Kinerja Pegawai (e-Sikap)', 'opd' => 'Badan Kepegawaian Daerah',
                'pic_aplikasi' => 'Bidang Pendayagunaan', 'fungsi_menu' => 'Integrasi kinerja dengan pembayaran TPP',
                'jenis_layanan' => 'pemerintahan', 'url' => 'skp.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 8, 'nama_aplikasi' => 'Sistem Pelaporan Situasi Daerah (LAPSITDA)', 'opd' => 'Badan Kesatuan Bangsa dan Politik',
                'fungsi_menu' => 'Sistem yang diupayakan penginputan Laporan Situasi kondisi daerah di Provinsi Riau',
                'jenis_layanan' => 'pemerintahan', 'url' => 'lapsitda.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 7, 'nama_aplikasi' => 'Website Resmi Badan Kesbangpol Provinsi Riau', 'opd' => 'Badan Kesatuan Bangsa dan Politik',
                'is_website_resmi' => true, 'pic_aplikasi' => 'Tenaga IT', 'fungsi_menu' => 'Sistem informasi Badan Kesatuan Bangsa dan Politik Provinsi Riau',
                'jenis_layanan' => 'publik', 'url' => 'kesbangpol.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 443, 'nama_aplikasi' => 'Website Resmi BRIDA Provinsi Riau', 'opd' => 'Badan Riset dan Inovasi Daerah',
                'is_website_resmi' => true, 'jenis_layanan' => 'publik', 'url' => 'brida.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 438, 'nama_aplikasi' => 'Sistem Online Bantuan Kesejahteraan Rakyat (Sobat Kesra)', 'opd' => 'Biro Kesejahteraan Rakyat',
                'jenis_layanan' => 'publik', 'url' => 'sobatkesra.riau.go.id', 'penempatan_server' => 'Diskominfotik',
                'programmer' => "Fakhrurazi, S.T\nJemi Faisal, S.Kom", 'pengelola_adm' => 'Gilang Ramadhan',
                'tim_pengujian_aplikasi' => "Martha Sari, S.Kom\nHendra K. R. Lase, S.T",
                'tim_pengujian_infrastruktur' => self::UJI_INFRA, 'tim_pengujian_keamanan' => self::UJI_KEAMANAN,
                'status_pengembangan' => 'sudah_selesai', 'dok' => [true, true, true, false],
                'catatan' => 'Aplikasi sudah selesai, tetapi masih ada pengembangan modul yang sedang berjalan.',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 75, 'nama_aplikasi' => 'Website Resmi Biro Kesra', 'opd' => 'Biro Kesejahteraan Rakyat',
                'is_website_resmi' => true, 'pic_aplikasi' => 'Tenaga IT', 'jenis_layanan' => 'publik', 'url' => 'birokesra.riau.go.id',
                'tanggal_pemberlakuan' => '2021-02-01', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 78, 'nama_aplikasi' => 'Website Resmi Biro Organisasi', 'opd' => 'Biro Organisasi',
                'is_website_resmi' => true, 'pic_aplikasi' => 'Jabatan', 'jenis_layanan' => 'publik', 'url' => 'biroorganisasi.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 437, 'nama_aplikasi' => 'Laporan Keterangan dan Pertanggungjawaban (LKPJ)', 'opd' => 'Biro Pemerintahan dan Otonomi Daerah',
                'pic_aplikasi' => 'Subkoordinator Evaluasi dan Penyelenggaraan Pemerintahan - Biro Pemotda',
                'fungsi_menu' => "1. Penginputan data LKPJ oleh pengguna\n2. Perekapan data LKPJ oleh admin",
                'jenis_layanan' => 'pemerintahan', 'url' => 'lkpj.riau.go.id', 'tanggal_pemberlakuan' => '2024-01-01', 'penempatan_server' => 'Diskominfotik',
                'programmer' => "Miskal, S.Kom\nRiawan Andika Putra, S.Kom\nAnggi Novendra, S.T\nPandu Eka Saputra, S.Kom",
                'pengelola_adm' => 'Siti Amelia Apriyanti, S.S.T',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 377, 'nama_aplikasi' => 'WebGIS Batas Daerah Provinsi Riau', 'opd' => 'Biro Pemerintahan dan Otonomi Daerah',
                'pic_aplikasi' => 'Tenaga IT', 'deskripsi' => 'Untuk mengetahui administrasi batas daerah kabupaten/kota dan provinsi serta pilar batas di Provinsi Riau',
                'jenis_layanan' => 'pemerintahan', 'url' => 'biropemotda.riau.go.id/petabatas', 'penempatan_server' => 'Diskominfotik',
                'catatan' => 'Tanggal pemberlakuan pada data sumber tertulis 14 Jul 1905, perlu diverifikasi.',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 80, 'nama_aplikasi' => 'Website Resmi Biro Pemotda Provinsi Riau', 'opd' => 'Biro Pemerintahan dan Otonomi Daerah',
                'is_website_resmi' => true, 'jenis_layanan' => 'publik', 'url' => 'biropemotda.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 82, 'nama_aplikasi' => 'Sistem Unit Pelayanan Pengadaan (SULaP)', 'opd' => 'Biro Pengadaan Barang dan Jasa',
                'pic_aplikasi' => 'Pranata Komputer Ahli Muda', 'fungsi_menu' => 'Sistem Administrasi Layanan PBJ',
                'jenis_layanan' => 'pemerintahan', 'url' => 'sulap.riau.go.id', 'tanggal_pemberlakuan' => '2024-07-10', 'penempatan_server' => 'Biro PBJ',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 85, 'nama_aplikasi' => 'Website Resmi Biro Perekonomian Provinsi Riau', 'opd' => 'Biro Perekonomian',
                'is_website_resmi' => true, 'jenis_layanan' => 'publik', 'url' => 'biroekonomi.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 439, 'nama_aplikasi' => 'Sistem Informasi Pendataan Rumah Tangga Tidak Mampu Belum Berlistrik Provinsi Riau (PEDULI Riau)', 'opd' => 'Dinas Energi dan Sumber Daya Mineral',
                'pic_aplikasi' => 'Kepala Bidang Ketenagalistrikan',
                'deskripsi' => 'Untuk peningkatan proses pelayanan pengumpulan dan pengolahan data keluarga tidak mampu untuk penyaluran bantuan pasang baru listrik melalui Sistem Informasi Peduli Riau (pendataan rumah tangga tidak mampu belum berlistrik Provinsi Riau)',
                'jenis_layanan' => 'publik', 'url' => 'peduli.riau.go.id', 'tanggal_pemberlakuan' => '2025-01-15', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 166, 'nama_aplikasi' => 'Aplikasi Helpdesk', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Aplikasi Layanan Pengaduan Terkait Hal Teknis Pemerintah Provinsi Riau', 'jenis_layanan' => 'pemerintahan', 'url' => 'helpdesk.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 167, 'nama_aplikasi' => 'Aplikasi Mikroji', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Aplikasi Monitoring Jaringan Dinas Komunikasi, Informatika dan Statistik Provinsi Riau', 'jenis_layanan' => 'pemerintahan', 'url' => 'monitoring.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 172, 'nama_aplikasi' => 'Buku Tamu', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Buku Tamu Pemerintah Provinsi Riau', 'jenis_layanan' => 'publik', 'url' => 'bukutamu.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 164, 'nama_aplikasi' => 'e-Absen', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Pengelolaan Kehadiran Pegawai Pemerintah Provinsi Riau', 'jenis_layanan' => 'pemerintahan', 'url' => 'e-absen.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 160, 'nama_aplikasi' => 'e-Office', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Sistem Administrasi Perkantoran Pemerintah Provinsi Riau', 'jenis_layanan' => 'pemerintahan', 'url' => 'e-office.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 173, 'nama_aplikasi' => 'Informasi Data Peta Riau', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Informasi data peta Riau dengan berbagai objek', 'jenis_layanan' => 'publik', 'url' => 'peta.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 175, 'nama_aplikasi' => 'Jaga Riau', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Portal transparansi anggaran Pemerintah Provinsi Riau', 'jenis_layanan' => 'publik', 'url' => 'jaga.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 162, 'nama_aplikasi' => 'Media Center', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Portal Berita Pemerintah Provinsi Riau', 'jenis_layanan' => 'publik', 'url' => 'mediacenter.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 176, 'nama_aplikasi' => 'Portal Informasi Geospasial Provinsi Riau', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Menampilkan informasi Geospasial Provinsi Riau kepada masyarakat', 'jenis_layanan' => 'publik', 'url' => 'geoportal.riau.go.id',
                'programmer' => "Hendra K. R. Lase, S.T\nDedi Damhudi, S.Kom", 'pengelola_adm' => 'Muhammad Desri Murdianto, S.Kom',
                'tim_pengujian_aplikasi' => 'Aulia Rahman, S.S.T', 'status_pengembangan' => 'on_progress', 'dok' => [false, true, false, false],
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 163, 'nama_aplikasi' => 'Rumah Data', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Penyajian atau Penyebarluasan Data dan Informasi Spesifik dari Data Sektoral (statistik dan spasial) Pemerintah Provinsi Riau',
                'jenis_layanan' => 'publik', 'url' => 'rumahdata.riau.go.id',
                'programmer' => "Mhd Salahuddin Al Arif, S.T.\nAulia Rahman, S.S.T", 'pengelola_adm' => 'Wakhid Primadi, S.Kom',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 179, 'nama_aplikasi' => 'Single Sign On (SSO)', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Akses aplikasi milik Pemerintah Provinsi Riau dengan satu username dan password', 'jenis_layanan' => 'pemerintahan', 'url' => 'sso.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 181, 'nama_aplikasi' => 'Sistem Informasi Pengelolaan Administrasi (SiPena)', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Pengelolaan dokumen-dokumen administrasi yang berkaitan dengan kegiatan (aplikasi, tata kelola, integrasi) di Bidang Aptika Diskominfotik Riau',
                'jenis_layanan' => 'pemerintahan', 'url' => 'sipena.diskominfotik.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 178, 'nama_aplikasi' => 'Tunjuk Integritas', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Menampilkan informasi Integritas Expo 2016', 'jenis_layanan' => 'publik', 'url' => 'integritas.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 165, 'nama_aplikasi' => 'Website Informasi Covid-19', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Website Informasi Covid-19 Provinsi Riau', 'jenis_layanan' => 'publik', 'url' => 'corona.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 168, 'nama_aplikasi' => 'Website Media Center Karhutla', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Website Informasi Kebakaran Hutan dan Lahan Provinsi Riau', 'jenis_layanan' => 'publik', 'url' => 'karhutla.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 169, 'nama_aplikasi' => 'Website Pejabat Pengelola Informasi Dokumentasi (PPID) Utama', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Pengelola Informasi dan Dokumentasi', 'jenis_layanan' => 'publik', 'url' => 'ppid.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 161, 'nama_aplikasi' => 'Website Resmi Diskominfotik Provinsi Riau', 'opd' => self::DISKOMINFO,
                'is_website_resmi' => true, 'fungsi_menu' => 'Website Dinas Komunikasi, Informatika dan Statistik Provinsi Riau',
                'jenis_layanan' => 'publik', 'url' => 'diskominfotik.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 159, 'nama_aplikasi' => 'Website Resmi Pemerintah Provinsi Riau', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Website Pemerintah Provinsi Riau', 'jenis_layanan' => 'publik', 'url' => 'riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 171, 'nama_aplikasi' => 'Website Streaming', 'opd' => self::DISKOMINFO,
                'fungsi_menu' => 'Website Streaming', 'jenis_layanan' => 'publik', 'url' => 'streaming.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 440, 'nama_aplikasi' => 'SIBERTAHAN', 'opd' => 'Dinas Lingkungan Hidup dan Kehutanan',
                'is_aplikasi_pemprov' => false, 'platform' => null, 'konektivitas' => null,
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 441, 'nama_aplikasi' => 'Sistem Informasi Monitoring Program Swasembada Pangan Riau (SIMPROSA)', 'opd' => 'Dinas Pangan, Tanaman Pangan dan Hortikultura',
                'jenis_layanan' => 'pemerintahan', 'url' => 'simprosa.riau.go.id', 'penempatan_server' => 'Diskominfotik',
                'programmer' => 'Aulia Rahman, S.S.T', 'pengelola_adm' => 'Eka Rahmadianingsih, S.S.T',
                'tim_pengujian_aplikasi' => "Pandu Eka Saputra, S.Kom\nYesi Hairian Wenda, S.Kom., M.Kom",
                'tim_pengujian_infrastruktur' => self::UJI_INFRA, 'tim_pengujian_keamanan' => self::UJI_KEAMANAN,
                'status_pengembangan' => 'menunggu_konfirmasi', 'dok' => [false, true, false, false],
                'catatan' => 'Aplikasi sudah selesai, terakhir bulan September untuk dilakukan pengujian oleh Tim DPTPH Provinsi Riau, tetapi tidak ada konfirmasi terkait kelanjutannya hingga saat ini.',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 226, 'nama_aplikasi' => 'Perencanaan dan Penganggaran Responsif Gender (PPRG)', 'opd' => self::DPPPA,
                'pic_aplikasi' => 'Tenaga IT',
                'deskripsi' => 'PPRG merupakan instrumen untuk mengatasi kesenjangan akses, partisipasi, kontrol, dan manfaat pembangunan bagi perempuan dan laki-laki. PPRG merupakan bentuk implementasi dari penganggaran berbasis kinerja yang mengintegrasikan aspek keadilan sebagai indikator kinerja. PPRG dilakukan melalui analisis gender dan penyusunan Gender Budget Statement (GBS). PPRG merupakan salah satu bentuk penerapan strategi pengarusutamaan gender (PUG) dalam pembangunan.',
                'fungsi_menu' => "Dalam rangka percepatan koordinasi antar OPD terkait penyusunan PPRG seperti pendampingan penyusunan secara online, antara lain:\n- Penyusunan Gender Analysis Pathway (GAP)\n- Penyusunan Gender Budget Statement (GBS)\n- Term of Reference (TOR) / Kerangka Acuan Kerja (KAK)",
                'jenis_layanan' => 'pemerintahan', 'url' => 'pprg.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 263, 'nama_aplikasi' => 'Sistem Informasi Pengentasan Anak Tidak Sekolah (SiPantas)', 'opd' => 'Dinas Pendidikan',
                'fungsi_menu' => 'Menampilkan statistik anak yang tidak sekolah', 'jenis_layanan' => 'publik', 'url' => 'sipantas.riau.go.id',
                'programmer' => "Mhd Salahuddin Al Arif, S.T.\nAulia Rahman, S.S.T", 'pengelola_adm' => 'Wakhid Primadi, S.Kom',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 264, 'nama_aplikasi' => 'Sistem Penerimaan Murid Baru (SPMB) Online', 'opd' => 'Dinas Pendidikan',
                'deskripsi' => 'Sistem Penerimaan Murid Baru (SPMB) Provinsi Riau adalah aplikasi yang digunakan sebagai wadah penyaringan penerimaan murid baru untuk tingkat SMA/SMK dan sederajat di Provinsi Riau.',
                'fungsi_menu' => 'Mengakomodir proses PPDB SMA dan SMK di Provinsi Riau secara online',
                'jenis_layanan' => 'publik', 'url' => 'pmb.riau.go.id', 'penempatan_server' => 'PDN',
                'programmer' => "Mhd Salahuddin Al Arif, S.T.\nAulia Rahman, S.S.T", 'pengelola_adm' => 'Martha Sari, S.Kom',
                'tim_pengujian_aplikasi' => 'Martha Sari, S.Kom', 'tim_pengujian_infrastruktur' => 'Tri Julian Indra, S.Kom', 'tim_pengujian_keamanan' => 'Tiara Mulia Putri, S.Kom',
                'status_pengembangan' => 'on_progress', 'dok' => [false, true, true, false],
                'catatan' => 'Sedang pengembangan tahun 2026.',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 267, 'nama_aplikasi' => 'Website Resmi Dinas Perhubungan Provinsi Riau', 'opd' => 'Dinas Perhubungan',
                'is_website_resmi' => true, 'pic_aplikasi' => 'Penelaah Teknis Kebijakan',
                'deskripsi' => 'Website resmi Dinas Perhubungan Provinsi Riau terkait informasi umum tugas dan fungsi urusan pemerintah di Bidang Perhubungan',
                'fungsi_menu' => "1. Profil Perhubungan\n2. Dokumen pendukung terkait perhubungan\n3. Informasi terupdate tentang perhubungan",
                'jenis_layanan' => 'publik', 'url' => 'dishub.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 272, 'nama_aplikasi' => 'Info Perdagangan', 'opd' => self::DISPERINDAG,
                'pic_aplikasi' => 'Bidang Perdagangan Disperindagkop-UKM Provinsi Riau',
                'deskripsi' => 'Informasi tentang harga bahan pokok', 'fungsi_menu' => 'Menampilkan informasi harga barang kebutuhan pokok',
                'jenis_layanan' => 'publik', 'url' => 'infoperdagangan.riau.go.id', 'tanggal_pemberlakuan' => '2021-01-12', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 435, 'nama_aplikasi' => 'Komunikasi dan Informasi Pasar Pantauan Barang Penting dan Harga Pangan (Kodai Puantipa)', 'opd' => self::DISPERINDAG,
                'jenis_layanan' => 'publik', 'url' => 'kodaipuantipa.riau.go.id', 'tanggal_pemberlakuan' => '2024-01-01', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 271, 'nama_aplikasi' => 'Monitoring Data UMKM (Mata UMKM)', 'opd' => self::DISPERINDAG,
                'pic_aplikasi' => 'Bidang Koperasi Disperindagkop-UKM Provinsi Riau',
                'deskripsi' => 'Untuk Pendataan dan Monitoring UMKM serta Bantuan UMKM', 'fungsi_menu' => 'Monitoring data UMKM',
                'jenis_layanan' => 'publik', 'url' => 'mataumkm.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 268, 'nama_aplikasi' => 'Website Resmi Disperindagkop-UKM Provinsi Riau', 'opd' => self::DISPERINDAG,
                'is_website_resmi' => true, 'pic_aplikasi' => 'Tenaga IT Disperindagkopukm Prov. Riau',
                'deskripsi' => 'Website resmi Disperindagkopukm Prov. Riau', 'fungsi_menu' => 'Web Portal Berita dan Informasi',
                'jenis_layanan' => 'publik', 'url' => 'disperindagkopukm.riau.go.id', 'tanggal_pemberlakuan' => '2022-01-01', 'penempatan_server' => 'Diskominfotik',
                'programmer' => 'Gilang Rizal Anugerah, S.S.T', 'pengelola_adm' => 'Gilang Ramadhan',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 292, 'nama_aplikasi' => 'Monitoring Data Bantuan Sosial (Mata Bansos)', 'opd' => 'Dinas Sosial',
                'fungsi_menu' => 'Monitoring data penyaluran dana bantuan sosial terutama terkait dampak pandemi Covid-19',
                'jenis_layanan' => 'pemerintahan', 'url' => 'matabansos.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 294, 'nama_aplikasi' => 'Puskesos-SLRT', 'opd' => 'Dinas Sosial',
                'deskripsi' => 'Pengelolaan data dan informasi kemiskinan yang terpadu dan efisien sebagai database program perlindungan dan bantuan sosial',
                'jenis_layanan' => 'publik', 'url' => 'puskesos-slrt.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),

            // ===================== STATUS TIDAK AKTIF =====================
            $this->terdaftar([
                'nomor_registrasi' => 88, 'nama_aplikasi' => 'Sistem Informasi Data Potensi ESDM (SIDPES)', 'opd' => 'Biro Perekonomian',
                'status' => 'tidak_aktif', 'url' => 'bap.riau.go.id/sidpes',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 87, 'nama_aplikasi' => 'Sistem Informasi Perekonomian Provinsi Riau (SIMPERKOM)', 'opd' => 'Biro Perekonomian',
                'status' => 'tidak_aktif', 'url' => 'bap.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 86, 'nama_aplikasi' => 'Sistem Penilaian Kinerja BUMD (SIPKA)', 'opd' => 'Biro Perekonomian',
                'status' => 'tidak_aktif', 'url' => 'bap.riau.go.id/sipka',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 391, 'nama_aplikasi' => 'Website Resmi Biro Umum Provinsi Riau', 'opd' => 'Biro Umum',
                'status' => 'tidak_aktif', 'is_website_resmi' => true, 'pic_aplikasi' => 'Staf',
                'deskripsi' => 'Website resmi dinas', 'fungsi_menu' => 'Informasi resmi untuk masyarakat',
                'jenis_layanan' => 'publik', 'url' => 'biroumum.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 170, 'nama_aplikasi' => 'Agenda Pimpinan', 'opd' => self::DISKOMINFO,
                'status' => 'tidak_aktif', 'fungsi_menu' => 'Informasi Agenda Pimpinan', 'jenis_layanan' => 'publik', 'url' => 'agenda.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 432, 'nama_aplikasi' => 'Computer Security Incident Response Team (CSIRT) Provinsi Riau', 'opd' => self::DISKOMINFO,
                'status' => 'tidak_aktif', 'jenis_layanan' => 'publik', 'url' => 'csirt.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 174, 'nama_aplikasi' => 'Kelompok Informasi Masyarakat (KIM)', 'opd' => self::DISKOMINFO,
                'status' => 'tidak_aktif', 'jenis_layanan' => 'publik', 'url' => 'kim.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 180, 'nama_aplikasi' => 'Sistem Pendataan Detail Aplikasi (SPADA)', 'opd' => self::DISKOMINFO,
                'status' => 'tidak_aktif', 'fungsi_menu' => 'Penyimpanan data detail aplikasi yang dikembangkan dan/atau digunakan oleh OPD Provinsi Riau',
                'jenis_layanan' => 'pemerintahan', 'url' => 'spada.diskominfotik.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 217, 'nama_aplikasi' => 'Sistem Informasi Jalan dan Jembatan Riau (Sijari) Mantap', 'opd' => self::PUPR,
                'status' => 'tidak_aktif', 'pic_aplikasi' => 'Tenaga IT', 'platform' => 'mobile',
                'deskripsi' => 'Pelaporan jalan dan jembatan rusak di wilayah Provinsi Riau oleh masyarakat',
                'fungsi_menu' => 'Pelaporan jalan dan jembatan rusak di wilayah Provinsi Riau oleh masyarakat',
                'jenis_layanan' => 'publik', 'url' => 'play.google.com/store/apps/details?id=riau.sijari.mantap', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 270, 'nama_aplikasi' => 'Indonesia Malaysia Thailand Growth Triangle Business Center (IMT-GT BC)', 'opd' => self::DISPERINDAG,
                'status' => 'tidak_aktif', 'deskripsi' => 'Untuk media informasi dan promosi produk berpotensi ekspor',
                'fungsi_menu' => 'Informasi/promosi produk yang berpotensi ekspor', 'jenis_layanan' => 'publik', 'url' => 'imtgt.riau.go.id', 'penempatan_server' => 'Diskominfotik',
                'programmer' => 'Miskal, S.Kom', 'pengelola_adm' => 'Robby Zukri Putra, S.Kom',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 269, 'nama_aplikasi' => 'Sistem Elektronik Perindustrian yang Akuntabel dan Terpadu (SEPAT)', 'opd' => self::DISPERINDAG,
                'status' => 'tidak_aktif', 'pic_aplikasi' => 'Kepala Bidang Sarana, Prasarana, dan Pemberdayaan Industri',
                'deskripsi' => 'Untuk IKM (industri kecil menengah), melihat izin usaha IKM', 'fungsi_menu' => 'Publikasi data IKM/UMKM',
                'jenis_layanan' => 'publik', 'url' => 'sepat.riau.go.id', 'tanggal_pemberlakuan' => '2017-01-01', 'penempatan_server' => 'Diskominfotik',
                'programmer' => "M. Iskandarsyah, S.T\nRiandi Rahmat, S.T\nMhd Salahuddin Al Arif, S.T.", 'pengelola_adm' => 'Muhammad Desri Murdianto, S.Kom',
                'tim_pengujian_aplikasi' => 'Aulia Rahman, S.S.T', 'status_pengembangan' => 'menunggu_konfirmasi', 'dok' => [false, false, false, false],
                'catatan' => 'Untuk pembuatan SK tim, dasar surat hanya ada surat Disperindagkop-UKM perihal pengaktifan domain URL www.sepat.riau.go.id.',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 424, 'nama_aplikasi' => 'Whistle Blowing System (WBS)', 'opd' => 'Inspektorat Provinsi Riau',
                'status' => 'tidak_aktif',
                'deskripsi' => 'Aplikasi WBS (Whistleblowing System) adalah sistem berbasis web yang dirancang untuk memungkinkan siapa saja melaporkan dugaan pelanggaran, penyimpangan, atau tindak korupsi di lingkungan organisasi atau pemerintahan.',
                'fungsi_menu' => 'Memfasilitasi pelaporan yang aman dan rahasia, mendorong transparansi, serta mendukung upaya pencegahan dan pemberantasan korupsi.',
                'jenis_layanan' => 'publik', 'url' => 'wbs.inspektorat.riau.go.id', 'tanggal_pemberlakuan' => '2024-01-01',
            ]),

            // ===================== STATUS DIGANTI =====================
            $this->terdaftar([
                'nomor_registrasi' => 74, 'nama_aplikasi' => 'Bantuan Pendidikan Kesra Riau', 'opd' => 'Biro Kesejahteraan Rakyat',
                'status' => 'diganti', 'pengganti' => 75, 'jenis_layanan' => 'publik', 'url' => 'bantuanpendidikankesra.riau.go.id',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 99, 'nama_aplikasi' => 'Sistem Informasi Pengumpulan dan Pengolahan Data Keluarga Tidak Mampu (SIPADU)', 'opd' => 'Dinas Energi dan Sumber Daya Mineral',
                'status' => 'diganti', 'pengganti' => 439,
                'pic_aplikasi' => 'Kepala Seksi Pengelolaan dan Pemanfaatan SDE Cabang Dinas Wilayah II Rengat',
                'deskripsi' => 'Sistem Informasi Pengumpulan dan Pengolahan Data Keluarga Tidak Mampu',
                'fungsi_menu' => 'Untuk peningkatan proses pelayanan pengumpulan dan pengolahan data keluarga tidak mampu untuk penyaluran bantuan pasang baru listrik melalui SIPADU',
                'jenis_layanan' => 'publik', 'url' => 'sipadu-esdm.riau.go.id', 'tanggal_pemberlakuan' => '2024-04-09', 'penempatan_server' => 'Diskominfotik',
            ]),
            $this->terdaftar([
                'nomor_registrasi' => 177, 'nama_aplikasi' => 'Data Pembangunan', 'opd' => self::DISKOMINFO,
                'status' => 'diganti', 'pengganti' => 163, 'fungsi_menu' => 'Menampilkan data dan informasi seluruh wilayah di Provinsi Riau',
                'jenis_layanan' => 'publik', 'url' => 'sddkd.riau.go.id', 'penempatan_server' => 'Diskominfotik',
            ]),

            // ===================== DAFTAR PENGEMBANGAN 2025-2026 (BELUM BERNOMOR) =====================
            [
                'nama_aplikasi' => 'Website Bunda PAUD', 'opd' => 'Bunda PAUD', 'is_aplikasi_pemprov' => false,
                'programmer' => 'Syamrizal Angga Agusta, S.Kom', 'pengelola_adm' => 'Robby Zukri Putra, S.Kom',
            ],
            [
                'nama_aplikasi' => 'Aplikasi e-Gemilang (Pelaporan Izin Minerba Perusahaan)', 'opd' => 'Dinas Energi dan Sumber Daya Mineral', 'is_aplikasi_pemprov' => false,
                'programmer' => 'Aulia Rahman, S.S.T', 'pengelola_adm' => 'Eka Rahmadianingsih, S.S.T',
                'tim_pengujian_aplikasi' => "Martha Sari, S.Kom\nHendra K. R. Lase, S.T",
                'tim_pengujian_infrastruktur' => self::UJI_INFRA, 'tim_pengujian_keamanan' => self::UJI_KEAMANAN,
                'status_pengembangan' => 'on_progress', 'dok' => [false, true, true, false],
                'catatan' => 'Bulan Desember sudah selesai agar bisa digunakan di bulan Januari 2026.',
            ],
            [
                'nama_aplikasi' => 'Aplikasi Sistem Informasi Farmasi dan Logistik (SIFIT)', 'opd' => 'Dinas Kesehatan', 'is_aplikasi_pemprov' => false,
                'pic_aplikasi' => 'UPT Instalasi Farmasi dan Logistik Kesehatan',
                'programmer' => 'Hendra K. R. Lase, S.T', 'pengelola_adm' => 'Eka Rahmadianingsih, S.S.T',
                'status_pengembangan' => 'dibatalkan', 'dok' => [false, false, false, false],
                'catatan' => 'Karena tidak ada tindak lanjut dari surat nomor B/39/500.12.6/Diskominfotik/2025 tanggal 8 September 2025 hal Konfirmasi Tindak Lanjut Permintaan Pembangunan/Pengembangan Aplikasi/Website. Jika ingin dilanjutkan kembali, perlu membuat ulang surat permintaan.',
            ],
            [
                'nama_aplikasi' => 'Aplikasi Sistem Informasi Farmasi dan Logistik (SIFIT) Tahun 2026', 'opd' => 'Dinas Kesehatan', 'is_aplikasi_pemprov' => false,
                'pic_aplikasi' => 'UPT Instalasi Farmasi dan Logistik Kesehatan',
                'programmer' => 'Hendra K. R. Lase, S.T', 'pengelola_adm' => 'Eka Rahmadianingsih, S.S.T',
                'status_pengembangan' => 'menunggu_konfirmasi',
            ],
            [
                'nama_aplikasi' => 'Aplikasi Apo Kesah', 'opd' => self::DISKOMINFO, 'is_aplikasi_pemprov' => false,
                'pic_aplikasi' => 'Bidang Informasi dan Komunikasi Publik',
                'programmer' => 'Hendra K. R. Lase, S.T', 'pengelola_adm' => 'Yesi Hairian Wenda, S.Kom., M.Kom',
            ],
            [
                'nama_aplikasi' => 'Pengembangan Aplikasi Sistem Pengelolaan Administrasi Rapat (SIPENARA)', 'opd' => self::DISKOMINFO, 'is_aplikasi_pemprov' => false,
                'pic_aplikasi' => 'Bidang Aplikasi dan Informatika',
                'programmer' => 'Hendra K. R. Lase, S.T', 'pengelola_adm' => 'Eka Rahmadianingsih, S.S.T',
                'status_pengembangan' => 'dibatalkan',
            ],
            [
                'nama_aplikasi' => 'Portal Riau', 'opd' => self::DISKOMINFO, 'is_aplikasi_pemprov' => false,
                'programmer' => "Marda Arrauf, S.S.T\nM. Iskandarsyah, S.T", 'pengelola_adm' => 'Martha Sari, S.Kom',
                'tim_pengujian_infrastruktur' => 'Tri Julian Indra, S.Kom', 'tim_pengujian_keamanan' => 'Tiara Mulia Putri, S.Kom',
                'status_pengembangan' => 'on_progress', 'dok' => [false, true, false, false],
            ],
            [
                'nama_aplikasi' => 'Website Resmi Dinas Pariwisata Provinsi Riau', 'opd' => 'Dinas Pariwisata', 'is_aplikasi_pemprov' => false,
                'programmer' => 'Miskal, S.Kom', 'pengelola_adm' => 'Robby Zukri Putra, S.Kom',
            ],
            [
                'nama_aplikasi' => 'Integrasi SIJARI MANTAP dengan SIPANGLIMA', 'opd' => self::PUPR, 'is_aplikasi_pemprov' => false,
                'programmer' => 'Hendra K. R. Lase, S.T', 'pengelola_adm' => 'Siti Amelia Apriyanti, S.S.T',
            ],
            [
                'nama_aplikasi' => 'Website Resmi Dinas Pekerjaan Umum, Penataan Ruang, Perumahan, Kawasan Permukiman dan Pertanahan Provinsi Riau', 'opd' => self::PUPR, 'is_aplikasi_pemprov' => false,
                'programmer' => 'Miskal, S.Kom', 'pengelola_adm' => 'Robby Zukri Putra, S.Kom',
            ],
            [
                'nama_aplikasi' => 'Aplikasi Bantuan Keuangan Khusus (BKK)', 'opd' => 'Dinas Pemberdayaan Masyarakat, Desa, Kependudukan dan Pencatatan Sipil', 'is_aplikasi_pemprov' => false,
                'programmer' => 'Irawaty, S.Kom., M.Si',
            ],
            [
                'nama_aplikasi' => 'Aplikasi Sistem Informasi Gender Anak Provinsi Riau', 'opd' => self::DPPPA, 'is_aplikasi_pemprov' => false,
                'programmer' => "Mhd Salahuddin Al Arif, S.T.\nAulia Rahman, S.S.T", 'pengelola_adm' => 'Wakhid Primadi, S.Kom',
            ],
            [
                'nama_aplikasi' => 'Website Resmi Dinas Pemberdayaan Perempuan, Perlindungan Anak dan Pengendalian Penduduk Provinsi Riau', 'opd' => self::DPPPA, 'is_aplikasi_pemprov' => false,
                'programmer' => 'Wakhid Primadi, S.Kom', 'pengelola_adm' => 'Muhammad Desri Murdianto, S.Kom',
                'tim_pengujian_aplikasi' => 'Muhammad Fachrul Arli, S.Komp', 'tim_pengujian_infrastruktur' => self::UJI_INFRA, 'tim_pengujian_keamanan' => 'Tiara Mulia Putri, S.Kom',
                'status_pengembangan' => 'sudah_selesai', 'dok' => [false, true, false, false],
            ],
            [
                'nama_aplikasi' => 'Website Pemberdayaan dan Kesejahteraan Keluarga', 'opd' => self::DPPPA, 'is_aplikasi_pemprov' => false,
                'programmer' => 'Pandu Eka Saputra, S.Kom', 'pengelola_adm' => 'Yesi Hairian Wenda, S.Kom., M.Kom',
            ],
            [
                'nama_aplikasi' => 'Website Resmi Dinas Perkebunan Provinsi Riau', 'opd' => 'Dinas Perkebunan', 'is_aplikasi_pemprov' => false,
                'programmer' => 'Aulia Rahman, S.S.T', 'pengelola_adm' => 'Sonya Parlina Rizki, S.T',
            ],
            [
                'nama_aplikasi' => 'Website Resmi Komisi Informasi Provinsi Riau', 'opd' => 'Komisi Informasi', 'is_aplikasi_pemprov' => false,
                'programmer' => 'Hendra K. R. Lase, S.T', 'pengelola_adm' => 'Eka Rahmadianingsih, S.S.T',
                'tim_pengujian_aplikasi' => "Martha Sari, S.Kom\nYesi Hairian Wenda, S.Kom., M.Kom",
                'tim_pengujian_infrastruktur' => self::UJI_INFRA, 'tim_pengujian_keamanan' => self::UJI_KEAMANAN,
                'status_pengembangan' => 'sudah_selesai', 'dok' => [false, true, false, true],
                'catatan' => 'Menunggu proses uji keamanan (ITSA) dari Bidang Persandian, nota dinas sudah dikirim melalui SRIKANDI.',
            ],
        ];
    }
}
