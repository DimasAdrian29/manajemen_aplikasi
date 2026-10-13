<?php

namespace Tests\Feature;

use App\Models\Instansi;
use App\Models\Layanan;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Workflow;
use Database\Seeders\LayananWorkflowSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DynamicLayananAndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected User $opdUser;

    protected Instansi $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->instansi = Instansi::create([
            'nama_instansi' => 'Dinas Komunikasi dan Informatika',
            'email' => 'diskominfo@riau.go.id',
            'no_telepon' => '0761-12345',
            'alamat' => 'Pekanbaru',
        ]);

        // Create Master Dokumen Pengajuan needed by LayananWorkflowSeeder
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

        MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Surat Pernyataan Tanggung Jawab Mutlak (SPTJM)',
            'kode_dokumen' => 'sptjm',
            'deskripsi' => 'SPTJM',
            'wajib' => true,
            'berlaku_untuk' => 'semua',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 5120,
            'is_active' => true,
            'urutan' => 2,
        ]);

        MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Surat Pengantar Kepala Dinas',
            'kode_dokumen' => 'surat_pengantar',
            'deskripsi' => 'Surat Pengantar',
            'wajib' => false,
            'berlaku_untuk' => 'semua',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 5120,
            'is_active' => true,
            'urutan' => 3,
        ]);

        $this->seed(LayananWorkflowSeeder::class);

        $this->adminAptika = User::factory()->create([
            'instansi_id' => $this->instansi->id,
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $this->opdUser = User::factory()->create([
            'instansi_id' => $this->instansi->id,
        ]);
        $this->opdUser->assignRole('opd');
    }

    public function test_admin_aptika_can_view_layanan_list_and_create_new_layanan(): void
    {
        $this->actingAs($this->adminAptika)
            ->get(route('aptika.layanan.index'))
            ->assertStatus(200)
            ->assertSee('Pembangunan Aplikasi Baru')
            ->assertSee('Pengembangan Aplikasi Eksisting')
            ->assertSee('Penonaktifan Aplikasi');

        $response = $this->actingAs($this->adminAptika)
            ->post(route('aptika.layanan.store'), [
                'nama' => 'Integrasi Interoperabilitas SPBE',
                'deskripsi' => 'Layanan khusus penghubung data API antar-perangkat daerah.',
                'ikon' => 'hub',
                'warna' => '#8B5CF6',
                'is_active' => 1,
            ]);

        $layanan = Layanan::where('slug', 'integrasi-interoperabilitas-spbe')->first();
        $this->assertNotNull($layanan);
        $response->assertRedirect(route('aptika.layanan.show', $layanan));

        $this->assertDatabaseHas('layanan', [
            'nama' => 'Integrasi Interoperabilitas SPBE',
            'slug' => 'integrasi-interoperabilitas-spbe',
        ]);
    }

    public function test_admin_aptika_can_attach_and_reorder_workflow_steps_in_layanan(): void
    {
        $layanan = Layanan::where('slug', 'penonaktifan-aplikasi')->first();
        $this->assertNotNull($layanan);

        $workflowDeployment = Workflow::where('slug', 'deployment')->first();
        $this->assertNotNull($workflowDeployment);

        // Attach workflow to Layanan
        $this->actingAs($this->adminAptika)
            ->post(route('aptika.layanan.workflow.attach', $layanan), [
                'workflow_id' => $workflowDeployment->id,
            ])
            ->assertSessionHas('success');

        $attached = $layanan->workflows()->where('workflow_id', $workflowDeployment->id)->first();
        $this->assertNotNull($attached);
        $this->assertEquals(4, $attached->urutan);

        // Reorder up
        $this->actingAs($this->adminAptika)
            ->patch(route('aptika.layanan.workflow.reorder', $layanan), [
                'layanan_workflow_id' => $attached->id,
                'direction' => 'up',
            ])
            ->assertSessionHas('success');

        $this->assertEquals(3, $attached->fresh()->urutan);
    }

    public function test_admin_aptika_can_create_workflow_and_configure_fields(): void
    {
        $response = $this->actingAs($this->adminAptika)
            ->post(route('aptika.workflow.store'), [
                'nama' => 'Audit Algoritma SPBE',
                'deskripsi' => 'Pengujian kesesuaian formula komputasi dan arsitektur algoritma.',
                'ikon' => 'calculate',
            ]);

        $workflow = Workflow::where('slug', 'audit-algoritma-spbe')->first();
        $this->assertNotNull($workflow);
        $response->assertRedirect(route('aptika.workflow.show', $workflow));

        // Add Checklist Field
        $this->actingAs($this->adminAptika)
            ->post(route('aptika.workflow.field.store', $workflow), [
                'label' => 'Formula telah divalidasi oleh Badan Pusat Statistik',
                'tipe' => 'checklist',
                'wajib' => 1,
                'keterangan' => 'Centang jika validasi BPS sudah sah.',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('workflow_field', [
            'workflow_id' => $workflow->id,
            'label' => 'Formula telah divalidasi oleh Badan Pusat Statistik',
            'tipe' => 'checklist',
            'wajib' => true,
        ]);
    }

    public function test_admin_aptika_can_toggle_layanan_status(): void
    {
        $layanan = Layanan::first();
        $this->assertTrue($layanan->is_active);

        $this->actingAs($this->adminAptika)
            ->patch(route('aptika.layanan.toggle', $layanan))
            ->assertSessionHas('success');

        $this->assertFalse($layanan->fresh()->is_active);

        $this->actingAs($this->adminAptika)
            ->patch(route('aptika.layanan.toggle', $layanan))
            ->assertSessionHas('success');

        $this->assertTrue($layanan->fresh()->is_active);
    }

    public function test_opd_dashboard_renders_dynamic_layanan_cards(): void
    {
        $this->actingAs($this->opdUser)
            ->get(route('opd.dashboard'))
            ->assertStatus(200)
            ->assertSee('Pembangunan Aplikasi Baru')
            ->assertSee('Pengembangan Aplikasi Eksisting')
            ->assertSee('Penonaktifan Aplikasi');
    }

    public function test_opd_can_submit_pengajuan_with_dynamic_layanan_and_workflow_fields(): void
    {
        Storage::fake('public');

        $layanan = Layanan::where('slug', 'pembangunan-aplikasi-baru')->first();
        $docProposal = MasterDokumenPengajuan::where('kode_dokumen', 'proposal_teknis')->first();
        $docSptjm = MasterDokumenPengajuan::where('kode_dokumen', 'sptjm')->first();

        // Get checklist field from first workflow
        $wfPengajuan = Workflow::where('slug', 'pengajuan-berkas')->first();
        $checklistField = $wfPengajuan->fields()->where('tipe', 'checklist')->first();

        $postData = [
            'layanan_id' => $layanan->id,
            'nama_aplikasi' => 'SI-Pariwisata Riau Gemilang',
            'deskripsi_kebutuhan' => 'Aplikasi informasi objek wisata, pemesanan tiket, dan data statistik turis asing daerah.',
            'nama_pemohon' => 'Budi Santoso, S.STP',
            'no_hp_pemohon' => '081299887766',
            'dokumen' => [
                $docProposal->id => UploadedFile::fake()->create('proposal.pdf', 100, 'application/pdf'),
                $docSptjm->id => UploadedFile::fake()->create('sptjm.pdf', 80, 'application/pdf'),
            ],
        ];

        if ($checklistField) {
            $postData['fields'][$checklistField->id] = '1';
        }

        $response = $this->actingAs($this->opdUser)
            ->post(route('opd.pengajuan.store'), $postData);

        $pengajuan = Pengajuan::where('nama_aplikasi', 'SI-Pariwisata Riau Gemilang')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals($layanan->id, $pengajuan->layanan_id);

        $response->assertRedirect(route('opd.pengajuan.show', $pengajuan));

        // Verify workflow pipeline initialization
        $workflowDataList = $pengajuan->workflowData()->orderBy('id')->get();
        $this->assertNotEmpty($workflowDataList);

        // First step (Pengajuan Berkas OPD) should be completed
        $firstStepData = $workflowDataList->first();
        $this->assertEquals('completed', $firstStepData->status);
        $this->assertEquals($this->opdUser->id, $firstStepData->completed_by);

        // Next step (Telaah Kelayakan) should be pending
        $secondStepData = $workflowDataList->skip(1)->first();
        $this->assertEquals('pending', $secondStepData->status);
    }

    public function test_aptika_review_advances_workflow_step(): void
    {
        Storage::fake('public');

        $layanan = Layanan::where('slug', 'pembangunan-aplikasi-baru')->first();

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-202610-999',
            'instansi_id' => $this->instansi->id,
            'layanan_id' => $layanan->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Sistem Rekrutmen CASN',
            'deskripsi_kebutuhan' => 'Sistem seleksi berkas calon aparatur sipil negara.',
            'nama_pemohon' => 'Ahmad Kasubag',
            'no_hp_pemohon' => '081234567890',
            'status' => 'submitted',
        ]);

        // Initialize workflow pipeline
        foreach ($layanan->workflows as $index => $lw) {
            $pengajuan->workflowData()->create([
                'layanan_workflow_id' => $lw->id,
                'status' => ($index === 0) ? 'completed' : 'pending',
                'completed_by' => ($index === 0) ? $this->opdUser->id : null,
                'completed_at' => ($index === 0) ? now() : null,
            ]);
        }

        // Review approval by APTIKA
        $response = $this->actingAs($this->adminAptika)
            ->patch(route('aptika.pengajuan.review', $pengajuan), [
                'status' => 'approved',
                'catatan_penilaian' => 'Usulan memenuhi syarat tata kelola SPBE Provinsi Riau.',
            ]);

        $this->assertEquals('approved', $pengajuan->fresh()->status);
        $response->assertRedirect(route('aptika.project.create', ['pengajuan_id' => $pengajuan->id]));

        // Verify Telaah step is completed
        $telaahStep = $pengajuan->workflowData()
            ->whereHas('layananWorkflow.workflow', function ($q) {
                $q->where('slug', 'telaah-kelayakan');
            })->first();

        $this->assertNotNull($telaahStep);
        $this->assertEquals('completed', $telaahStep->status);

        // Next step (Inisiasi & Pengerjaan Proyek) is in_progress
        $devStep = $pengajuan->workflowData()
            ->whereHas('layananWorkflow.workflow', function ($q) {
                $q->where('slug', 'in_development');
            })->first();

        $this->assertNotNull($devStep);
        $this->assertEquals('in_progress', $devStep->status);
    }

    public function test_opd_can_open_create_form_and_render_all_layanan_workflow_timelines_and_fields(): void
    {
        $response = $this->actingAs($this->opdUser)
            ->get(route('opd.pengajuan.create', ['layanan' => 'penonaktifan-aplikasi']))
            ->assertStatus(200)
            ->assertSee('Penonaktifan Aplikasi')
            ->assertSee('Decommissioning Server')
            ->assertSee('wf_timeline_')
            ->assertSee('step1_pane_');
    }

    public function test_opd_can_submit_custom_layanan_with_custom_workflow_fields(): void
    {
        Storage::fake('public');

        // Create a custom workflow and service
        $customWorkflow = Workflow::create([
            'nama' => 'Pemeriksaan Integritas Modul',
            'slug' => 'pemeriksaan-integritas-modul',
            'deskripsi' => 'Verifikasi keaslian dan integritas source code.',
            'ikon' => 'fingerprint',
        ]);

        $customFieldDoc = $customWorkflow->fields()->create([
            'label' => 'Pakta Integritas Vendor',
            'tipe' => 'file_upload',
            'master_dokumen_id' => null,
            'wajib' => true,
            'urutan' => 1,
        ]);

        $customFieldCheck = $customWorkflow->fields()->create([
            'label' => 'Telah lolos review kode internal',
            'tipe' => 'checklist',
            'wajib' => true,
            'urutan' => 2,
        ]);

        $customLayanan = Layanan::create([
            'nama' => 'Layanan Audit Source Code',
            'slug' => 'layanan-audit-source-code',
            'deskripsi' => 'Pengujian keamanan dan audit kode.',
            'ikon' => 'code',
            'warna' => '#6366F1',
            'is_active' => true,
            'urutan' => 10,
        ]);

        $customLayanan->workflows()->create([
            'workflow_id' => $customWorkflow->id,
            'urutan' => 1,
            'is_active' => true,
        ]);

        $postData = [
            'layanan_id' => $customLayanan->id,
            'nama_aplikasi' => 'Aplikasi Audit Custom',
            'deskripsi_kebutuhan' => 'Deskripsi permohonan audit source code aplikasi dinas.',
            'nama_pemohon' => 'Hendra Pemohon',
            'no_hp_pemohon' => '081234567891',
            'workflow_files' => [
                $customFieldDoc->id => UploadedFile::fake()->create('pakta.pdf', 100, 'application/pdf'),
            ],
            'fields' => [
                $customFieldCheck->id => '1',
            ],
        ];

        $response = $this->actingAs($this->opdUser)
            ->post(route('opd.pengajuan.store'), $postData);

        $pengajuan = Pengajuan::where('nama_aplikasi', 'Aplikasi Audit Custom')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals($customLayanan->id, $pengajuan->layanan_id);

        $response->assertRedirect(route('opd.pengajuan.show', $pengajuan));

        // Verify workflow data and field data are saved
        $pwData = $pengajuan->workflowData()->first();
        $this->assertNotNull($pwData);
        $this->assertEquals('completed', $pwData->status);

        $this->assertDatabaseHas('pengajuan_field_data', [
            'pengajuan_workflow_data_id' => $pwData->id,
            'workflow_field_id' => $customFieldCheck->id,
            'value' => '1',
        ]);

        $this->assertDatabaseHas('pengajuan_field_data', [
            'pengajuan_workflow_data_id' => $pwData->id,
            'workflow_field_id' => $customFieldDoc->id,
        ]);
    }
}
