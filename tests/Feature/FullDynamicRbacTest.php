<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\Instansi;
use App\Models\Pengajuan;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FullDynamicRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_custom_role_with_opd_permissions_can_access_opd_portal_and_redirects_correctly(): void
    {
        $role = Role::create(['name' => 'auditor_opd', 'guard_name' => 'web']);
        $role->givePermissionTo(['pengajuan.view', 'master.aplikasi.view']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertEquals(route('opd.dashboard'), $user->dashboardRoute());

        $this->actingAs($user)->get(route('opd.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get(route('opd.pengajuan.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('opd.aplikasi.index'))->assertStatus(200);

        // Cannot create because pengajuan.create was not granted
        $this->actingAs($user)->get(route('opd.pengajuan.create'))->assertStatus(403);
    }

    public function test_custom_role_with_security_permission_can_access_persandian_portal(): void
    {
        $role = Role::create(['name' => 'auditor_siber', 'guard_name' => 'web']);
        $role->givePermissionTo(['security.audit.view']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertEquals(route('persandian.dashboard'), $user->dashboardRoute());

        $this->actingAs($user)->get(route('persandian.dashboard'))->assertStatus(200);
        $this->actingAs($user)->get(route('persandian.pengujian.index'))->assertStatus(200);

        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas Testing',
            'singkatan' => 'DT',
            'jenis_instansi' => 'dinas',
            'kontak_person' => '08123456789',
            'alamat' => 'Pekanbaru',
        ]);
        $aplikasi = Aplikasi::create([
            'instansi_id' => $instansi->id,
            'nama_aplikasi' => 'App Test',
            'deskripsi' => 'Deskripsi',
            'status' => 'pengembangan',
            'bahasa_pemrograman' => 'PHP',
            'framework' => 'Laravel',
            'basis_data' => 'MySQL',
        ]);
        $pengajuan = Pengajuan::create([
            'kode_pengajuan' => 'REG-123',
            'instansi_id' => $instansi->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_aplikasi' => 'App Test',
            'jenis_pengajuan' => 'new_app',
            'deskripsi_kebutuhan' => 'Deskripsi Kebutuhan',
            'nama_pemohon' => 'Pemohon Test',
            'no_hp_pemohon' => '0812345678',
            'status' => 'approved',
        ]);
        $project = Project::create([
            'pengajuan_id' => $pengajuan->id,
            'aplikasi_id' => $aplikasi->id,
            'nama_project' => 'Project Test',
            'status' => 'security_testing',
            'progres_total' => 50,
            'tanggal_mulai' => now(),
            'target_selesai' => now()->addDays(30),
        ]);

        // Cannot access create audit without security.audit.create
        $this->actingAs($user)->get(route('persandian.pengujian.create', $project))->assertStatus(403);
    }

    public function test_custom_role_with_master_data_permission_can_access_superadmin_instansi_but_not_roles(): void
    {
        $role = Role::create(['name' => 'petugas_instansi', 'guard_name' => 'web']);
        $role->givePermissionTo(['master.instansi.view']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertEquals(route('superadmin.instansi.index'), $user->dashboardRoute());

        $this->actingAs($user)->get(route('superadmin.instansi.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('superadmin.dashboard'))->assertStatus(403);

        // Cannot access roles or permissions
        $this->actingAs($user)->get(route('superadmin.role.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('superadmin.permission.index'))->assertStatus(403);
    }

    public function test_unauthorized_custom_role_without_permissions_is_rejected(): void
    {
        $role = Role::create(['name' => 'role_tanpa_izin', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertEquals(route('login'), $user->dashboardRoute());
        $this->actingAs($user)->get(route('opd.dashboard'))->assertStatus(403);
        $this->actingAs($user)->get(route('aptika.dashboard'))->assertStatus(403);
        $this->actingAs($user)->get(route('superadmin.dashboard'))->assertStatus(403);
    }
}
