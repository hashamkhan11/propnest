<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_registration_screen_has_a_continue_with_google_button(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee(route('auth.google.redirect'), false);
        $response->assertSee('Continue with Google');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_new_users_default_to_buyer_role(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'buyer@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', 'buyer');

        $component->call('register');

        $this->assertSame(\App\Enums\User\UserRole::Buyer, \App\Models\User::where('email', 'buyer@example.com')->first()->role);
    }

    public function test_registering_as_agent_creates_an_agent_profile(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test Agent')
            ->set('email', 'agent@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', 'agent');

        $component->call('register');

        $user = \App\Models\User::where('email', 'agent@example.com')->first();

        $this->assertSame(\App\Enums\User\UserRole::Agent, $user->role);
        $this->assertNotNull($user->agentProfile);
    }

    public function test_role_cannot_be_set_to_admin_via_registration(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Sneaky User')
            ->set('email', 'sneaky@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', 'admin');

        $component->call('register');

        $component->assertHasErrors(['role']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.com']);
    }
}
