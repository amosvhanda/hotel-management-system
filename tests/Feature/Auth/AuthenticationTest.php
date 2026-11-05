<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_guests_can_sign_in_with_email_and_go_to_their_dashboard(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_users_can_sign_in_with_their_phone_number(): void
    {
        $user = User::factory()->create(['phone' => '0771234567']);

        $this->post('/login', ['login' => '0771234567', 'password' => 'password']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_admins_are_sent_to_the_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['login' => $admin->email, 'password' => 'password'])
            ->assertRedirect('/admin/dashboard');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['login' => $user->email, 'password' => 'wrong-password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }
}
