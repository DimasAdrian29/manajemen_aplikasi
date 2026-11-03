<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Layanan;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\TahapanProject;
use App\Models\TimProject;
use App\Models\User;
use Database\Seeders\LayananWorkflowSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KetuaTimWorkflowSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $ketuaTim;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

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

        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas Kesehatan Provinsi Riau',
            'email' => 'diskes@riau.go.id',
            'no_telepon' => '0761-22222',
            'alamat' => 'Pekanbaru',
        ]);

        $this->ketuaTim = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@riau.go.id',
        ]);
        $this->ketuaTim->assignRole('ketua_tim');

        $layanan = Layanan::where('slug', 'pembangunan-aplikasi-baru')->first();

        $app = Aplikasi::create([
            'instansi_id' => $instansi->id,
            'nama_aplikasi' => 'e-Puskesmas Test',
            'status' => 'aktif',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261102-9999',
            'instansi_id' => $instansi->id,
            'layanan_id' => $layanan->id,
            'aplikasi_id' => $app->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'e-Puskesmas Test',
            'deskripsi_kebutuhan' => 'Kebutuhan puskesmas',
            'nama_pemohon' => 'Pemohon',
            'no_hp_pemohon' => '08123456789',
            'status' => 'approved',
        ]);

        // Inisialisasi pengajuan workflow data
        foreach ($layanan->workflows as $index => $lw) {
            $pengajuan->workflowData()->create([
                'layanan_workflow_id' => $lw->id,
                'status' => ($index < 2) ? 'completed' : (($index === 2) ? 'in_progress' : 'pending'),
                'completed_by' => ($index < 2) ? $this->ketuaTim->id : null,
                'completed_at' => ($index < 2) ? now() : null,
            ]);
        }

        $step3 = $layanan->workflows->firstWhere('workflow.slug', 'in_development');

        $this->project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $app->id,
            'nama_project' => 'Pembangunan e-Puskesmas Test',
            'progres_total' => 50,
            'status' => 'in_development',
            'current_workflow_id' => $step3->id,
        ]);

        TimProject::create([
            'project_id' => $this->project->id,
            'user_id' => $this->ketuaTim->id,
            'peran' => 'ketua_tim',
        ]);

        TahapanProject::create([
            'project_id' => $this->project->id,
            'nama_tahapan' => 'Coding Modul Pasien',
            'persentase' => 50,
            'status' => 'dalam_pengerjaan',
            'urutan' => 1,
        ]);
    }

    public function test_updating_progres_to_100_automatically_advances_workflow_to_functional_testing(): void
    {
        $response = $this->actingAs($this->ketuaTim)->patch(
            route('tim.project.progres', $this->project),
            [
                'progres_total' => 100,
                'tahapan' => [
                    [
                        'id' => $this->project->tahapan->first()->id,
                        'persentase' => 100,
                        'status' => 'selesai',
                    ],
                ],
            ]
        );

        $response->assertRedirect();
        $this->project->refresh();

        // Status proyek otomatis berubah menjadi functional_testing
        $this->assertEquals('functional_testing', $this->project->status);
        $this->assertEquals(100, $this->project->progres_total);

        // Current workflow id berubah ke tahapan Uji Fungsional Aplikasi
        $this->assertEquals('functional_testing', $this->project->currentWorkflow->workflow->slug);

        // Tahapan in_development pada workflowData ditandai completed
        $stepDevData = $this->project->pengajuan->workflowData()
            ->whereHas('layananWorkflow.workflow', fn ($q) => $q->where('slug', 'in_development'))
            ->first();
        $this->assertEquals('completed', $stepDevData->status);
        $this->assertNotNull($stepDevData->completed_at);

        // Tahapan functional_testing pada workflowData otomatis aktif (in_progress)
        $stepTestingData = $this->project->pengajuan->workflowData()
            ->whereHas('layananWorkflow.workflow', fn ($q) => $q->where('slug', 'functional_testing'))
            ->first();
        $this->assertEquals('in_progress', $stepTestingData->status);
    }

    public function test_ketua_tim_can_explicitly_submit_project_to_aptika_functional_testing(): void
    {
        $response = $this->actingAs($this->ketuaTim)->post(
            route('tim.project.serahkan_testing', $this->project),
            [
                'catatan' => 'Modul telah lengkap dan lulus pengetesan internal tim.',
            ]
        );

        $response->assertRedirect(route('tim.project.show', $this->project));
        $this->project->refresh();

        $this->assertEquals('functional_testing', $this->project->status);
        $this->assertEquals('functional_testing', $this->project->currentWorkflow->workflow->slug);
    }
}
