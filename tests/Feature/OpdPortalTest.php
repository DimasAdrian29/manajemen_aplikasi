<?php

namespace Tests\Feature;

use App\Models\Instansi;
use App\Models\MasterDokumenPengajuan;
use App\Models\Pengajuan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OpdPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $opdUser;

    protected Instansi $dinkes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->opdUser = User::where('username', 'opd_dinkes')->first();
        $this->dinkes = Instansi::where('nama_instansi', 'like', '%Kesehatan%')->first();
    }

    public function test_opd_can_access_dashboard_and_see_own_instansi_stats(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('opd.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Layanan OPD');
        $response->assertSee('Dinas Kesehatan');
    }

    public function test_opd_can_view_create_form_with_dynamic_documents(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('opd.pengajuan.create'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pengajuan Layanan Aplikasi');
        $response->assertSee('Surat Pengantar Kepala Dinas');
        $response->assertSee('Proposal Teknis / KAK');
        $response->assertSee('Surat Pernyataan Tanggung Jawab Mutlak (SPTJM)');
    }

    public function test_opd_can_submit_application_with_isolated_storage_documents(): void
    {
        Storage::fake('public');

        $suratDoc = MasterDokumenPengajuan::where('kode_dokumen', 'surat_pengantar')->first();
        $proposalDoc = MasterDokumenPengajuan::where('kode_dokumen', 'proposal_teknis')->first();
        $sptjmDoc = MasterDokumenPengajuan::where('kode_dokumen', 'sptjm')->first();

        $suratFile = UploadedFile::fake()->create('surat_resmi_dinkes.pdf', 300, 'application/pdf');
        $proposalFile = UploadedFile::fake()->create('kak_proposal_telemedicine.pdf', 1500, 'application/pdf');
        $sptjmFile = UploadedFile::fake()->create('sptjm_komitmen.pdf', 200, 'application/pdf');

        $payload = [
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Telemedicine Siaga Riau Sehat',
            'deskripsi_kebutuhan' => 'Sistem konsultasi online dokter spesialis ke seluruh puskesmas terpencil di kepulauan dan pesisir Riau.',
            'nama_pemohon' => 'Dr. Suhendri, M.Kes',
            'no_hp_pemohon' => '081298765432',
            'dokumen' => [
                $suratDoc->id => $suratFile,
                $proposalDoc->id => $proposalFile,
                $sptjmDoc->id => $sptjmFile,
            ],
        ];

        $response = $this->actingAs($this->opdUser)->post(route('opd.pengajuan.store'), $payload);

        $pengajuan = Pengajuan::where('nama_aplikasi', 'Telemedicine Siaga Riau Sehat')->first();
        $this->assertNotNull($pengajuan);
        $this->assertEquals($this->dinkes->id, $pengajuan->instansi_id);
        $this->assertEquals('submitted', $pengajuan->status);

        $response->assertRedirect(route('opd.pengajuan.show', $pengajuan));

        // Verifikasi dokumen di database
        $this->assertCount(3, $pengajuan->dokumen);
        $this->assertDatabaseHas('dokumen_pengajuan', [
            'pengajuan_id' => $pengajuan->id,
            'master_dokumen_id' => $suratDoc->id,
            'nama_file_asli' => 'surat_resmi_dinkes.pdf',
        ]);

        // Verifikasi isolasi direktori storage:
        // storage/app/public/pengajuan/{kode_pengajuan}/{kode_dokumen}/{timestamp}_{clean_filename}.pdf
        $dokumenSurat = $pengajuan->dokumen()->where('master_dokumen_id', $suratDoc->id)->first();
        $dokumenProposal = $pengajuan->dokumen()->where('master_dokumen_id', $proposalDoc->id)->first();

        $this->assertStringStartsWith("pengajuan/{$pengajuan->kode_pengajuan}/surat_pengantar/", $dokumenSurat->file_path);
        $this->assertStringStartsWith("pengajuan/{$pengajuan->kode_pengajuan}/proposal_teknis/", $dokumenProposal->file_path);

        Storage::disk('public')->assertExists($dokumenSurat->file_path);
        Storage::disk('public')->assertExists($dokumenProposal->file_path);
    }

    public function test_opd_cannot_view_or_delete_other_instansi_pengajuan(): void
    {
        $diskominfo = Instansi::where('nama_instansi', 'like', '%Komunikasi%')->first();

        // Buat pengajuan milik Diskominfo
        $foreignPengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-9999',
            'instansi_id' => $diskominfo->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Sistem Internal Diskominfo',
            'deskripsi_kebutuhan' => 'Kebutuhan khusus diskominfo',
            'nama_pemohon' => 'Pejabat Diskominfo',
            'no_hp_pemohon' => '081111111111',
            'status' => 'submitted',
        ]);

        // OPD Dinkes mencoba melihat detail
        $responseShow = $this->actingAs($this->opdUser)->get(route('opd.pengajuan.show', $foreignPengajuan));
        $responseShow->assertStatus(403);

        // OPD Dinkes mencoba menghapus
        $responseDestroy = $this->actingAs($this->opdUser)->delete(route('opd.pengajuan.destroy', $foreignPengajuan));
        $responseDestroy->assertStatus(403);
    }

    public function test_opd_can_view_own_aplikasi_catalog_only(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('opd.aplikasi.index'));

        $response->assertStatus(200);
        // Dinkes memiliki e-Puskesmas Riau
        $response->assertSee('e-Puskesmas Riau');
        // Dinkes tidak boleh melihat SIMPEG Pemprov Riau milik Diskominfo
        $response->assertDontSee('SIMPEG Pemprov Riau');
    }
}
