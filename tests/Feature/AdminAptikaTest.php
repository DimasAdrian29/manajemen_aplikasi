<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\DokumenPerancangan;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\RapatProject;
use App\Models\User;
use App\Notifications\UndanganRapatNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminAptikaTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected User $opdUser;

    protected User $ketuaTim;

    protected User $anggotaTim;

    protected Instansi $dinkes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->adminAptika = User::where('username', 'admin_aptika')->first();
        $this->opdUser = User::where('username', 'opd_dinkes')->first();
        $this->ketuaTim = User::where('username', 'ahmad_lead')->first();
        $this->anggotaTim = User::where('username', 'bambang_dev')->first();
        $this->dinkes = Instansi::where('nama_instansi', 'like', '%Kesehatan%')->first();
    }

    public function test_admin_aptika_can_access_dashboard_and_see_metrics(): void
    {
        $response = $this->actingAs($this->adminAptika)->get(route('aptika.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Bidang APTIKA');
        $response->assertSee('Menunggu Telaah');
        $response->assertSee('Proyek Berjalan');
    }

    public function test_admin_aptika_can_review_and_approve_pengajuan(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0010',
            'instansi_id' => $this->dinkes->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Sistem Antrean Faskes',
            'deskripsi_kebutuhan' => 'Manajemen antrean pasien rawat jalan.',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->adminAptika)->patch(route('aptika.pengajuan.review', $pengajuan), [
            'status' => 'approved',
            'catatan_penilaian' => 'Arsitektur KAK memenuhi kriteria SPBE Riau.',
        ]);

        $response->assertRedirect(route('aptika.project.create', ['pengajuan_id' => $pengajuan->id]));

        $pengajuan->refresh();
        $this->assertEquals('approved', $pengajuan->status);
        $this->assertEquals($this->adminAptika->id, $pengajuan->dinilai_oleh);
        $this->assertNotNull($pengajuan->tanggal_penilaian);
    }

    public function test_admin_aptika_can_review_and_reject_pengajuan_with_reason(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0011',
            'instansi_id' => $this->dinkes->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Sistem Duplikasi',
            'deskripsi_kebutuhan' => 'Kebutuhan sudah terakomodir di aplikasi pusat.',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->adminAptika)->patch(route('aptika.pengajuan.review', $pengajuan), [
            'status' => 'rejected',
            'catatan_penilaian' => 'Ditolak: Terjadi duplikasi fungsi dengan aplikasi Satu Sehat Kemenkes.',
        ]);

        $response->assertRedirect(route('aptika.pengajuan.show', $pengajuan));

        $pengajuan->refresh();
        $this->assertEquals('rejected', $pengajuan->status);
        $this->assertStringContainsString('duplikasi', $pengajuan->catatan_penilaian);
    }

    public function test_admin_aptika_can_initiate_project_with_team_assignment(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0012',
            'instansi_id' => $this->dinkes->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'SI-Puskesmas Digital',
            'deskripsi_kebutuhan' => 'Sistem informasi puskesmas modern.',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->adminAptika)->post(route('aptika.project.store'), [
            'pengajuan_id' => $pengajuan->id,
            'nama_project' => 'Pengembangan SI-Puskesmas Digital v1.0',
            'tanggal_mulai' => '2026-10-10',
            'tanggal_selesai' => '2026-12-31',
            'ketua_tim_id' => $this->ketuaTim->id,
            'anggota_tim_ids' => [$this->anggotaTim->id],
        ]);

        $project = Project::where('nama_project', 'Pengembangan SI-Puskesmas Digital v1.0')->first();
        $this->assertNotNull($project);
        $this->assertEquals('initiated', $project->status);
        $this->assertEquals($pengajuan->id, $project->pengajuan_id);

        $response->assertRedirect(route('aptika.project.show', $project));

        // Verifikasi tim
        $this->assertDatabaseHas('tim_project', [
            'project_id' => $project->id,
            'user_id' => $this->ketuaTim->id,
            'peran' => 'ketua_tim',
        ]);

        $this->assertDatabaseHas('tim_project', [
            'project_id' => $project->id,
            'user_id' => $this->anggotaTim->id,
            'peran' => 'anggota',
        ]);
    }

    public function test_admin_aptika_can_record_functional_testing_and_transition_status(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Testing',
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0013',
            'instansi_id' => $this->dinkes->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi Testing',
            'deskripsi_kebutuhan' => 'Testing app',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Uji Fungsional',
            'status' => 'functional_testing',
            'progres_total' => 60,
        ]);

        $response = $this->actingAs($this->adminAptika)->post(route('aptika.testing.store'), [
            'project_id' => $project->id,
            'tanggal_testing' => '2026-10-05',
            'status_validasi' => 'lulus',
            'catatan_validasi' => 'Seluruh modul CRUD dan hak akses pengguna berfungsi dengan baik.',
        ]);

        $response->assertRedirect(route('aptika.testing.index'));

        $this->assertDatabaseHas('testing_aplikasi', [
            'project_id' => $project->id,
            'penguji_id' => $this->adminAptika->id,
            'status_validasi' => 'lulus',
        ]);

        $project->refresh();
        $this->assertEquals('security_testing', $project->status);
    }

    public function test_admin_aptika_can_schedule_meeting_and_notify_participants(): void
    {
        Notification::fake();

        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Rapat',
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0014',
            'instansi_id' => $this->dinkes->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi Rapat',
            'deskripsi_kebutuhan' => 'Rapat app',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Rapat',
            'status' => 'in_development',
        ]);

        $response = $this->actingAs($this->adminAptika)->post(route('aptika.rapat.store'), [
            'project_id' => $project->id,
            'nama_rapat' => 'Rapat Koordinasi Arsitektur Database',
            'waktu_rapat' => '2026-10-15 10:00:00',
            'tempat_rapat' => 'Ruang APTIKA Lt. 2',
            'deskripsi' => 'Pembahasan skema database dan integrasi API.',
            'peserta_ids' => [$this->ketuaTim->id, $this->opdUser->id],
        ]);

        $rapat = RapatProject::where('nama_rapat', 'Rapat Koordinasi Arsitektur Database')->first();
        $this->assertNotNull($rapat);

        $response->assertRedirect(route('aptika.rapat.show', $rapat));

        $this->assertDatabaseHas('peserta_rapat', [
            'rapat_project_id' => $rapat->id,
            'user_id' => $this->ketuaTim->id,
        ]);

        $this->assertDatabaseHas('peserta_rapat', [
            'rapat_project_id' => $rapat->id,
            'user_id' => $this->opdUser->id,
        ]);

        Notification::assertSentTo([$this->ketuaTim, $this->opdUser], UndanganRapatNotification::class);
    }

    public function test_admin_aptika_can_save_notulensi_and_complete_meeting(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Notulensi',
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0015',
            'instansi_id' => $this->dinkes->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi Notulensi',
            'deskripsi_kebutuhan' => 'Notulensi app',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Notulensi',
            'status' => 'in_development',
        ]);

        $rapat = RapatProject::create([
            'project_id' => $project->id,
            'dibuat_oleh' => $this->adminAptika->id,
            'nama_rapat' => 'Rapat Selesai',
            'waktu_rapat' => '2026-10-05 09:00:00',
            'status' => 'dijadwalkan',
        ]);

        $response = $this->actingAs($this->adminAptika)->patch(route('aptika.rapat.notulensi', $rapat), [
            'notulensi' => 'Disepakati menggunakan arsitektur REST API dengan autentikasi Sanctum.',
            'status' => 'selesai',
        ]);

        $response->assertRedirect(route('aptika.rapat.show', $rapat));

        $rapat->refresh();
        $this->assertEquals('selesai', $rapat->status);
        $this->assertStringContainsString('Sanctum', $rapat->notulensi);
    }

    public function test_admin_aptika_can_view_and_review_dokumen_perancangan(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi SRS',
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0099',
            'instansi_id' => $this->dinkes->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi SRS',
            'deskripsi_kebutuhan' => 'SRS test kebutuhan',
            'nama_pemohon' => 'dr. Anwar',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek SRS System',
            'status' => 'in_development',
        ]);

        $dokumen = DokumenPerancangan::create([
            'project_id' => $project->id,
            'jenis_dokumen' => 'srs',
            'file_dokumen' => 'https://docs.google.com/document/d/example-srs',
            'status' => 'menunggu_review',
            'catatan' => 'Draft awal SRS sistem puskesmas.',
        ]);

        // 1. Admin APTIKA melihat halaman detail proyek dan dokumen perancangan tampil
        $responseShow = $this->actingAs($this->adminAptika)->get(route('aptika.project.show', $project));
        $responseShow->assertOk();
        $responseShow->assertSee('Dokumen Perancangan Sistem');
        $responseShow->assertSee('Dokumen srs');
        $responseShow->assertSee('https://docs.google.com/document/d/example-srs');

        // 2. Admin APTIKA menelaah dokumen menjadi disetujui
        $responseReview = $this->actingAs($this->adminAptika)->patch(route('aptika.project.dokumen.review', [$project, $dokumen]), [
            'status' => 'disetujui',
            'catatan' => 'Spesifikasi SRS telah sesuai standar SPBE Riau.',
        ]);

        $responseReview->assertRedirect();
        $responseReview->assertSessionHas('success');

        $dokumen->refresh();
        $this->assertEquals('disetujui', $dokumen->status);
        $this->assertEquals($this->adminAptika->id, $dokumen->dinilai_oleh);
        $this->assertNotNull($dokumen->tanggal_penilaian);
        $this->assertEquals('Spesifikasi SRS telah sesuai standar SPBE Riau.', $dokumen->catatan);
    }

    public function test_non_aptika_cannot_access_aptika_portal(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('aptika.dashboard'));
        $response->assertStatus(403);
    }
}
