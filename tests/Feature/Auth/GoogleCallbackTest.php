<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleCallbackTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleUser(string $id, string $email, string $name = 'Test User', bool $emailVerified = true): void
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;
        $socialiteUser->name = $name;
        $socialiteUser->avatar = 'https://lh3.googleusercontent.com/a/avatar.jpg';
        $socialiteUser->user = ['email_verified' => $emailVerified];

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn(Mockery::mock(GoogleProvider::class, function ($mock) use ($socialiteUser) {
                $mock->shouldReceive('user')->andReturn($socialiteUser);
            }));
    }

    public function test_existing_google_user_logs_in_directly(): void
    {
        $user = User::factory()->create(['google_id' => 'g-123', 'email' => 'known@example.com']);
        $this->fakeGoogleUser('g-123', 'known@example.com');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_matching_email_links_google_id_and_logs_in(): void
    {
        $user = User::factory()->create(['email' => 'linkme@example.com', 'google_id' => null]);
        $this->fakeGoogleUser('g-456', 'linkme@example.com');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('g-456', $user->fresh()->google_id);
    }

    public function test_brand_new_email_is_sent_to_role_selection_without_creating_a_user(): void
    {
        $this->fakeGoogleUser('g-789', 'brandnew@example.com', 'Brand New');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('auth.social-role'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'brandnew@example.com']);
        $this->assertSame('brandnew@example.com', session('social_pending_user')['email']);
        $this->assertSame('Brand New', session('social_pending_user')['name']);
    }

    public function test_denied_or_failed_google_auth_redirects_to_login_with_an_error(): void
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn(Mockery::mock(GoogleProvider::class, function ($mock) {
                $mock->shouldReceive('user')->andThrow(new \Exception('invalid state'));
            }));

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_matching_email_with_unverified_google_claim_does_not_link_or_log_in(): void
    {
        $user = User::factory()->create(['email' => 'unverified-claim@example.com', 'google_id' => null]);
        $this->fakeGoogleUser('g-999', 'unverified-claim@example.com', 'Test User', emailVerified: false);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_matching_email_auto_link_marks_unverified_user_as_verified(): void
    {
        $user = User::factory()->create([
            'email' => 'was-unverified@example.com',
            'google_id' => null,
            'email_verified_at' => null,
        ]);
        $this->fakeGoogleUser('g-111', 'was-unverified@example.com');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_matching_email_auto_link_preserves_existing_verified_timestamp(): void
    {
        $originalTimestamp = now()->subDays(10)->startOfSecond();
        $user = User::factory()->create([
            'email' => 'already-verified@example.com',
            'google_id' => null,
            'email_verified_at' => $originalTimestamp,
        ]);
        $this->fakeGoogleUser('g-222', 'already-verified@example.com');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($originalTimestamp->equalTo($user->fresh()->email_verified_at));
    }
}
