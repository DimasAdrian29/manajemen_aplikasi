<?php

namespace Tests\Feature;

use App\Models\MasterDokumenPengajuan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterDokumenTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->superadmin = User::where('username', 'kaido')->first();
    }

    public function test_superadmin_can_view_master_dokumen_index(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.master-dokumen.index'));

        $response->assertStatus(200);
        $response->assertSee('Master Dokumen Pengajuan');
        $response->assertSee('Surat Pengantar Kepala Dinas');
        $response->assertSee('Proposal Teknis / KAK');
    }

    public function test_superadmin_can_create_new_master_dokumen(): void
    {
        Storage::fake('public');

        $templateFile = UploadedFile::fake()->create('format_rekomendasi.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->superadmin)->post(route('superadmin.master-dokumen.store'), [
            'nama_dokumen' => 'Surat Rekomendasi SPBE Dewan Smart Province',
            'kode_dokumen' => 'rekomendasi_spbe',
            'deskripsi' => 'Surat rekomendasi keselarasan arsitektur SPBE daerah.',
            'wajib' => '1',
            'berlaku_untuk' => 'new_app',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 5120,
            'urutan' => 6,
            'is_active' => '1',
            'file_template' => $templateFile,
        ]);

        $response->assertRedirect(route('superadmin.master-dokumen.index'));
        $this->assertDatabaseHas('master_dokumen_pengajuan', [
            'kode_dokumen' => 'rekomendasi_spbe',
            'wajib' => true,
            'berlaku_untuk' => 'new_app',
        ]);

        $created = MasterDokumenPengajuan::where('kode_dokumen', 'rekomendasi_spbe')->first();
        $this->assertNotNull($created->file_template);
        Storage::disk('public')->assertExists($created->file_template);
    }

    public function test_superadmin_can_toggle_dokumen_active_status(): void
    {
        $doc = MasterDokumenPengajuan::first();
        $initialStatus = $doc->is_active;

        $response = $this->actingAs($this->superadmin)->patch(route('superadmin.master-dokumen.toggle', $doc));

        $response->assertRedirect();
        $doc->refresh();
        $this->assertEquals(! $initialStatus, $doc->is_active);
    }

    public function test_superadmin_can_update_master_dokumen(): void
    {
        $doc = MasterDokumenPengajuan::where('kode_dokumen', 'surat_pengantar')->first();

        $response = $this->actingAs($this->superadmin)->put(route('superadmin.master-dokumen.update', $doc), [
            'nama_dokumen' => 'Surat Pengantar Kepala Dinas (Revisi Regulasi)',
            'kode_dokumen' => 'surat_pengantar',
            'deskripsi' => 'Wajib TTE Elektronik Balai Sertifikasi Elektronik (BSrE).',
            'wajib' => '1',
            'berlaku_untuk' => 'semua',
            'format_diizinkan' => 'pdf',
            'max_size_kb' => 10240,
            'urutan' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('superadmin.master-dokumen.index'));
        $doc->refresh();
        $this->assertEquals('Surat Pengantar Kepala Dinas (Revisi Regulasi)', $doc->nama_dokumen);
        $this->assertEquals('Wajib TTE Elektronik Balai Sertifikasi Elektronik (BSrE).', $doc->deskripsi);
    }

    public function test_non_superadmin_cannot_access_master_dokumen(): void
    {
        $opdUser = User::where('username', 'opd_dinkes')->first();

        $response = $this->actingAs($opdUser)->get(route('superadmin.master-dokumen.index'));
        $response->assertStatus(403);
    }
}
