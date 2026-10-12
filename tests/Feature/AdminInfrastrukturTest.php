<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\HostingAplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\RiwayatHosting;
use App\Models\TimProject;
use App\Models\User;
use App\Notifications\AplikasiLiveNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminInfrastrukturTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminInfrastruktur;

    protected User $opdUser;

    protected User $ketuaTim;

    protected Instansi $dinkes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->adminInfrastruktur = User::where('username', 'admin_infrastruktur')->first();
        $this->opdUser = User::where('username', 'opd_dinkes')->first();
        $this->ketuaTim = User::where('username', 'ahmad_lead')->first();
        $this->dinkes = Instansi::where('nama_instansi', 'like', '%Kesehatan%')->first();
    }

    /**
     * Helper to create a valid Project with its required Pengajuan.
     */
    protected function createProjectWithPengajuan(Aplikasi $aplikasi, string $namaProject, string $status = 'deployment', int $progres = 90): Project
    {
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-'.strtoupper(uniqid()),
            'instansi_id' => $aplikasi->instansi_id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => $aplikasi->nama_aplikasi,
            'deskripsi_kebutuhan' => 'Kebutuhan sistem aplikasi.',
            'nama_pemohon' => 'Narahubung Dinkes',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
        ]);

        return Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => $namaProject,
            'status' => $status,
            'progres_total' => $progres,
        ]);
    }

    public function test_unauthorized_user_cannot_access_infrastruktur_portal(): void
    {
        // Guest redirected to login
        $guestResponse = $this->get(route('infrastruktur.dashboard'));
        $guestResponse->assertRedirect(route('login'));

        // OPD user gets 403 forbidden
        $opdResponse = $this->actingAs($this->opdUser)->get(route('infrastruktur.dashboard'));
        $opdResponse->assertStatus(403);
    }

    public function test_admin_infrastruktur_can_access_dashboard_and_see_metrics(): void
    {
        $response = $this->actingAs($this->adminInfrastruktur)->get(route('infrastruktur.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Infrastruktur');
        $response->assertSee('Antrean Deployment');
        $response->assertSee('Aplikasi Live');
    }

    public function test_deployment_queue_only_shows_projects_in_deployment_status(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Antrean Rumah Sakit',
            'status' => 'pengembangan',
        ]);

        $projectDeployment = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Sistem Antrean Faskes',
            'deployment',
            90
        );

        $projectDev = $this->createProjectWithPengajuan(
            $aplikasi,
            'Modul Penjadwalan Dokter (In Dev)',
            'in_development',
            50
        );

        $response = $this->actingAs($this->adminInfrastruktur)->get(route('infrastruktur.deployment.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengembangan Sistem Antrean Faskes');
        $response->assertDontSee('Modul Penjadwalan Dokter (In Dev)');
    }

    public function test_admin_infrastruktur_can_view_deployment_create_page_for_deployment_project(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Rekam Medis',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Proyek Rekam Medis Digital',
            'deployment',
            95
        );

        $response = $this->actingAs($this->adminInfrastruktur)->get(route('infrastruktur.deployment.create', $project));

        $response->assertStatus(200);
        $response->assertSee('Proyek Rekam Medis Digital');
        $response->assertSee('Form Deployment & Alokasi Server');
    }

    public function test_cannot_access_deployment_create_page_for_non_deployment_project(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Telemedicine',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Proyek Telemedicine',
            'in_development',
            30
        );

        $response = $this->actingAs($this->adminInfrastruktur)->get(route('infrastruktur.deployment.create', $project));

        $response->assertRedirect(route('infrastruktur.deployment.index'));
        $response->assertSessionHas('error');
    }

    public function test_deployment_store_triggers_go_live_transitions_and_sends_notification(): void
    {
        Notification::fake();

        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Portal Layanan Vaksinasi',
            'status' => 'pengembangan',
        ]);

        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-20261005-0099',
            'instansi_id' => $this->dinkes->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'new_app',
            'nama_aplikasi' => 'Portal Layanan Vaksinasi',
            'deskripsi_kebutuhan' => 'Layanan registrasi vaksinasi warga Riau.',
            'status' => 'approved',
            'nama_pemohon' => 'dr. Budi',
            'no_hp_pemohon' => '081234567890',
        ]);

        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Pengembangan Vaksinasi Online',
            'status' => 'deployment',
            'progres_total' => 95,
        ]);

        TimProject::create([
            'project_id' => $project->id,
            'user_id' => $this->ketuaTim->id,
            'peran' => 'ketua_tim',
        ]);

        $postData = [
            'nama_domain' => 'vaksin.riau.go.id',
            'url_aplikasi' => 'https://vaksin.riau.go.id',
            'status_hosting' => 'aktif',
            'jenis_hosting' => 'vps',
            'ip_server' => '103.150.12.88',
            'nama_server' => 'SRV-TIK-VM09',
            'sistem_operasi' => 'Ubuntu 22.04 LTS',
            'cpu_core' => 4,
            'ram_gb' => 8,
            'storage_gb' => 120,
            'web_server' => 'Nginx 1.24',
            'database_server' => 'MySQL 8.0',
            'ssl_aktif' => true,
            'ssl_kadaluarsa' => now()->addDays(90)->format('Y-m-d'),
            'tanggal_live' => now()->format('Y-m-d'),
            'keterangan' => 'Hosting produksi live.',
        ];

        $response = $this->actingAs($this->adminInfrastruktur)
            ->post(route('infrastruktur.deployment.store', $project), $postData);

        // Verify redirect
        $hosting = HostingAplikasi::where('project_id', $project->id)->first();
        $this->assertNotNull($hosting);
        $response->assertRedirect(route('infrastruktur.hosting.show', $hosting));

        // Verify Project transitions to LIVE
        $project->refresh();
        $this->assertEquals('live', $project->status);
        $this->assertEquals(100, $project->progres_total);

        // Verify Aplikasi transitions to aktif and syncs URL
        $aplikasi->refresh();
        $this->assertEquals('aktif', $aplikasi->status);
        $this->assertEquals('https://vaksin.riau.go.id', $aplikasi->url_aplikasi);

        // Verify Pengajuan has note updated
        $pengajuan->refresh();
        $this->assertStringContainsString('Aplikasi berhasil dideploy', $pengajuan->catatan_penilaian);

        // Verify Notification sent to OPD user and Ketua Tim
        Notification::assertSentTo($this->opdUser, AplikasiLiveNotification::class);
        Notification::assertSentTo($this->ketuaTim, AplikasiLiveNotification::class);
    }

    public function test_cannot_deploy_project_not_in_deployment_status(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Farmasi',
            'status' => 'pengembangan',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Proyek Farmasi',
            'functional_testing',
            80
        );

        $postData = [
            'nama_domain' => 'farmasi.riau.go.id',
            'url_aplikasi' => 'https://farmasi.riau.go.id',
            'status_hosting' => 'aktif',
            'jenis_hosting' => 'vps',
        ];

        $response = $this->actingAs($this->adminInfrastruktur)
            ->post(route('infrastruktur.deployment.store', $project), $postData);

        $response->assertRedirect(route('infrastruktur.deployment.index'));
        $this->assertDatabaseMissing('hosting_aplikasi', ['project_id' => $project->id]);
    }

    public function test_admin_infrastruktur_can_view_hosting_index_and_detail(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Posyandu Pintar',
            'status' => 'aktif',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Posyandu Pintar',
            'live',
            100
        );

        $hosting = HostingAplikasi::create([
            'project_id' => $project->id,
            'user_id' => $this->adminInfrastruktur->id,
            'nama_domain' => 'posyandu.riau.go.id',
            'url_aplikasi' => 'https://posyandu.riau.go.id',
            'ip_server' => '103.150.12.99',
            'jenis_hosting' => 'vps',
            'status_hosting' => 'aktif',
        ]);

        // Index
        $indexResponse = $this->actingAs($this->adminInfrastruktur)->get(route('infrastruktur.hosting.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('posyandu.riau.go.id');

        // Show
        $showResponse = $this->actingAs($this->adminInfrastruktur)->get(route('infrastruktur.hosting.show', $hosting));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('posyandu.riau.go.id');
        $showResponse->assertSee('103.150.12.99');
    }

    public function test_updating_hosting_creates_riwayat_hosting_audit_record(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Laboratorium',
            'status' => 'aktif',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Lab Online',
            'live',
            100
        );

        $hosting = HostingAplikasi::create([
            'project_id' => $project->id,
            'user_id' => $this->adminInfrastruktur->id,
            'nama_domain' => 'lab.riau.go.id',
            'url_aplikasi' => 'https://lab.riau.go.id',
            'ip_server' => '103.150.12.20',
            'jenis_hosting' => 'vps',
            'cpu_core' => 2,
            'ram_gb' => 4,
            'status_hosting' => 'aktif',
        ]);

        $updateData = [
            'nama_domain' => 'labkes.riau.go.id',
            'url_aplikasi' => 'https://labkes.riau.go.id',
            'status_hosting' => 'aktif',
            'jenis_hosting' => 'cloud',
            'ip_server' => '103.150.12.25',
            'cpu_core' => 4,
            'ram_gb' => 8,
            'alasan_perubahan' => 'Migrasi server ke Cloud dan upgrade kapasitas RAM & CPU.',
        ];

        $response = $this->actingAs($this->adminInfrastruktur)
            ->put(route('infrastruktur.hosting.update', $hosting), $updateData);

        $response->assertRedirect(route('infrastruktur.hosting.show', $hosting));

        // Verify hosting is updated
        $hosting->refresh();
        $this->assertEquals('labkes.riau.go.id', $hosting->nama_domain);
        $this->assertEquals('https://labkes.riau.go.id', $hosting->url_aplikasi);
        $this->assertEquals(8, $hosting->ram_gb);

        // Verify riwayat audit trail created
        $this->assertDatabaseHas('riwayat_hosting', [
            'hosting_aplikasi_id' => $hosting->id,
            'user_id' => $this->adminInfrastruktur->id,
            'alasan_perubahan' => 'Migrasi server ke Cloud dan upgrade kapasitas RAM & CPU.',
        ]);

        $riwayat = RiwayatHosting::where('hosting_aplikasi_id', $hosting->id)->first();
        $this->assertNotNull($riwayat);
        $this->assertEquals('lab.riau.go.id', $riwayat->data_sebelum['nama_domain']);
        $this->assertEquals(4, $riwayat->data_sebelum['ram_gb']);
    }

    public function test_updating_hosting_requires_alasan_perubahan(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Donor Darah',
            'status' => 'aktif',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Donor Darah Digital',
            'live',
            100
        );

        $hosting = HostingAplikasi::create([
            'project_id' => $project->id,
            'user_id' => $this->adminInfrastruktur->id,
            'nama_domain' => 'donor.riau.go.id',
            'url_aplikasi' => 'https://donor.riau.go.id',
            'status_hosting' => 'aktif',
            'jenis_hosting' => 'vps',
        ]);

        // Attempt update without alasan_perubahan
        $response = $this->actingAs($this->adminInfrastruktur)
            ->put(route('infrastruktur.hosting.update', $hosting), [
                'nama_domain' => 'donordarah.riau.go.id',
                'url_aplikasi' => 'https://donordarah.riau.go.id',
                'status_hosting' => 'aktif',
                'jenis_hosting' => 'vps',
            ]);

        $response->assertSessionHasErrors(['alasan_perubahan']);
    }

    public function test_admin_infrastruktur_can_execute_application_deactivation(): void
    {
        $aplikasi = Aplikasi::create([
            'instansi_id' => $this->dinkes->id,
            'nama_aplikasi' => 'Aplikasi Lama Dinkes',
            'status' => 'aktif',
            'url_aplikasi' => 'https://lama.dinkes.riau.go.id',
        ]);

        $project = $this->createProjectWithPengajuan(
            $aplikasi,
            'Pengembangan Sistem Lama',
            'live',
            100
        );

        $hosting = HostingAplikasi::create([
            'project_id' => $project->id,
            'user_id' => $this->adminInfrastruktur->id,
            'nama_domain' => 'lama.dinkes.riau.go.id',
            'url_aplikasi' => 'https://lama.dinkes.riau.go.id',
            'status_hosting' => 'aktif',
        ]);

        $pengajuanDeactivation = Pengajuan::create([
            'kode_pengajuan' => 'REG-DEACT-001',
            'instansi_id' => $this->dinkes->id,
            'aplikasi_id' => $aplikasi->id,
            'jenis_pengajuan' => 'app_deactivation',
            'nama_aplikasi' => 'Aplikasi Lama Dinkes',
            'deskripsi_kebutuhan' => 'Sistem sudah tidak digunakan dan digantikan oleh platform nasional.',
            'nama_pemohon' => 'Kadis Kesehatan',
            'no_hp_pemohon' => '081234567890',
            'status' => 'approved',
            'dinilai_oleh' => $this->adminInfrastruktur->id,
        ]);

        $response = $this->actingAs($this->adminInfrastruktur)
            ->post(route('infrastruktur.penonaktifan.execute', $pengajuanDeactivation), [
                'catatan' => 'Domain dinonaktifkan, database diarsip ke backup offline.',
            ]);

        $response->assertRedirect(route('infrastruktur.penonaktifan.index'));
        $response->assertSessionHas('success');

        // Verify Aplikasi is nonaktif
        $aplikasi->refresh();
        $this->assertEquals('nonaktif', $aplikasi->status);

        // Verify Project is inactive
        $project->refresh();
        $this->assertEquals('inactive', $project->status);

        // Verify Hosting is nonaktif
        $hosting->refresh();
        $this->assertEquals('nonaktif', $hosting->status_hosting);

        // Verify Pengajuan catatan updated
        $pengajuanDeactivation->refresh();
        $this->assertStringContainsString('Server dan domain aplikasi telah dinonaktifkan', $pengajuanDeactivation->catatan_penilaian);
    }
}
