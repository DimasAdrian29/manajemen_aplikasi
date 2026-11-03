<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Layanan;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\LayananWorkflowSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkflowMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $adminAptika;

    protected User $opd1;

    protected User $opd2;

    protected Instansi $instansi1;

    protected Instansi $instansi2;

    protected Project $project1;

    protected Project $project2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        // Buat Master Dokumen Pengajuan
        MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Proposal Teknis / KAK',
            'kode_dokumen' => 'proposal_teknis',
            'deskripsi' => 'Kerangka Acuan Kerja',
            'wajib' => true,
            'berlaku_untuk' => 'semua',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 10240,
            'is_active' => true,
            'urutan' => 1,
        ]);

        $this->seed(LayananWorkflowSeeder::class);

        $this->instansi1 = Instansi::create([
            'nama_instansi' => 'Dinas Pendidikan Provinsi Riau',
            'email' => 'disdik@riau.go.id',
            'no_telepon' => '0761-11111',
            'alamat' => 'Pekanbaru',
        ]);

        $this->instansi2 = Instansi::create([
            'nama_instansi' => 'Dinas Kesehatan Provinsi Riau',
            'email' => 'diskes@riau.go.id',
            'no_telepon' => '0761-22222',
            'alamat' => 'Pekanbaru',
        ]);

        $this->superadmin = User::factory()->create([
            'name' => 'Superadmin System',
            'email' => 'superadmin@riau.go.id',
        ]);
        $this->superadmin->assignRole('superadmin');

        $this->adminAptika = User::factory()->create([
            'name' => 'Admin APTIKA Test',
            'email' => 'aptika@riau.go.id',
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $this->opd1 = User::factory()->create([
            'name' => 'Operator Disdik',
            'email' => 'operator.disdik@riau.go.id',
            'instansi_id' => $this->instansi1->id,
        ]);
        $this->opd1->assignRole('opd');

        $this->opd2 = User::factory()->create([
            'name' => 'Operator Diskes',
            'email' => 'operator.diskes@riau.go.id',
            'instansi_id' => $this->instansi2->id,
        ]);
        $this->opd2->assignRole('opd');

        $layanan = Layanan::first();

        // Project 1 milik Instansi 1
        $app1 = Aplikasi::create([
            'instansi_id' => $this->instansi1->id,
            'nama_aplikasi' => 'SI Beasiswa Riau',
            'status' => 'aktif',
        ]);

        $pengajuan1 = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261102-0001',
            'instansi_id' => $this->instansi1->id,
            'layanan_id' => $layanan->id,
            'aplikasi_id' => $app1->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'SI Beasiswa Riau',
            'deskripsi_kebutuhan' => 'Aplikasi beasiswa daerah',
            'nama_pemohon' => 'Budi Santoso',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $this->project1 = Project::create([
            'pengajuan_id' => $pengajuan1->id,
            'aplikasi_id' => $app1->id,
            'nama_project' => 'Pembangunan SI Beasiswa Riau',
            'progres_total' => 45,
            'status' => 'in_development',
            'current_workflow_id' => $layanan->workflows->first()?->id,
        ]);

        // Project 2 milik Instansi 2
        $app2 = Aplikasi::create([
            'instansi_id' => $this->instansi2->id,
            'nama_aplikasi' => 'SIM RSUD Riau',
            'status' => 'aktif',
        ]);

        $pengajuan2 = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261102-0002',
            'instansi_id' => $this->instansi2->id,
            'layanan_id' => $layanan->id,
            'aplikasi_id' => $app2->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'SIM RSUD Riau',
            'deskripsi_kebutuhan' => 'Sistem informasi rumah sakit daerah',
            'nama_pemohon' => 'Dr. Siti',
            'no_hp_pemohon' => '089876543210',
            'status' => 'approved',
        ]);

        $this->project2 = Project::create([
            'pengajuan_id' => $pengajuan2->id,
            'aplikasi_id' => $app2->id,
            'nama_project' => 'Pengembangan SIM RSUD Riau',
            'progres_total' => 70,
            'status' => 'functional_testing',
            'current_workflow_id' => $layanan->workflows->skip(1)->first()?->id,
        ]);
    }

    public function test_user_with_permission_can_access_workflow_monitoring_index(): void
    {
        $response = $this->actingAs($this->adminAptika)->get(route('workflow.monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('Pemantauan Alur Workflow Aplikasi Berjalan');
        $response->assertSee('SI Beasiswa Riau');
        $response->assertSee('SIM RSUD Riau');
    }

    public function test_user_without_permission_cannot_access_workflow_monitoring_when_turned_off(): void
    {
        // Matikan permission workflow.monitoring.view pada role admin_aptika (simulasi toggle OFF)
        $roleAptika = Role::findByName('admin_aptika');
        $roleAptika->revokePermissionTo('workflow.monitoring.view');

        $response = $this->actingAs($this->adminAptika)->get(route('workflow.monitoring.index'));

        $response->assertStatus(403);
    }

    public function test_opd_user_only_sees_projects_from_their_own_instansi(): void
    {
        $response = $this->actingAs($this->opd1)->get(route('workflow.monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('SI Beasiswa Riau');
        $response->assertDontSee('SIM RSUD Riau');
    }

    public function test_internal_user_can_see_all_projects_across_instansi(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('workflow.monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('SI Beasiswa Riau');
        $response->assertSee('SIM RSUD Riau');
    }

    public function test_workflow_monitoring_detail_view_shows_full_pipeline(): void
    {
        $response = $this->actingAs($this->adminAptika)->get(route('workflow.monitoring.show', $this->project1));

        $response->assertStatus(200);
        $response->assertSee('Pembangunan SI Beasiswa Riau');
        $response->assertSee('Riwayat Audit Tahapan Alur Kerja');
    }

    public function test_opd_cannot_view_monitoring_detail_of_other_instansi_project(): void
    {
        // OPD 1 mencoba melihat detail Proyek 2 milik OPD 2
        $response = $this->actingAs($this->opd1)->get(route('workflow.monitoring.show', $this->project2));

        $response->assertStatus(403);
    }
}
