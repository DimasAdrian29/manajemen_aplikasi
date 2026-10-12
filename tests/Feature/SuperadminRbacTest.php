<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperadminRbacTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $opdUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $this->superadmin = User::factory()->create([
            'username' => 'super_test',
            'name' => 'Superadmin Tester',
        ]);
        $this->superadmin->assignRole('superadmin');

        $this->opdUser = User::factory()->create([
            'username' => 'opd_test',
            'name' => 'OPD Tester',
        ]);
        $this->opdUser->assignRole('opd');
    }

    public function test_superadmin_can_access_role_index(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.role.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Role (Peran Pengguna)');
        $response->assertSee('Daftar Peran Pengguna (Role)');
        $response->assertSee('Tambah Peran Baru');
        $response->assertSee('superadmin');
        $response->assertSee('admin_aptika');
        $response->assertDontSee('Atur Permission');
        $response->assertSee('Master User');
    }

    public function test_superadmin_can_access_permission_index_showing_roles_table_with_action(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.permission.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Peran dan Hak Akses Sistem');
        $response->assertSee('admin_aptika');
        $response->assertSee('Atur Permission');
        $aptikaRole = Role::where('name', 'admin_aptika')->first();
        $response->assertSee(route('superadmin.role.permissions', $aptikaRole));
    }

    public function test_sidebar_displays_master_user_section_with_all_three_menus(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Master User');
        $response->assertSee(route('superadmin.user.index'));
        $response->assertSee(route('superadmin.role.index'));
        $response->assertSee(route('superadmin.permission.index'));
    }

    public function test_superadmin_can_create_new_custom_role(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.role.store'), [
            'name' => 'auditor_keuangan',
        ]);

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'auditor_keuangan',
            'guard_name' => 'web',
        ]);
    }

    public function test_superadmin_cannot_create_duplicate_role(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.role.store'), [
            'name' => 'admin_aptika',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_superadmin_can_view_role_permissions_matrix(): void
    {
        $role = Role::where('name', 'opd')->first();

        $response = $this->actingAs($this->superadmin)->get(route('superadmin.role.permissions', $role));

        $response->assertStatus(200);
        $response->assertSee('Matriks Hak Akses Peran');
        $response->assertSee('Modul 1: Manajemen Akses & Keamanan Sistem (RBAC)');
        $response->assertSee('Modul 3: Layanan Usulan & Pengajuan OPD');
        $response->assertSee('pengajuan.view');
        $response->assertSee('pengajuan.create');
    }

    public function test_superadmin_can_update_role_permissions(): void
    {
        $role = Role::create(['name' => 'verifikator_spbe', 'guard_name' => 'web']);

        $permissions = ['pengajuan.view', 'pengajuan.review', 'master.aplikasi.view'];

        $response = $this->actingAs($this->superadmin)->put(route('superadmin.role.permissions.update', $role), [
            'permissions' => $permissions,
        ]);

        $response->assertRedirect(route('superadmin.role.permissions', $role));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertTrue($role->hasPermissionTo('pengajuan.view'));
        $this->assertTrue($role->hasPermissionTo('pengajuan.review'));
        $this->assertTrue($role->hasPermissionTo('master.aplikasi.view'));
        $this->assertFalse($role->hasPermissionTo('pengajuan.delete'));
    }

    public function test_superadmin_role_cannot_be_deleted(): void
    {
        $role = Role::where('name', 'superadmin')->first();

        $response = $this->actingAs($this->superadmin)->delete(route('superadmin.role.destroy', $role));

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'superadmin']);
    }

    public function test_system_roles_cannot_be_deleted(): void
    {
        $role = Role::where('name', 'ketua_tim')->first();

        $response = $this->actingAs($this->superadmin)->delete(route('superadmin.role.destroy', $role));

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'ketua_tim']);
    }

    public function test_system_roles_cannot_be_renamed(): void
    {
        $role = Role::where('name', 'ketua_tim')->first();

        $response = $this->actingAs($this->superadmin)->put(route('superadmin.role.update', $role), [
            'name' => 'lead_developer',
        ]);

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'ketua_tim']);
    }

    public function test_superadmin_can_delete_role(): void
    {
        $role = Role::create(['name' => 'temp_role', 'guard_name' => 'web']);

        $response = $this->actingAs($this->superadmin)->delete(route('superadmin.role.destroy', $role));

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['name' => 'temp_role']);
    }

    public function test_superadmin_can_delete_role_even_with_active_users(): void
    {
        $role = Role::create(['name' => 'special_tester', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($this->superadmin)->delete(route('superadmin.role.destroy', $role));

        $response->assertRedirect(route('superadmin.role.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['name' => 'special_tester']);
        $this->assertFalse($user->fresh()->hasRole('special_tester'));
    }

    public function test_non_superadmin_is_forbidden_from_rbac_pages(): void
    {
        $response = $this->actingAs($this->opdUser)->get(route('superadmin.role.index'));
        $response->assertStatus(403);
    }
}
