<?php

namespace Tests\Feature;

use App\Models\Instansi;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->superadmin = User::where('username', 'kaido')->first();
    }

    public function test_guest_is_redirected_to_login_when_accessing_superadmin(): void
    {
        $response = $this->get('/superadmin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_non_superadmin_is_forbidden_from_superadmin_dashboard(): void
    {
        $opdUser = User::create([
            'name' => 'Staf OPD',
            'username' => 'stafopd',
            'email' => 'opd@riau.go.id',
            'password' => bcrypt('password123'),
        ]);
        $opdUser->assignRole('opd');

        $response = $this->actingAs($opdUser)->get('/superadmin/dashboard');

        $response->assertStatus(403);
    }

    public function test_superadmin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->superadmin)->get('/superadmin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, '.$this->superadmin->name.'!');
    }

    public function test_superadmin_can_create_instansi(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.instansi.store'), [
            'nama_instansi' => 'Dinas Pendidikan Provinsi Riau',
            'email' => 'disdik@riau.go.id',
            'no_telepon' => '0761-123456',
            'alamat' => 'Pekanbaru',
        ]);

        $response->assertRedirect(route('superadmin.instansi.index'));
        $this->assertDatabaseHas('instansi', [
            'nama_instansi' => 'Dinas Pendidikan Provinsi Riau',
        ]);
    }

    public function test_superadmin_can_create_user_with_role_and_instansi(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas PUPR Riau',
        ]);

        $response = $this->actingAs($this->superadmin)->post(route('superadmin.user.store'), [
            'name' => 'Operator PUPR',
            'username' => 'operatorpupr',
            'email' => 'pupr@riau.go.id',
            'password' => 'secret123',
            'role' => 'opd',
            'instansi_id' => $instansi->id,
        ]);

        $response->assertRedirect(route('superadmin.user.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'operatorpupr',
            'instansi_id' => $instansi->id,
        ]);

        $newUser = User::where('username', 'operatorpupr')->first();
        $this->assertTrue($newUser->hasRole('opd'));
    }

    public function test_superadmin_can_create_aplikasi(): void
    {
        $instansi = Instansi::create([
            'nama_instansi' => 'Dinas Kesehatan Riau',
        ]);

        $response = $this->actingAs($this->superadmin)->post(route('superadmin.aplikasi.store'), [
            'instansi_id' => $instansi->id,
            'nama_aplikasi' => 'e-Puskesmas Riau',
            'deskripsi' => 'Sistem data faskes daerah',
            'url_aplikasi' => 'https://epuskesmas.riau.go.id',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('superadmin.aplikasi.index'));
        $this->assertDatabaseHas('aplikasi', [
            'nama_aplikasi' => 'e-Puskesmas Riau',
            'status' => 'aktif',
        ]);
    }

    public function test_superadmin_can_access_anggota_tim_index(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.anggota-tim.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Anggota Tim Pengembang');
    }

    public function test_superadmin_can_create_anggota_tim(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('superadmin.anggota-tim.store'), [
            'name' => 'Fajar Programmer',
            'username' => 'fajarprog',
            'email' => 'fajar@riau.go.id',
            'no_hp' => '081234567890',
            'jabatan' => 'Backend Developer (PHP/Laravel)',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('superadmin.anggota-tim.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'fajarprog',
            'jabatan' => 'Backend Developer (PHP/Laravel)',
        ]);

        $fajar = User::where('username', 'fajarprog')->first();
        $this->assertTrue($fajar->hasRole('anggota_tim'));
    }

    public function test_superadmin_can_update_anggota_tim(): void
    {
        $person = User::create([
            'name' => 'Doni Test Update',
            'username' => 'donitestupdate',
            'email' => 'donitestupdate@riau.go.id',
            'password' => bcrypt('secret123'),
        ]);
        $person->assignRole('anggota_tim');

        $response = $this->actingAs($this->superadmin)->put(route('superadmin.anggota-tim.update', $person), [
            'name' => 'Doni Senior Lead',
            'username' => 'donitestupdate',
            'email' => 'donitestupdate@riau.go.id',
            'jabatan' => 'Lead Fullstack Developer',
        ]);

        $response->assertRedirect(route('superadmin.anggota-tim.index'));
        $person->refresh();
        $this->assertEquals('Doni Senior Lead', $person->name);
        $this->assertEquals('Lead Fullstack Developer', $person->jabatan);
        $this->assertTrue($person->hasRole('anggota_tim'));
    }
}
