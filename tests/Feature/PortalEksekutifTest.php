<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\Penundaan;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PortalEksekutifTest extends TestCase
{
    use RefreshDatabase;

    protected User $kadis;

    protected User $kabid;

    protected User $opdUser;

    protected Instansi $instansi;

    protected Project $project;

    protected Penundaan $penundaan;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Roles
        Role::firstOrCreate(['name' => 'kepala_dinas']);
        Role::firstOrCreate(['name' => 'kepala_bidang']);
        Role::firstOrCreate(['name' => 'opd']);
        Role::firstOrCreate(['name' => 'ketua_tim']);

        $this->instansi = Instansi::create([
            'nama_instansi' => 'Dinas Komunikasi, Informatika dan Statistik',
            'singkatan' => 'Diskominfotik',
            'alamat' => 'Pekanbaru',
        ]);

        // Kepala Dinas
        $this->kadis = User::factory()->create([
            'name' => 'Kepala Dinas Kominfo',
            'username' => 'kadis_test',
            'email' => 'kadis@test.com',
            'jabatan' => 'Kepala Dinas Kominfo Riau',
        ]);
        $this->kadis->assignRole('kepala_dinas');

        // Kepala Bidang
        $this->kabid = User::factory()->create([
            'name' => 'Kepala Bidang APTIKA',
            'username' => 'kabid_test',
            'email' => 'kabid@test.com',
            'jabatan' => 'Kepala Bidang Aptika',
        ]);
        $this->kabid->assignRole('kepala_bidang');

        // OPD User (Bukan Eksekutif)
        $this->opdUser = User::factory()->create([
            'name' => 'Staf OPD Biasa',
            'username' => 'opd_biasa',
            'email' => 'opd@test.com',
        ]);
        $this->opdUser->assignRole('opd');

        // Lead Developer
        $lead = User::factory()->create([
            'name' => 'Lead Dev',
            'username' => 'lead_dev',
            'email' => 'lead@test.com',
        ]);
        $lead->assignRole('ketua_tim');

        // Aplikasi & Pengajuan
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->instansi->id,
            'nama_aplikasi' => 'Portal Layanan Publik Riau',
            'deskripsi' => 'Aplikasi portal pengujian eksekutif',
            'status' => 'aktif',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-EKS-01',
            'aplikasi_id' => $aplikasi->id,
            'instansi_id' => $this->instansi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Portal Layanan Publik Riau',
            'deskripsi_kebutuhan' => 'Kebutuhan pengujian portal eksekutif',
            'nama_pemohon' => 'Pemohon Eksekutif',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        $this->project = Project::create([
            'aplikasi_id' => $aplikasi->id,
            'pengajuan_id' => $pengajuan->id,
            'nama_project' => 'Pengembangan Portal Terpadu Riau',
            'tanggal_mulai' => now()->subDays(20)->toDateString(),
            'tanggal_selesai' => now()->addDays(40)->toDateString(),
            'status' => 'delayed',
            'progres_total' => 45,
        ]);

        // Permohonan Penundaan
        $this->penundaan = Penundaan::create([
            'project_id' => $this->project->id,
            'diajukan_oleh' => $lead->id,
            'alasan' => 'Menunggu penyediaan domain dan SSL dari Tim Infrastruktur',
            'status' => 'diajukan',
        ]);
    }

    public function test_kepala_dinas_dapat_mengakses_dashboard_dan_melihat_metrik_makro(): void
    {
        $response = $this->actingAs($this->kadis)->get(route('eksekutif.dashboard'));

        $response->assertOk();
        $response->assertSee('Portal Pimpinan Eksekutif');
        $response->assertSee('Selamat Datang, Kepala Dinas Kominfo');
        $response->assertSee('Level Pimpinan Tertinggi Daerah');
        $response->assertSee('Pengembangan Portal Terpadu Riau');
    }

    public function test_kepala_bidang_dapat_mengakses_dashboard_dan_melihat_antrean_penundaan(): void
    {
        $response = $this->actingAs($this->kabid)->get(route('eksekutif.dashboard'));

        $response->assertOk();
        $response->assertSee('Selamat Datang, Kepala Bidang APTIKA');
        $response->assertSee('Level Pimpinan Bidang Teknis');
        $response->assertSee('Menunggu penyediaan domain dan SSL');
    }

    public function test_role_selain_eksekutif_ditolak_403_dari_portal_eksekutif(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('eksekutif.dashboard'));
        $response->assertForbidden();

        $responseProject = $this->actingAs($this->opdUser)->get(route('eksekutif.project.index'));
        $responseProject->assertForbidden();
    }

    public function test_eksekutif_dapat_melihat_portofolio_proyek_dan_detail_lembar_kerja(): void
    {
        $responseIndex = $this->actingAs($this->kabid)->get(route('eksekutif.project.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Pengembangan Portal Terpadu Riau');

        $responseShow = $this->actingAs($this->kabid)->get(route('eksekutif.project.show', $this->project));
        $responseShow->assertOk();
        $responseShow->assertSee('Detail Lembar Kerja Eksekutif Proyek');
        $responseShow->assertSee('Pengembangan Portal Terpadu Riau');
        $responseShow->assertSee('45%');
    }

    public function test_eksekutif_dapat_menyetujui_permohonan_penundaan_proyek(): void
    {
        $responseIndex = $this->actingAs($this->kabid)->get(route('eksekutif.penundaan.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Antrean Permohonan Penundaan Proyek');
        $responseIndex->assertSee('Menunggu penyediaan domain dan SSL');

        $targetBaru = now()->addDays(90)->toDateString();

        $responseApprove = $this->actingAs($this->kabid)
            ->post(route('eksekutif.penundaan.approve', $this->penundaan), [
                'tanggal_selesai_baru' => $targetBaru,
            ]);

        $responseApprove->assertRedirect();
        $responseApprove->assertSessionHas('success');

        $this->assertDatabaseHas('penundaan', [
            'id' => $this->penundaan->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('project', [
            'id' => $this->project->id,
            'status' => 'delayed',
            'tanggal_selesai' => $targetBaru,
        ]);
    }

    public function test_eksekutif_dapat_menolak_permohonan_penundaan_dan_mengembalikan_proyek_ke_in_development(): void
    {
        $responseReject = $this->actingAs($this->kabid)
            ->post(route('eksekutif.penundaan.reject', $this->penundaan));

        $responseReject->assertRedirect();
        $responseReject->assertSessionHas('success');

        $this->assertDatabaseHas('penundaan', [
            'id' => $this->penundaan->id,
            'status' => 'ditolak',
        ]);

        // Status proyek harus kembali ke in_development
        $this->assertDatabaseHas('project', [
            'id' => $this->project->id,
            'status' => 'in_development',
        ]);
    }

    public function test_eksekutif_dapat_melihat_katalog_aplikasi_dan_laporan_eksekutif(): void
    {
        $responseAplikasi = $this->actingAs($this->kadis)->get(route('eksekutif.aplikasi.index'));
        $responseAplikasi->assertOk();
        $responseAplikasi->assertSee('Portal Layanan Publik Riau');

        $responseLaporan = $this->actingAs($this->kadis)->get(route('eksekutif.laporan.index'));
        $responseLaporan->assertOk();
        $responseLaporan->assertSee('Laporan Ringkasan Eksekutif');
        $responseLaporan->assertSee('Dinas Komunikasi, Informatika dan Statistik');
        $responseLaporan->assertSee('Pengembangan Portal Terpadu Riau');
    }
}
