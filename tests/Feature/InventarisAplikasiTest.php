<?php

namespace Tests\Feature;

use App\Models\Instansi;
use App\Models\InventarisAplikasi;
use App\Models\User;
use Database\Seeders\InventarisAplikasiSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarisAplikasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminAptika;

    protected Instansi $instansi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->instansi = Instansi::create(['nama_instansi' => 'Dinas Komunikasi, Informatika dan Statistik']);

        $this->adminAptika = User::factory()->create();
        $this->adminAptika->assignRole('admin_aptika');
    }

    public function test_admin_aptika_melihat_menu_dan_halaman_monitoring(): void
    {
        InventarisAplikasi::create([
            'nama_aplikasi' => 'Rumah Data',
            'instansi_id' => $this->instansi->id,
            'status' => 'aktif',
            'url' => 'rumahdata.riau.go.id',
        ]);

        $this->actingAs($this->adminAptika)
            ->get(route('aptika.dashboard'))
            ->assertOk()
            ->assertSee('Monitoring Aplikasi');

        $this->actingAs($this->adminAptika)
            ->get(route('aptika.inventaris-aplikasi.index'))
            ->assertOk()
            ->assertSee('Rumah Data')
            ->assertSee('rumahdata.riau.go.id');
    }

    public function test_filter_status_hanya_menampilkan_aplikasi_yang_sesuai(): void
    {
        InventarisAplikasi::create(['nama_aplikasi' => 'Aplikasi Aktif', 'instansi_id' => $this->instansi->id, 'status' => 'aktif']);
        InventarisAplikasi::create(['nama_aplikasi' => 'Aplikasi Mati', 'instansi_id' => $this->instansi->id, 'status' => 'tidak_aktif']);

        $this->actingAs($this->adminAptika)
            ->get(route('aptika.inventaris-aplikasi.index', ['status' => 'tidak_aktif']))
            ->assertOk()
            ->assertSee('Aplikasi Mati')
            ->assertDontSee('Aplikasi Aktif');
    }

    public function test_admin_aptika_dapat_menambah_mengubah_dan_menghapus_aplikasi(): void
    {
        $response = $this->actingAs($this->adminAptika)->post(route('aptika.inventaris-aplikasi.store'), [
            'nomor_registrasi' => 500,
            'nama_aplikasi' => 'Sistem Baru',
            'instansi_id' => $this->instansi->id,
            'status' => 'aktif',
            'platform' => 'web',
            'jenis_layanan' => 'publik',
            'konektivitas' => 'online',
            'programmer' => "Nama Satu, S.Kom\nNama Dua, S.T",
            'ada_surat_keputusan' => '1',
        ]);

        $aplikasi = InventarisAplikasi::where('nomor_registrasi', 500)->firstOrFail();
        $response->assertRedirect(route('aptika.inventaris-aplikasi.show', $aplikasi));
        $this->assertTrue($aplikasi->ada_surat_keputusan);
        $this->assertFalse($aplikasi->ada_bast);
        $this->assertSame(['Nama Satu, S.Kom', 'Nama Dua, S.T'], $aplikasi->anggotaTim('programmer'));

        $this->actingAs($this->adminAptika)
            ->get(route('aptika.inventaris-aplikasi.show', $aplikasi))
            ->assertOk()
            ->assertSee('Nama Dua, S.T');

        $this->actingAs($this->adminAptika)
            ->get(route('aptika.inventaris-aplikasi.edit', $aplikasi))
            ->assertOk();

        $this->actingAs($this->adminAptika)->put(route('aptika.inventaris-aplikasi.update', $aplikasi), [
            'nomor_registrasi' => 500,
            'nama_aplikasi' => 'Sistem Baru v2',
            'instansi_id' => $this->instansi->id,
            'status' => 'tidak_aktif',
        ])->assertRedirect(route('aptika.inventaris-aplikasi.show', $aplikasi));

        $aplikasi->refresh();
        $this->assertSame('Sistem Baru v2', $aplikasi->nama_aplikasi);
        $this->assertSame('tidak_aktif', $aplikasi->status);
        $this->assertFalse($aplikasi->ada_surat_keputusan);

        $this->actingAs($this->adminAptika)
            ->delete(route('aptika.inventaris-aplikasi.destroy', $aplikasi))
            ->assertRedirect(route('aptika.inventaris-aplikasi.index'));

        $this->assertModelMissing($aplikasi);
    }

    public function test_status_diganti_wajib_memilih_aplikasi_pengganti(): void
    {
        $this->actingAs($this->adminAptika)->post(route('aptika.inventaris-aplikasi.store'), [
            'nama_aplikasi' => 'Aplikasi Lama',
            'instansi_id' => $this->instansi->id,
            'status' => 'diganti',
        ])->assertSessionHasErrors('aplikasi_pengganti_id');

        $this->assertDatabaseCount('inventaris_aplikasi', 0);
    }

    public function test_aplikasi_pengganti_tidak_dapat_dihapus_selama_masih_direferensikan(): void
    {
        $baru = InventarisAplikasi::create(['nama_aplikasi' => 'Aplikasi Baru', 'instansi_id' => $this->instansi->id, 'status' => 'aktif']);
        InventarisAplikasi::create([
            'nama_aplikasi' => 'Aplikasi Lama',
            'instansi_id' => $this->instansi->id,
            'status' => 'diganti',
            'aplikasi_pengganti_id' => $baru->id,
        ]);

        $this->actingAs($this->adminAptika)
            ->delete(route('aptika.inventaris-aplikasi.destroy', $baru))
            ->assertSessionHas('error');

        $this->assertModelExists($baru);
    }

    public function test_pengguna_tanpa_hak_akses_ditolak(): void
    {
        $opd = User::factory()->create();
        $opd->assignRole('opd');

        $this->actingAs($opd)
            ->get(route('aptika.inventaris-aplikasi.index'))
            ->assertForbidden();
    }

    public function test_seeder_mengimpor_daftar_aplikasi_sesuai_ringkasan_sumber(): void
    {
        $this->seed(InventarisAplikasiSeeder::class);

        $this->assertSame(76, InventarisAplikasi::count());
        $this->assertSame(45, InventarisAplikasi::where('status', 'aktif')->count());
        $this->assertSame(12, InventarisAplikasi::where('status', 'tidak_aktif')->count());
        $this->assertSame(3, InventarisAplikasi::where('status', 'diganti')->count());
        $this->assertSame(40, InventarisAplikasi::where('jenis_layanan', 'publik')->count());
        $this->assertSame(16, InventarisAplikasi::where('jenis_layanan', 'pemerintahan')->count());
        $this->assertSame(10, InventarisAplikasi::where('is_website_resmi', true)->count());
        $this->assertSame(31, InventarisAplikasi::where('penempatan_server', 'Diskominfotik')->count());

        $sipadu = InventarisAplikasi::where('nomor_registrasi', 99)->first();
        $this->assertSame(439, $sipadu->aplikasiPengganti->nomor_registrasi);

        // Seeder aman dijalankan ulang tanpa menggandakan data
        $this->seed(InventarisAplikasiSeeder::class);
        $this->assertSame(76, InventarisAplikasi::count());
    }
}
