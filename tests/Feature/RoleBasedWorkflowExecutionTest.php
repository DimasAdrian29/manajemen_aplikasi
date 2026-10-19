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
use App\Models\WorkflowField;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleBasedWorkflowExecutionTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected User $persandianUser;

    protected User $opdUser;

    protected Instansi $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->instansi = Instansi::create([
            'nama_instansi' => 'Dinas Kesehatan Provinsi Riau',
            'email' => 'dinkes@riau.go.id',
            'no_telepon' => '0761-99887',
            'alamat' => 'Pekanbaru',
        ]);

        $this->adminAptika = User::factory()->create([
            'username' => 'aptika_tester',
            'email' => 'aptika@riau.go.id',
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $this->persandianUser = User::factory()->create([
            'username' => 'csirt_tester',
            'email' => 'csirt@riau.go.id',
        ]);
        $this->persandianUser->assignRole('admin_persandian');

        $this->opdUser = User::factory()->create([
            'username' => 'opd_tester',
            'email' => 'opd@riau.go.id',
            'instansi_id' => $this->instansi->id,
        ]);
        $this->opdUser->assignRole('opd');
    }

    public function test_aptika_can_create_workflow_with_assigned_role(): void
    {
        $persandianRole = Role::where('name', 'admin_persandian')->first();

        $response = $this->actingAs($this->adminAptika)->post(route('aptika.workflow.store'), [
            'nama' => 'Pengecekan Database',
            'deskripsi' => 'Pengujian struktur, indeks, dan sanitasi database oleh Tim Persandian.',
            'ikon' => 'database',
            'role_id' => $persandianRole->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('workflow', [
            'nama' => 'Pengecekan Database',
            'slug' => 'pengecekan-database',
            'role_id' => $persandianRole->id,
        ]);
    }

    public function test_user_with_assigned_role_sees_task_in_universal_task_inbox(): void
    {
        $persandianRole = Role::where('name', 'admin_persandian')->first();

        $wfDbCheck = Workflow::create([
            'nama' => 'Pengecekan Database',
            'slug' => 'pengecekan-database',
            'deskripsi' => 'Audit Database',
            'role_id' => $persandianRole->id,
        ]);

        $layanan = Layanan::create([
            'nama' => 'Layanan Khusus Data',
            'slug' => 'layanan-khusus-data',
            'deskripsi' => 'Testing layanan',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $lw = LayananWorkflow::create([
            'layanan_id' => $layanan->id,
            'workflow_id' => $wfDbCheck->id,
            'urutan' => 1,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'SIM-Puskesmas',
            'instansi_id' => $this->instansi->id,
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261015-0001',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $layanan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'SIM-Puskesmas',
            'deskripsi_kebutuhan' => 'Kebutuhan database puskesmas',
            'nama_pemohon' => 'dr. Budi',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek SIM-Puskesmas',
            'status' => 'in_development',
            'current_workflow_id' => $lw->id,
            'progres_total' => 20,
        ]);

        // Persandian user SHOULD see this task
        $responsePersandian = $this->actingAs($this->persandianUser)->get(route('workflow.task.index'));
        $responsePersandian->assertOk();
        $responsePersandian->assertSee('Proyek SIM-Puskesmas');
        $responsePersandian->assertSee('Pengecekan Database');

        // OPD user should NOT see this task (because it's assigned to admin_persandian)
        $responseOpd = $this->actingAs($this->opdUser)->get(route('workflow.task.index'));
        $responseOpd->assertOk();
        $responseOpd->assertDontSee('Proyek SIM-Puskesmas');
    }

    public function test_assigned_role_user_can_advance_workflow_task_with_dynamic_fields(): void
    {
        $persandianRole = Role::where('name', 'admin_persandian')->first();

        $wfDbCheck = Workflow::create([
            'nama' => 'Pengecekan Database',
            'slug' => 'pengecekan-database',
            'deskripsi' => 'Audit Database',
            'role_id' => $persandianRole->id,
        ]);

        $fieldChecklist = WorkflowField::create([
            'workflow_id' => $wfDbCheck->id,
            'label' => 'Sanitasi SQL Injection Terverifikasi',
            'tipe' => 'checklist',
            'wajib' => true,
            'urutan' => 1,
        ]);

        $wfNext = Workflow::create([
            'nama' => 'Go Live Produksi',
            'slug' => 'deployment',
            'deskripsi' => 'Publish',
        ]);

        $layanan = Layanan::create([
            'nama' => 'Layanan Database',
            'slug' => 'layanan-database',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $lw1 = LayananWorkflow::create([
            'layanan_id' => $layanan->id,
            'workflow_id' => $wfDbCheck->id,
            'urutan' => 1,
            'is_active' => true,
        ]);

        $lw2 = LayananWorkflow::create([
            'layanan_id' => $layanan->id,
            'workflow_id' => $wfNext->id,
            'urutan' => 2,
            'is_active' => true,
        ]);

        $aplikasi = Aplikasi::create([
            'nama_aplikasi' => 'Sistem Antrean Dinkes',
            'instansi_id' => $this->instansi->id,
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261015-0002',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $layanan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'Sistem Antrean Dinkes',
            'deskripsi_kebutuhan' => 'Kebutuhan database antrean',
            'nama_pemohon' => 'dr. Siti',
            'no_hp_pemohon' => '081234567891',
            'status' => 'approved',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Antrean Dinkes',
            'status' => 'in_development',
            'current_workflow_id' => $lw1->id,
            'progres_total' => 30,
        ]);

        // Attempt by unauthorized OPD user should be 403 Forbidden
        $responseUnauthorized = $this->actingAs($this->opdUser)->post(route('workflow.task.advance', $project), [
            'workflow_fields' => [
                $fieldChecklist->id => '1',
            ],
            'catatan' => 'Mencoba eksekusi tanpa hak akses',
        ]);
        $responseUnauthorized->assertForbidden();

        // Advance by authorized Persandian user
        $responseAuthorized = $this->actingAs($this->persandianUser)->post(route('workflow.task.advance', $project), [
            'workflow_fields' => [
                $fieldChecklist->id => '1',
            ],
            'catatan' => 'Struktur database aman dan telah memenuhi standar keamanan data.',
        ]);

        $responseAuthorized->assertRedirect(route('workflow.task.index'));

        // Project should advance to next workflow step (lw2)
        $project->refresh();
        $this->assertEquals($lw2->id, $project->current_workflow_id);
        $this->assertDatabaseHas('pengajuan_field_data', [
            'workflow_field_id' => $fieldChecklist->id,
            'value' => '1',
        ]);
    }
}
