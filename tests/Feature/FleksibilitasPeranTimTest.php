<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\TimProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FleksibilitasPeranTimTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $adminAptika;

    protected User $devAhmad;

    protected User $devBambang;

    protected Instansi $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles
        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'admin_aptika']);
        Role::firstOrCreate(['name' => 'anggota_tim']);
        Role::firstOrCreate(['name' => 'ketua_tim']);

        $this->instansi = Instansi::create([
            'nama_instansi' => 'Dinas Komunikasi dan Informatika',
            'singkatan' => 'Diskominfo',
            'alamat' => 'Pekanbaru',
        ]);

        $this->superadmin = User::factory()->create([
            'username' => 'superadmin_test',
            'email' => 'superadmin@test.com',
        ]);
        $this->superadmin->assignRole('superadmin');

        $this->adminAptika = User::factory()->create([
            'username' => 'aptika_test',
            'email' => 'aptika@test.com',
        ]);
        $this->adminAptika->assignRole('admin_aptika');

        $this->devAhmad = User::factory()->create([
            'name' => 'Ahmad Fauzi',
            'username' => 'ahmad_dev',
            'email' => 'ahmad@test.com',
            'jabatan' => 'Fullstack Developer',
        ]);
        $this->devAhmad->assignRole('anggota_tim');

        $this->devBambang = User::factory()->create([
            'name' => 'Bambang Sudiro',
            'username' => 'bambang_dev',
            'email' => 'bambang@test.com',
            'jabatan' => 'Backend Developer',
        ]);
        $this->devBambang->assignRole('anggota_tim');
    }

    public function test_superadmin_dapat_menambahkan_personil_tanpa_dropdown_peran_statis(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.anggota-tim.store'), [
            'name' => 'Rian Pratama',
            'username' => 'rianpratama',
            'email' => 'rian@test.com',
            'no_hp' => '081234567890',
            'jabatan' => 'Frontend Developer',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('superadmin.anggota-tim.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'rianpratama',
            'email' => 'rian@test.com',
            'jabatan' => 'Frontend Developer',
        ]);

        $user = User::where('username', 'rianpratama')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('anggota_tim'));
    }

    public function test_penugasan_peran_silang_pada_dua_proyek_berbeda(): void
    {
        // 1. Inisiasi Proyek A: Ahmad Ketua, Bambang Anggota
        $pengajuanA = Pengajuan::create([
            'kode_pengajuan' => 'REG-TEST-001',
            'instansi_id' => $this->instansi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi A',
            'deskripsi_kebutuhan' => 'Kebutuhan A',
            'nama_pemohon' => 'Pemohon A',
            'no_hp_pemohon' => '081200000001',
            'status' => 'approved',
        ]);

        $responseA = $this->actingAs($this->adminAptika)->post(route('aptika.project.store'), [
            'pengajuan_id' => $pengajuanA->id,
            'nama_project' => 'Proyek Alpha',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'ketua_tim_id' => $this->devAhmad->id,
            'anggota_tim_ids' => [$this->devBambang->id],
        ]);

        $projectA = Project::where('pengajuan_id', $pengajuanA->id)->first();
        $this->assertNotNull($projectA);
        $responseA->assertRedirect(route('aptika.project.show', $projectA));

        // Verifikasi peran di Proyek A
        $this->assertDatabaseHas('tim_project', [
            'project_id' => $projectA->id,
            'user_id' => $this->devAhmad->id,
            'peran' => 'ketua_tim',
        ]);
        $this->assertDatabaseHas('tim_project', [
            'project_id' => $projectA->id,
            'user_id' => $this->devBambang->id,
            'peran' => 'anggota',
        ]);

        // 2. Inisiasi Proyek B: Bambang Ketua, Ahmad Anggota
        $pengajuanB = Pengajuan::create([
            'kode_pengajuan' => 'REG-TEST-002',
            'instansi_id' => $this->instansi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi B',
            'deskripsi_kebutuhan' => 'Kebutuhan B',
            'nama_pemohon' => 'Pemohon B',
            'no_hp_pemohon' => '081200000002',
            'status' => 'approved',
        ]);

        $responseB = $this->actingAs($this->adminAptika)->post(route('aptika.project.store'), [
            'pengajuan_id' => $pengajuanB->id,
            'nama_project' => 'Proyek Beta',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'ketua_tim_id' => $this->devBambang->id,
            'anggota_tim_ids' => [$this->devAhmad->id],
        ]);

        $projectB = Project::where('pengajuan_id', $pengajuanB->id)->first();
        $this->assertNotNull($projectB);
        $responseB->assertRedirect(route('aptika.project.show', $projectB));

        // Verifikasi peran di Proyek B (Bambang Ketua Tim, Ahmad Anggota)
        $this->assertDatabaseHas('tim_project', [
            'project_id' => $projectB->id,
            'user_id' => $this->devBambang->id,
            'peran' => 'ketua_tim',
        ]);
        $this->assertDatabaseHas('tim_project', [
            'project_id' => $projectB->id,
            'user_id' => $this->devAhmad->id,
            'peran' => 'anggota',
        ]);
    }

    public function test_aptika_dapat_mengubah_susunan_tim_proyek_berjalan(): void
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-TEST-003',
            'instansi_id' => $this->instansi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Aplikasi C',
            'deskripsi_kebutuhan' => 'Kebutuhan C',
            'nama_pemohon' => 'Pemohon C',
            'no_hp_pemohon' => '081200000003',
            'status' => 'approved',
        ]);

        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->instansi->id,
            'nama_aplikasi' => 'Aplikasi C',
            'deskripsi' => 'Deskripsi C',
            'status' => 'pengembangan',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Proyek Gamma',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status' => 'initiated',
        ]);

        // Awalnya: Ahmad Ketua, Bambang Anggota
        TimProject::create(['project_id' => $project->id, 'user_id' => $this->devAhmad->id, 'peran' => 'ketua_tim']);
        TimProject::create(['project_id' => $project->id, 'user_id' => $this->devBambang->id, 'peran' => 'anggota']);

        // Rotasi Tim: Bambang dijadikan Ketua Tim, Ahmad jadi Anggota
        $response = $this->actingAs($this->adminAptika)->patch(route('aptika.project.tim', $project), [
            'ketua_tim_id' => $this->devBambang->id,
            'anggota_tim_ids' => [$this->devAhmad->id],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tim_project', [
            'project_id' => $project->id,
            'user_id' => $this->devBambang->id,
            'peran' => 'ketua_tim',
        ]);
        $this->assertDatabaseHas('tim_project', [
            'project_id' => $project->id,
            'user_id' => $this->devAhmad->id,
            'peran' => 'anggota',
        ]);
    }
}
