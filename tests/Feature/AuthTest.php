<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('SIMBANGDA');
        $response->assertSee('Username atau Email');
    }

    public function test_user_can_login_using_username(): void
    {
        $response = $this->post('/login', [
            'login' => 'kaido',
            'password' => 'wanokuni123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_user_can_login_using_email(): void
    {
        $response = $this->post('/login', [
            'login' => 'kaido@admin.com',
            'password' => 'wanokuni123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'kaido',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_root_redirects_guest_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_root_redirects_authenticated_user_to_dashboard_without_loop(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_login_page_redirects_authenticated_user_to_dashboard_without_loop(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('superadmin.dashboard'));
    }
}
