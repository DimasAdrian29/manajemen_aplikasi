<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\PengujianKeamanan;
use App\Models\Project;
use App\Models\TimProject;
use App\Models\User;
use App\Notifications\HasilUjiKeamananNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPersandianTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminPersandian;

    protected User $opdUser;

    protected User $adminAptika;

    protected User $ketuaTim;

    protected Instansi $diskominfo;

    protected Instansi $dinkes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->adminPersandian = User::where('username', 'admin_persandian')->first();
        $this->opdUser = User::where('username', 'opd_dinkes')->first();
        $this->adminAptika = User::where('username', 'admin_aptika')->first();
        $this->ketuaTim = User::where('username', 'ahmad_lead')->first();
        $this->diskominfo = Instansi::where('nama_instansi', 'like', '%Komunikasi%')->first();
        $this->dinkes = Instansi::where('nama_instansi', 'like', '%Kesehatan%')->first();
    }

    /**
     * Helper to create a Project with required Pengajuan and Ketua Tim.
     */
    protected function createProjectWithPengajuan(
        Aplikasi $aplikasi,
        string $namaProject,
        string $status = 'security_testing',
        int $progres = 85
    ): Project {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-'.strtoupper(uniqid()),
            'instansi_id' => $aplikasi->instansi_id,
            'aplikasi_id' => $aplikasi->id,
            'dinilai_oleh' => $this->adminAptika->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => $aplikasi->nama_aplikasi,
            'deskripsi_kebutuhan' => 'Kebutuhan sistem aplikasi kesehatan terintegrasi.',
            'nama_pemohon' => 'Narahubung Dinkes',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => $namaProject,
            'status' => $status,
            'progres_total' => $progres,
        ]);

        TimProject::create([
            'project_id' => $project->id,
            'user_id' => $this->ketuaTim->id,
            'peran' => 'ketua_tim',
        ]);

        return $project;
    }

    public function test_unauthorized_user_cannot_access_persandian_portal(): void
    {
        // Guest redirected to login
        $guestResponse = $this->get(route('persandian.dashboard'));
        $guestResponse->assertRedirect(route('login'));

        // OPD user gets 403 forbidden
        $opdResponse = $this->actingAs($this->opdUser)->get(route('persandian.dashboard'));
        $opdResponse->assertStatus(403);
    }

    public function test_admin_persandian_can_access_dashboard_and_see_metrics(): void
    {
        $response = $this->actingAs($this->adminPersandian)->get(route('persandian.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Persandian');
        $response->assertSee('Antrean Uji Keamanan');
        $response->assertSee('Lulus Audit Siber');
    }

    public function test_security_testing_queue_only_shows_projects_in_security_testing_status(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Antrean Rumah Sakit',
            'status' => 'pengembangan',
        ]);

        $projectSecurity = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Modul Antrean Rumah Sakit',
            'security_testing',
            85
        );

        $projectFunctional = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Modul Rekam Medis',
            'functional_testing',
            70
        );

        $response = $this->actingAs($this->adminPersandian)->get(route('persandian.pengujian.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengembangan Modul Antrean Rumah Sakit');
        $response->assertDontSee('Pengembangan Modul Rekam Medis');
    }

    public function test_admin_persandian_can_access_create_audit_form(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Rekam Medis Elektronik',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan RME Rumah Sakit',
            'security_testing',
            85
        );

        $response = $this->actingAs($this->adminPersandian)->get(route('persandian.pengujian.create', $project));

        $response->assertStatus(200);
        $response->assertSee('Form Audit Keamanan Informasi');
        $response->assertSee('Pengembangan RME Rumah Sakit');
        $response->assertSee('Vulnerability Assessment');
    }

    public function test_admin_persandian_cannot_audit_project_not_in_security_testing_status(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Rekam Medis',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan RME',
            'in_development',
            50
        );

        $response = $this->actingAs($this->adminPersandian)->get(route('persandian.pengujian.create', $project));

        $response->assertRedirect(route('persandian.pengujian.index'));
        $response->assertSessionHas('error');
    }

    public function test_audit_lulus_automatically_advances_project_to_deployment_and_sends_notification(): void
    {
        Notification::fake();

        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Sistem Rujukan Terpadu',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Sistem Rujukan Terpadu',
            'security_testing',
            85
        );

        $auditData = [
            'kategori_pengujian' => 'penetration_testing',
            'hasil' => 'lulus',
            'tingkat_risiko' => 'aman',
            'tanggal_uji' => now()->toDateString(),
            'nomor_surat' => 'SKLO/DISKOMINFO-PERSANDIAN/2026/001',
            'catatan_kerentanan' => 'Tidak ditemukan celah berbahaya berkategori High maupun Critical.',
            'rekomendasi' => 'Aplikasi laik rilis ke server produksi dan memenuhi standar kelaikan operasi.',
            'keterangan' => 'Audit blackbox dan graybox penetrasi tuntas 100%.',
        ];

        $response = $this->actingAs($this->adminPersandian)
            ->post(route('persandian.pengujian.store', $project), $auditData);

        // Project must transition to deployment with progress at least 90%
        $project->refresh();
        $this->assertEquals('deployment', $project->status);
        $this->assertGreaterThanOrEqual(90, $project->progres_total);

        // Audit record created in DB
        $this->assertDatabaseHas('pengujian_keamanan', [
            'project_id' => $project->id,
            'user_id' => $this->adminPersandian->id,
            'hasil' => 'lulus',
            'tingkat_risiko' => 'aman',
            'nomor_surat' => 'SKLO/DISKOMINFO-PERSANDIAN/2026/001',
        ]);

        // Notifications sent to Ketua Tim and Reviewer APTIKA
        Notification::assertSentTo($this->ketuaTim, HasilUjiKeamananNotification::class);
        Notification::assertSentTo($this->adminAptika, HasilUjiKeamananNotification::class);

        $pengujian = PengujianKeamanan::where('project_id', $project->id)->first();
        $response->assertRedirect(route('persandian.pengujian.show', $pengujian));
        $response->assertSessionHas('success');
    }

    public function test_audit_perbaikan_returns_project_to_in_development(): void
    {
        Notification::fake();

        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Portal Farmasi',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Portal Farmasi',
            'security_testing',
            85
        );

        $auditData = [
            'kategori_pengujian' => 'vulnerability_assessment',
            'hasil' => 'perbaikan',
            'tingkat_risiko' => 'tinggi',
            'tanggal_uji' => now()->toDateString(),
            'catatan_kerentanan' => 'Ditemukan potensi Cross-Site Scripting (XSS) dan Blind SQL Injection pada modul resep obat.',
            'rekomendasi' => 'Gunakan parameterized query dan sanitasi input form sebelum data diproses.',
            'keterangan' => 'Perlu perbaikan segera dari tim developer.',
        ];

        $response = $this->actingAs($this->adminPersandian)
            ->post(route('persandian.pengujian.store', $project), $auditData);

        // Project must return to in_development
        $project->refresh();
        $this->assertEquals('in_development', $project->status);

        $this->assertDatabaseHas('pengujian_keamanan', [
            'project_id' => $project->id,
            'hasil' => 'perbaikan',
            'tingkat_risiko' => 'tinggi',
        ]);

        $pengujian = PengujianKeamanan::where('project_id', $project->id)->first();
        $response->assertRedirect(route('persandian.pengujian.show', $pengujian));
    }

    public function test_audit_with_file_upload_stores_document(): void
    {
        Storage::fake('public');

        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'SI Penyakit Menular',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan SI Penyakit Menular',
            'security_testing',
            85
        );

        $dummyPdf = UploadedFile::fake()->create('laporan_pentest_lengkap.pdf', 1024, 'application/pdf');

        $auditData = [
            'kategori_pengujian' => 'penetration_testing',
            'hasil' => 'lulus',
            'tingkat_risiko' => 'rendah',
            'tanggal_uji' => now()->toDateString(),
            'nomor_surat' => 'SKLO/CSIRT/2026/099',
            'catatan_kerentanan' => 'Hanya informational finding: banner grab server version.',
            'rekomendasi' => 'Nonaktifkan header Server info di Nginx.',
            'file_hasil' => $dummyPdf,
        ];

        $response = $this->actingAs($this->adminPersandian)
            ->post(route('persandian.pengujian.store', $project), $auditData);

        $pengujian = PengujianKeamanan::where('project_id', $project->id)->first();
        $this->assertNotNull($pengujian->file_hasil);
        Storage::disk('public')->assertExists($pengujian->file_hasil);

        $response->assertRedirect(route('persandian.pengujian.show', $pengujian));
    }

    public function test_admin_persandian_can_view_audit_detail_sheet(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Sistem Laboratorium Daerah',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Sistem Labda',
            'deployment',
            90
        );

        $pengujian = PengujianKeamanan::create([
            'project_id' => $project->id,
            'user_id' => $this->adminPersandian->id,
            'kategori_pengujian' => 'source_code_review',
            'hasil' => 'lulus',
            'tingkat_risiko' => 'aman',
            'tanggal_uji' => now()->toDateString(),
            'nomor_surat' => 'SKLO/LABDA/2026/042',
            'catatan_kerentanan' => 'Clean of critical vulnerabilities.',
            'rekomendasi' => 'Disetujui untuk rilis publik.',
        ]);

        $response = $this->actingAs($this->adminPersandian)
            ->get(route('persandian.pengujian.show', $pengujian));

        $response->assertStatus(200);
        $response->assertSee('Lembar Hasil Audit Keamanan Informasi');
        $response->assertSee('SKLO/LABDA/2026/042');
        $response->assertSee('Pengembangan Sistem Labda');
        $response->assertSee('Lulus (Laik Operasi)');
    }

    public function test_admin_persandian_can_filter_audit_history_catalogue(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Sistem Kesehatan',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Sistem Kesehatan',
            'deployment',
            90
        );

        PengujianKeamanan::create([
            'project_id' => $project->id,
            'user_id' => $this->adminPersandian->id,
            'kategori_pengujian' => 'penetration_testing',
            'hasil' => 'lulus',
            'tingkat_risiko' => 'aman',
            'tanggal_uji' => now()->toDateString(),
            'nomor_surat' => 'SKLO/LULUS/001',
        ]);

        PengujianKeamanan::create([
            'project_id' => $project->id,
            'user_id' => $this->adminPersandian->id,
            'kategori_pengujian' => 'vulnerability_assessment',
            'hasil' => 'perbaikan',
            'tingkat_risiko' => 'kritis',
            'tanggal_uji' => now()->toDateString(),
            'nomor_surat' => 'SKLO/TEMUAN/002',
        ]);

        // Access catalogue without filter
        $response = $this->actingAs($this->adminPersandian)->get(route('persandian.pengujian.riwayat'));
        $response->assertStatus(200);
        $response->assertSee('SKLO/LULUS/001');
        $response->assertSee('SKLO/TEMUAN/002');

        // Access catalogue filtered by hasil=lulus
        $responseLulus = $this->actingAs($this->adminPersandian)->get(route('persandian.pengujian.riwayat', ['hasil' => 'lulus']));
        $responseLulus->assertStatus(200);
        $responseLulus->assertSee('SKLO/LULUS/001');
        $responseLulus->assertDontSee('SKLO/TEMUAN/002');
    }
}
