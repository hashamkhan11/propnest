<?php

namespace Tests\Feature\Auth;

use App\Enums\User\UserRole;
use App\Livewire\Auth\SocialRoleSelection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SocialRoleSelectionTest extends TestCase
{
    use RefreshDatabase;

    private function pending(): array
    {
        return [
            'google_id' => 'g-999',
            'name' => 'Pending Person',
            'email' => 'pending@example.com',
            'avatar_url' => 'https://lh3.googleusercontent.com/a/avatar.jpg',
        ];
    }

    public function test_visiting_without_a_pending_google_session_redirects_to_login(): void
    {
        $response = $this->get('/auth/social-role');

        $response->assertRedirect(route('login'));
    }

    public function test_renders_the_pending_users_name_and_email(): void
    {
        $response = $this->withSession(['social_pending_user' => $this->pending()])->get('/auth/social-role');

        $response->assertOk();
        $response->assertSee('Pending', false);
        $response->assertSee('pending@example.com');
    }

    public function test_submitting_without_a_role_shows_a_validation_error(): void
    {
        $this->withSession(['social_pending_user' => $this->pending()]);

        Livewire::test(SocialRoleSelection::class)
            ->set('role', '')
            ->call('continue')
            ->assertHasErrors(['role']);

        $this->assertGuest();
    }

    public function test_choosing_buyer_creates_a_verified_buyer_and_logs_in(): void
    {
        $this->withSession(['social_pending_user' => $this->pending()]);

        Livewire::test(SocialRoleSelection::class)
            ->set('role', 'buyer')
            ->call('continue')
            ->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'pending@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(UserRole::Buyer, $user->role);
        $this->assertSame('g-999', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('social_pending_user'));
    }

    public function test_choosing_agent_creates_an_agent_with_an_agent_profile(): void
    {
        $this->withSession(['social_pending_user' => $this->pending()]);

        Livewire::test(SocialRoleSelection::class)
            ->set('role', 'agent')
            ->call('continue');

        $user = User::where('email', 'pending@example.com')->first();

        $this->assertSame(UserRole::Agent, $user->role);
        $this->assertNotNull($user->agentProfile);
    }

    public function test_cancel_clears_the_session_and_redirects_to_login(): void
    {
        $this->withSession(['social_pending_user' => $this->pending()]);

        Livewire::test(SocialRoleSelection::class)
            ->call('cancel')
            ->assertRedirect(route('login'));

        $this->assertNull(session('social_pending_user'));
    }
}
