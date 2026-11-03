<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get(route('profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->username);
        $response->assertSee($user->email);
        $response->assertSee('ID User Telegram');
        $response->assertSee('Keamanan &amp; Kata Sandi', false);
    }

    public function test_user_can_update_profile_information_and_telegram_id(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Kaido Super Admin Updated',
            'username' => 'kaido_updated',
            'email' => 'kaido_new@riau.go.id',
            'no_hp' => '081299998888',
            'jabatan' => 'Administrator Utama SIMBANGDA',
            'telegram_id' => '987654321',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Kaido Super Admin Updated', $user->name);
        $this->assertSame('kaido_updated', $user->username);
        $this->assertSame('kaido_new@riau.go.id', $user->email);
        $this->assertSame('081299998888', $user->no_hp);
        $this->assertSame('Administrator Utama SIMBANGDA', $user->jabatan);
        $this->assertSame('987654321', $user->telegram_id);
    }

    public function test_user_can_keep_own_username_and_leave_telegram_id_null(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Kaido Same Username',
            'username' => 'kaido',
            'email' => $user->email,
            'no_hp' => null,
            'jabatan' => null,
            'telegram_id' => null,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Kaido Same Username', $user->name);
        $this->assertSame('kaido', $user->username);
        $this->assertNull($user->telegram_id);
    }

    public function test_user_cannot_take_existing_username_of_another_user(): void
    {
        $user1 = User::where('username', 'kaido')->first();
        $user2 = User::where('username', 'admin_aptika')->first();

        $response = $this->actingAs($user1)->patch(route('profile.update'), [
            'name' => 'Kaido Attempt',
            'username' => 'admin_aptika', // milik user2
            'email' => 'unique_kaido@riau.go.id',
        ]);

        $response->assertSessionHasErrors('username');
    }

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'wanokuni123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'wrongcurrentpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');

        $user->refresh();
        $this->assertTrue(Hash::check('wanokuni123', $user->password));
    }

    public function test_user_cannot_update_password_when_confirmation_mismatches(): void
    {
        $user = User::where('username', 'kaido')->first();

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'wanokuni123',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
