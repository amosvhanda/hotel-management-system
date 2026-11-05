<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** The guest account pages (My Profile, Change Password). */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/profile')
            ->assertOk()
            ->assertSee('My Profile');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/profile/store', ['name' => 'Tendai Moyo', 'email' => 'tendai@example.com', 'phone' => '0771234567'])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $user->refresh();
        $this->assertSame('Tendai Moyo', $user->name);
        $this->assertSame('tendai@example.com', $user->email);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/profile/store', ['name' => 'X', 'email' => 'taken@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function test_password_can_be_changed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/password/change/password', [
            'old_password' => 'password',
            'new_password' => 'NewSecret123',
            'new_password_confirmation' => 'NewSecret123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('NewSecret123', $user->refresh()->password));
    }

    public function test_wrong_current_password_is_refused(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/password/change/password', [
            'old_password' => 'not-my-password',
            'new_password' => 'NewSecret123',
            'new_password_confirmation' => 'NewSecret123',
        ]);

        $this->assertTrue(Hash::check('password', $user->refresh()->password));
    }
}
