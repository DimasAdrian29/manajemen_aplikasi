<?php

namespace Tests\Feature;

use App\Models\Instansi;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OpdDynamicPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $opdUser;

    protected Role $opdRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas Kesehatan Uji',
            'singkatan' => 'DINKES',
            'alamat' => 'Jl. Kesehatan No. 1',
            'kontak' => '08123456789',
        ]);

        $this->opdUser = User::factory()->create([
            'username' => 'opd_dynamic_tester',
            'name' => 'Operator DINKES',
            'instansi_id' => $instansi->id,
        ]);
        $this->opdUser->assignRole('opd');
        $this->opdRole = Role::where('name', 'opd')->first();
    }

    public function test_opd_sees_menu_and_can_access_routes_when_permissions_are_active(): void
    {
        // Secara default role opd memiliki permission pengajuan.view dan pengajuan.create
        $response = $this->actingAs($this->opdUser)->get(route('opd.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Ajukan Permohonan');
        $response->assertSee('Riwayat Pengajuan');

        $responseIndex = $this->actingAs($this->opdUser)->get(route('opd.pengajuan.index'));
        $responseIndex->assertStatus(200);

        $responseCreate = $this->actingAs($this->opdUser)->get(route('opd.pengajuan.create'));
        $responseCreate->assertStatus(200);
    }

    public function test_when_pengajuan_create_is_off_menu_disappears_and_create_route_returns_403(): void
    {
        // Superadmin mencabut izin 'pengajuan.create' dari role OPD
        $this->opdRole->revokePermissionTo('pengajuan.create');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Pada dashboard & sidebar OPD, tombol dan menu 'Ajukan Permohonan' harus HILANG
        $response = $this->actingAs($this->opdUser)->get(route('opd.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Ajukan Permohonan Baru');
        $response->assertDontSee('Jenis Layanan Fasilitasi Aplikasi Daerah');

        // 2. Akses langsung via URL ke formulir permohonan baru harus DITOLAK (403 Forbidden)
        $responseCreate = $this->actingAs($this->opdUser)->get(route('opd.pengajuan.create'));
        $responseCreate->assertStatus(403);
    }

    public function test_when_pengajuan_view_is_off_menu_disappears_and_index_route_returns_403(): void
    {
        // Superadmin mencabut izin 'pengajuan.view' dari role OPD
        $this->opdRole->revokePermissionTo('pengajuan.view');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Pada dashboard & sidebar OPD, menu dan tabel riwayat pengajuan harus HILANG
        $response = $this->actingAs($this->opdUser)->get(route('opd.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Lihat Riwayat Status');
        $response->assertDontSee('Permohonan Terbaru');

        // 2. Akses langsung via URL ke daftar pengajuan harus DITOLAK (403 Forbidden)
        $responseIndex = $this->actingAs($this->opdUser)->get(route('opd.pengajuan.index'));
        $responseIndex->assertStatus(403);
    }
}
