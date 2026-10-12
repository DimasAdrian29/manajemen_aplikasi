<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\PesertaRapat;
use App\Models\Project;
use App\Models\RapatProject;
use App\Models\TimProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PortalTimPengembangTest extends TestCase
{
    use RefreshDatabase;

    protected User $ketuaTim;

    protected User $anggotaTim;

    protected User $userLuar;

    protected Project $project;

    protected Instansi $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Setup Spatie Roles
        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'admin_aptika']);
        Role::firstOrCreate(['name' => 'ketua_tim']);
        Role::firstOrCreate(['name' => 'anggota_tim']);
        Role::firstOrCreate(['name' => 'opd']);

        $this->instansi = Instansi::create([
            'nama_instansi' => 'Dinas Kesehatan Provinsi Riau',
            'singkatan' => 'Dinkes',
            'alamat' => 'Jl. Cut Nyak Dien, Pekanbaru',
        ]);

        // Ketua Tim User
        $this->ketuaTim = User::factory()->create([
            'name' => 'Ahmad Ketua',
            'username' => 'ahmad_ketua',
            'email' => 'ahmad@test.com',
        ]);
        $this->ketuaTim->assignRole('ketua_tim');

        // Anggota Tim User
        $this->anggotaTim = User::factory()->create([
            'name' => 'Bambang Anggota',
            'username' => 'bambang_anggota',
            'email' => 'bambang@test.com',
        ]);
        $this->anggotaTim->assignRole('anggota_tim');

        // User dari luar tim
        $this->userLuar = User::factory()->create([
            'name' => 'Cahyo User Luar',
            'username' => 'cahyo_luar',
            'email' => 'cahyo@test.com',
        ]);
        $this->userLuar->assignRole('anggota_tim');

        // Setup Project & Aplikasi
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->instansi->id,
            'nama_aplikasi' => 'Aplikasi Uji Tim',
            'deskripsi' => 'Pengujian portal tim pengembang',
            'status' => 'aktif',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-TEST-TIM-01',
            'aplikasi_id' => $aplikasi->id,
            'instansi_id' => $this->instansi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi Uji Tim',
            'deskripsi_kebutuhan' => 'Pengujian portal tim pengembang',
            'nama_pemohon' => 'Pemohon Uji',
            'no_hp_pemohon' => '08123456789',
            'status' => 'approved',
        ]);

        $this->project = Project::create([
            'aplikasi_id' => $aplikasi->id,
            'pengajuan_id' => $pengajuan->id,
            'nama_project' => 'Pengembangan Sistem Kesehatan Digital',
            'tanggal_mulai' => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->addDays(60)->toDateString(),
            'status' => 'in_development',
            'progres_total' => 25,
        ]);

        // Assign Ahmad sebagai Ketua Tim di Proyek ini
        TimProject::create([
            'project_id' => $this->project->id,
            'user_id' => $this->ketuaTim->id,
            'peran' => 'ketua_tim',
            'tugas' => 'Koordinator Teknis & Fullstack Lead',
        ]);

        // Assign Bambang sebagai Anggota di Proyek ini
        TimProject::create([
            'project_id' => $this->project->id,
            'user_id' => $this->anggotaTim->id,
            'peran' => 'anggota',
            'tugas' => 'Frontend Developer',
        ]);
    }

    public function test_user_tim_dapat_mengakses_dashboard_dan_melihat_daftar_proyek(): void
    {
        $responseKetua = $this->actingAs($this->ketuaTim)->get(route('tim.dashboard'));
        $responseKetua->assertOk();
        $responseKetua->assertSee('Portal Tim Pengembang');
        $responseKetua->assertSee('Pengembangan Sistem Kesehatan Digital');

        $responseIndex = $this->actingAs($this->ketuaTim)->get(route('tim.project.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Pengembangan Sistem Kesehatan Digital');
    }

    public function test_ketua_tim_dapat_melihat_detail_proyek_dan_banner_ketua(): void
    {
        $response = $this->actingAs($this->ketuaTim)->get(route('tim.project.show', $this->project));

        $response->assertOk();
        $response->assertSee('Anda adalah Ketua Tim (Lead Developer) pada Proyek Ini');
        $response->assertSee('Update Capaian Sprint');
        $response->assertSee('Tambah Dokumen');
    }

    public function test_anggota_tim_melihat_proyek_dalam_mode_pemantauan(): void
    {
        $response = $this->actingAs($this->anggotaTim)->get(route('tim.project.show', $this->project));

        $response->assertOk();
        $response->assertSee('Anda Bertugas sebagai Anggota Tim pada Proyek Ini');
        $response->assertDontSee('Update Capaian Sprint');
        $response->assertDontSee('openModalUpdateProgres()');
    }

    public function test_ketua_tim_dapat_mengupdate_progres_proyek(): void
    {
        $response = $this->actingAs($this->ketuaTim)
            ->patch(route('tim.project.progres', $this->project), [
                'progres_total' => 60,
                'status' => 'in_development',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('project', [
            'id' => $this->project->id,
            'progres_total' => 60,
            'status' => 'in_development',
        ]);
    }

    public function test_anggota_tim_ditolak_403_saat_mencoba_update_progres(): void
    {
        $response = $this->actingAs($this->anggotaTim)
            ->patch(route('tim.project.progres', $this->project), [
                'progres_total' => 80,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('project', [
            'id' => $this->project->id,
            'progres_total' => 25, // Nilai lama tidak berubah
        ]);
    }

    public function test_ketua_tim_dapat_menambahkan_tahapan_sprint(): void
    {
        $response = $this->actingAs($this->ketuaTim)
            ->post(route('tim.project.tahapan', $this->project), [
                'nama_tahapan' => 'Sprint 1 - Database Design',
                'persentase' => 50,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tahapan_project', [
            'project_id' => $this->project->id,
            'nama_tahapan' => 'Sprint 1 - Database Design',
            'persentase' => 50,
        ]);
    }

    public function test_ketua_tim_dapat_menyimpan_dokumen_dengan_link_google_docs(): void
    {
        $response = $this->actingAs($this->ketuaTim)
            ->post(route('tim.project.dokumen', $this->project), [
                'jenis_dokumen' => 'srs',
                'tipe_sumber' => 'link',
                'link_dokumen' => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit',
                'catatan' => 'Dokumen live SRS di Google Docs terintegrasi',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dokumen_perancangan', [
            'project_id' => $this->project->id,
            'jenis_dokumen' => 'srs',
            'file_dokumen' => 'https://docs.google.com/document/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit',
        ]);
    }

    public function test_ketua_tim_dapat_mengajukan_penundaan_proyek(): void
    {
        $fileDummy = UploadedFile::fake()->create('surat_kendala.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->ketuaTim)
            ->post(route('tim.project.penundaan', $this->project), [
                'alasan' => 'Menunggu server dan firewall dari Dinas Kominfo siap dipasang',
                'file_surat' => $fileDummy,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penundaan', [
            'project_id' => $this->project->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('project', [
            'id' => $this->project->id,
            'status' => 'delayed',
        ]);
    }

    public function test_user_dari_luar_tim_ditolak_403_melihat_proyek(): void
    {
        $response = $this->actingAs($this->userLuar)->get(route('tim.project.show', $this->project));
        $response->assertForbidden();
    }

    public function test_user_dapat_melihat_agenda_rapat_dan_mengonfirmasi_presensi(): void
    {
        $rapat = RapatProject::create([
            'project_id' => $this->project->id,
            'dibuat_oleh' => $this->ketuaTim->id,
            'nama_rapat' => 'Koordinasi Arsitektur Database & Keamanan',
            'waktu_rapat' => now()->addDays(2),
            'tempat_rapat' => 'Ruang Rapat APTIKA Lt. 2',
            'deskripsi' => 'Pembahasan skema integrasi API OPD',
            'status' => 'dijadwalkan',
        ]);

        PesertaRapat::create([
            'rapat_project_id' => $rapat->id,
            'user_id' => $this->ketuaTim->id,
            'status_kehadiran' => 'menunggu',
        ]);

        // Akses daftar rapat
        $responseIndex = $this->actingAs($this->ketuaTim)->get(route('tim.rapat.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Koordinasi Arsitektur Database & Keamanan');

        // Akses detail rapat
        $responseShow = $this->actingAs($this->ketuaTim)->get(route('tim.rapat.show', $rapat));
        $responseShow->assertOk();
        $responseShow->assertSee('Konfirmasi Kehadiran Presensi');

        // Submit presensi
        $responsePresensi = $this->actingAs($this->ketuaTim)
            ->post(route('tim.rapat.presensi', $rapat), [
                'status_kehadiran' => 'hadir',
            ]);

        $responsePresensi->assertRedirect();
        $responsePresensi->assertSessionHas('success');

        $this->assertDatabaseHas('peserta_rapat', [
            'rapat_project_id' => $rapat->id,
            'user_id' => $this->ketuaTim->id,
            'status_kehadiran' => 'hadir',
        ]);
    }

    public function test_user_tanpa_role_tim_ditolak_akses_portal_tim(): void
    {
        $opdUser = User::factory()->create();
        $opdUser->assignRole('opd');

        $response = $this->actingAs($opdUser)->get(route('tim.dashboard'));
        $response->assertForbidden();
    }
}
