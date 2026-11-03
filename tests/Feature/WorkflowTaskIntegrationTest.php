<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Layanan;
use App\Models\LayananWorkflow;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\User;
use App\Models\Workflow;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkflowTaskIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected User $adminPersandian;

    protected User $adminInfrastruktur;

    protected User $ketuaTim;

    protected Instansi $instansi;

    protected Layanan $layanan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->instansi = Instansi::create([
            'nama_instansi' => 'Dinas Komunikasi Informatika dan Statistik Riau',
            'email' => 'diskominfotik@riau.go.id',
            'no_telepon' => '0761-123456',
            'alamat' => 'Pekanbaru',
        ]);

        $this->adminAptika = User::factory()->create([
            'username' => 'aptika_tester',
            'email' => 'aptika@riau.go.id',
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $this->adminPersandian = User::factory()->create([
            'username' => 'persandian_tester',
            'email' => 'csirt@riau.go.id',
        ]);
        $this->adminPersandian->assignRole('admin_persandian');

        $this->adminInfrastruktur = User::factory()->create([
            'username' => 'infra_tester',
            'email' => 'infra@riau.go.id',
        ]);
        $this->adminInfrastruktur->assignRole('admin_infrastruktur');

        $this->ketuaTim = User::factory()->create([
            'username' => 'ketua_tester',
            'email' => 'ketua@riau.go.id',
        ]);
        $this->ketuaTim->assignRole('ketua_tim');

        $this->layanan = Layanan::create([
            'nama' => 'Pembangunan Aplikasi Terintegrasi',
            'slug' => 'pembangunan-aplikasi-terintegrasi',
            'deskripsi' => 'Layanan pembuatan sistem',
            'urutan' => 1,
            'is_active' => true,
        ]);
    }

    public function test_aptika_sees_incoming_pengajuan_queue_in_workflow_tasks(): void
    {
        // 1. Pengajuan submitted yang butuh telaah
        $pengajuanTelaah = Pengajuan::create([
            'kode_pengajuan' => 'REG-TELAAH-001',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $this->layanan->id,
            'nama_aplikasi' => 'Aplikasi Uji Telaah',
            'deskripsi_kebutuhan' => 'Kebutuhan telaah awal',
            'nama_pemohon' => 'Budi Santoso',
            'no_hp_pemohon' => '081234567890',
            'jenis_pengajuan' => 'new_app',
            'status' => 'submitted',
        ]);

        // 2. Pengajuan approved tanpa project yang butuh inisiasi proyek
        $pengajuanInisiasi = Pengajuan::create([
            'kode_pengajuan' => 'REG-INISIASI-002',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $this->layanan->id,
            'nama_aplikasi' => 'Aplikasi Siap Inisiasi',
            'deskripsi_kebutuhan' => 'Kebutuhan inisiasi tim',
            'nama_pemohon' => 'Siti Rahma',
            'no_hp_pemohon' => '081234567891',
            'jenis_pengajuan' => 'new_app',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->adminAptika)->get(route('workflow.task.index'));

        $response->assertOk();
        $response->assertSee('Antrean Berkas Permohonan Masuk');
        $response->assertSee('Aplikasi Uji Telaah');
        $response->assertSee('Buka Telaah Berkas');
        $response->assertSee(route('aptika.pengajuan.show', $pengajuanTelaah));

        $response->assertSee('Aplikasi Siap Inisiasi');
        $response->assertSee('Inisiasi Proyek Baru');
        $response->assertSee(route('aptika.project.create', ['pengajuan_id' => $pengajuanInisiasi->id]));
    }

    public function test_infrastruktur_sees_decommissioning_pengajuan_in_workflow_tasks(): void
    {
        $layananNonaktif = Layanan::create([
            'nama' => 'Penonaktifan Aplikasi',
            'slug' => 'penonaktifan-aplikasi',
            'deskripsi' => 'Layanan decommissioning',
            'urutan' => 2,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'SIM Jadul OPD',
            'instansi_id' => $this->instansi->id,
            'status' => 'aktif',
        ]);

        Pengajuan::create([
            'kode_pengajuan' => 'REG-NONAKTIF-003',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $layananNonaktif->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'SIM Jadul OPD',
            'deskripsi_kebutuhan' => 'Server sudah tidak dipakai',
            'nama_pemohon' => 'Kadis OPD',
            'no_hp_pemohon' => '081234567899',
            'jenis_pengajuan' => 'app_deactivation',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->adminInfrastruktur)->get(route('workflow.task.index'));

        $response->assertOk();
        $response->assertSee('Antrean Berkas Permohonan Masuk');
        $response->assertSee('SIM Jadul OPD');
        $response->assertSee('Eksekusi Penonaktifan Server');
        $response->assertSee(route('infrastruktur.penonaktifan.index'));
    }

    public function test_smart_redirect_to_aptika_testing_on_functional_testing_step(): void
    {
        $roleAptika = Role::where('name', 'admin_aptika')->first();

        $wfTesting = Workflow::create([
            'nama' => 'Kendali Mutu & Uji Fungsional',
            'slug' => 'functional_testing',
            'deskripsi' => 'Uji fungsional sistem',
            'role_id' => $roleAptika->id,
        ]);

        $lw = LayananWorkflow::create([
            'layanan_id' => $this->layanan->id,
            'workflow_id' => $wfTesting->id,
            'urutan' => 4,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'SIMRS Testing',
            'instansi_id' => $this->instansi->id,
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-TEST-004',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $this->layanan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'SIMRS Testing',
            'deskripsi_kebutuhan' => 'Uji mutu',
            'nama_pemohon' => 'drg. Rina',
            'no_hp_pemohon' => '081234567811',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek SIMRS Testing',
            'status' => 'functional_testing',
            'current_workflow_id' => $lw->id,
            'progres_total' => 70,
        ]);

        // Default: smart redirect langsung ke aptika.testing.create
        $response = $this->actingAs($this->adminAptika)->get(route('workflow.task.show', $project));
        $response->assertRedirect(route('aptika.testing.create', ['project_id' => $project->id]));

        // With no_redirect=1: tetap di workflow.task.show dengan banner penunjuk ke aptika.testing.create
        $responseNoRedirect = $this->actingAs($this->adminAptika)->get(route('workflow.task.show', [$project, 'no_redirect' => 1]));
        $responseNoRedirect->assertOk();
        $responseNoRedirect->assertSee('Kendali Mutu');
        $responseNoRedirect->assertSee(route('aptika.testing.create', ['project_id' => $project->id]));
    }

    public function test_smart_redirect_to_persandian_csirt_on_security_testing_step(): void
    {
        $rolePersandian = Role::where('name', 'admin_persandian')->first();

        $wfCsirt = Workflow::create([
            'nama' => 'Audit Keamanan Informasi (CSIRT)',
            'slug' => 'security_testing',
            'deskripsi' => 'Uji kerentanan CSIRT',
            'role_id' => $rolePersandian->id,
        ]);

        $lw = LayananWorkflow::create([
            'layanan_id' => $this->layanan->id,
            'workflow_id' => $wfCsirt->id,
            'urutan' => 5,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'Aplikasi CSIRT',
            'instansi_id' => $this->instansi->id,
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-CSIRT-005',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $this->layanan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'Aplikasi CSIRT',
            'deskripsi_kebutuhan' => 'Audit keamanan',
            'nama_pemohon' => 'CSIRT Officer',
            'no_hp_pemohon' => '081234567822',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Aplikasi CSIRT',
            'status' => 'security_testing',
            'current_workflow_id' => $lw->id,
            'progres_total' => 85,
        ]);

        $response = $this->actingAs($this->adminPersandian)->get(route('workflow.task.show', $project));
        $response->assertRedirect(route('persandian.pengujian.create', $project));
    }

    public function test_smart_redirect_to_infrastruktur_deployment_on_deployment_step(): void
    {
        $roleInfra = Role::where('name', 'admin_infrastruktur')->first();

        $wfDeploy = Workflow::create([
            'nama' => 'Deployment & Integrasi Hosting',
            'slug' => 'deployment',
            'deskripsi' => 'Alokasi hosting',
            'role_id' => $roleInfra->id,
        ]);

        $lw = LayananWorkflow::create([
            'layanan_id' => $this->layanan->id,
            'workflow_id' => $wfDeploy->id,
            'urutan' => 6,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'Aplikasi Deployment',
            'instansi_id' => $this->instansi->id,
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-DEPLOY-006',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $this->layanan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'Aplikasi Deployment',
            'deskripsi_kebutuhan' => 'Deploy hosting',
            'nama_pemohon' => 'Infra Officer',
            'no_hp_pemohon' => '081234567833',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Aplikasi Deployment',
            'status' => 'deployment',
            'current_workflow_id' => $lw->id,
            'progres_total' => 95,
        ]);

        $response = $this->actingAs($this->adminInfrastruktur)->get(route('workflow.task.show', $project));
        $response->assertRedirect(route('infrastruktur.deployment.create', $project));
    }

    public function test_smart_redirect_to_tim_project_show_on_development_step(): void
    {
        $roleTim = Role::where('name', 'ketua_tim')->first();

        $wfDev = Workflow::create([
            'nama' => 'Pengerjaan & Pengembangan',
            'slug' => 'in_development',
            'deskripsi' => 'Pengembangan sprint',
            'role_id' => $roleTim->id,
        ]);

        $lw = LayananWorkflow::create([
            'layanan_id' => $this->layanan->id,
            'workflow_id' => $wfDev->id,
            'urutan' => 3,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'Aplikasi Dev Tim',
            'instansi_id' => $this->instansi->id,
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-DEV-007',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $this->layanan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'Aplikasi Dev Tim',
            'deskripsi_kebutuhan' => 'Sprint 1',
            'nama_pemohon' => 'Dev Team',
            'no_hp_pemohon' => '081234567844',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Dev Tim',
            'status' => 'in_development',
            'current_workflow_id' => $lw->id,
            'progres_total' => 50,
        ]);

        $response = $this->actingAs($this->ketuaTim)->get(route('workflow.task.show', $project));
        $response->assertRedirect(route('tim.project.show', $project));
    }
}
