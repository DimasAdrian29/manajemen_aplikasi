<?php

namespace Tests\Feature;

use App\Models\DokumenPengajuan;
use App\Models\Instansi;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\User;
use App\Models\Workflow;
use App\Models\WorkflowField;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AptikaInlineMasterDokumenTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected Instansi $instansi;

    protected Workflow $workflow;

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

        $this->adminAptika = User::factory()->create([
            'instansi_id' => $this->instansi->id,
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $this->workflow = Workflow::create([
            'nama' => 'Pengujian Tahap 1',
            'slug' => 'pengujian-tahap-1',
            'deskripsi' => 'Pengujian sistem alur pertama.',
            'ikon' => 'fact_check',
        ]);
    }

    public function test_admin_aptika_can_create_master_dokumen_via_quick_endpoint(): void
    {
        $response = $this->actingAs($this->adminAptika)
            ->postJson(route('aptika.master-dokumen.store'), [
                'nama_dokumen' => 'Surat Rekomendasi CSIRT Khusus',
                'deskripsi' => 'Dokumen hasil asesmen kerentanan keamanan.',
                'format_diizinkan' => 'pdf',
                'max_size_kb' => 10240,
                'wajib' => true,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama_dokumen' => 'Surat Rekomendasi CSIRT Khusus',
                    'kode_dokumen' => 'surat_rekomendasi_csirt_khusus',
                    'max_size_kb' => 10240,
                ],
            ]);

        $this->assertDatabaseHas('master_dokumen_pengajuan', [
            'nama_dokumen' => 'Surat Rekomendasi CSIRT Khusus',
            'kode_dokumen' => 'surat_rekomendasi_csirt_khusus',
        ]);
    }

    public function test_admin_aptika_can_view_and_update_master_dokumen_via_quick_endpoint(): void
    {
        $doc = MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Berita Acara Uji Beban',
            'kode_dokumen' => 'ba_uji_beban',
            'deskripsi' => 'Deskripsi lama.',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 5120,
            'wajib' => true,
            'berlaku_untuk' => 'semua',
            'is_active' => true,
            'urutan' => 1,
        ]);

        // 1. View / show
        $this->actingAs($this->adminAptika)
            ->getJson(route('aptika.master-dokumen.show', $doc))
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $doc->id,
                    'nama_dokumen' => 'Berita Acara Uji Beban',
                ],
            ]);

        // 2. Update
        $response = $this->actingAs($this->adminAptika)
            ->putJson(route('aptika.master-dokumen.update', $doc), [
                'nama_dokumen' => 'Berita Acara Uji Beban & Stres',
                'deskripsi' => 'Deskripsi yang diperbarui.',
                'max_size_kb' => 10240,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama_dokumen' => 'Berita Acara Uji Beban & Stres',
                ],
            ]);

        $this->assertDatabaseHas('master_dokumen_pengajuan', [
            'id' => $doc->id,
            'nama_dokumen' => 'Berita Acara Uji Beban & Stres',
            'deskripsi' => 'Deskripsi yang diperbarui.',
            'max_size_kb' => 10240,
        ]);
    }

    public function test_safety_guard_prevents_deleting_master_dokumen_with_existing_pengajuan_files(): void
    {
        $doc = MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Proposal KAK Penting',
            'kode_dokumen' => 'proposal_kak_penting',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 10240,
            'wajib' => true,
            'berlaku_untuk' => 'semua',
            'is_active' => true,
            'urutan' => 1,
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-TEST-001',
            'instansi_id' => $this->instansi->id,
            'user_id' => $this->adminAptika->id,
            'nama_aplikasi' => 'Aplikasi Pengujian',
            'deskripsi_kebutuhan' => 'Kebutuhan uji sistem',
            'nama_pemohon' => 'Ahmad Kasubag',
            'no_hp_pemohon' => '081234567890',
            'jenis_pengajuan' => 'new_app',
            'status' => 'draft',
        ]);

        DokumenPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'master_dokumen_id' => $doc->id,
            'nama_file_asli' => 'kak.pdf',
            'file_path' => 'dokumen/kak.pdf',
            'file_size' => 1024,
            'status_verifikasi' => 'pending',
        ]);

        // Attempt delete
        $response = $this->actingAs($this->adminAptika)
            ->deleteJson(route('aptika.master-dokumen.destroy', $doc));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('master_dokumen_pengajuan', ['id' => $doc->id]);
    }

    public function test_safety_guard_prevents_deleting_master_dokumen_used_in_workflow_fields(): void
    {
        $doc = MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Dokumen Desain Sistem',
            'kode_dokumen' => 'dok_desain_sistem',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 10240,
            'wajib' => true,
            'berlaku_untuk' => 'semua',
            'is_active' => true,
            'urutan' => 1,
        ]);

        WorkflowField::create([
            'workflow_id' => $this->workflow->id,
            'label' => 'Upload Dokumen Desain',
            'tipe' => 'file_upload',
            'master_dokumen_id' => $doc->id,
            'wajib' => true,
            'urutan' => 1,
        ]);

        // Attempt delete
        $response = $this->actingAs($this->adminAptika)
            ->deleteJson(route('aptika.master-dokumen.destroy', $doc));

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('master_dokumen_pengajuan', ['id' => $doc->id]);
    }

    public function test_admin_aptika_can_delete_unused_master_dokumen(): void
    {
        $doc = MasterDokumenPengajuan::create([
            'nama_dokumen' => 'Dokumen Sementara Tidak Terpakai',
            'kode_dokumen' => 'dok_sementara',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 10240,
            'wajib' => true,
            'berlaku_untuk' => 'semua',
            'is_active' => true,
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->adminAptika)
            ->deleteJson(route('aptika.master-dokumen.destroy', $doc));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('master_dokumen_pengajuan', ['id' => $doc->id]);
    }

    public function test_admin_aptika_can_attach_new_quick_dokumen_to_workflow_field(): void
    {
        // 1. Create quick master doc
        $docResponse = $this->actingAs($this->adminAptika)
            ->postJson(route('aptika.master-dokumen.store'), [
                'nama_dokumen' => 'Sertifikat Laik Operasi SPBE',
                'max_size_kb' => 10240,
            ]);

        $docId = $docResponse->json('data.id');

        // 2. Attach to workflow field
        $fieldResponse = $this->actingAs($this->adminAptika)
            ->post(route('aptika.workflow.field.store', $this->workflow), [
                'label' => 'Unggah Sertifikat Laik Operasi',
                'tipe' => 'file_upload',
                'master_dokumen_id' => $docId,
                'wajib' => 1,
                'keterangan' => 'Sertifikat resmi dari dinas terkait.',
            ]);

        $fieldResponse->assertSessionHas('success');

        $this->assertDatabaseHas('workflow_field', [
            'workflow_id' => $this->workflow->id,
            'label' => 'Unggah Sertifikat Laik Operasi',
            'tipe' => 'file_upload',
            'master_dokumen_id' => $docId,
            'wajib' => 1,
        ]);
    }
}
