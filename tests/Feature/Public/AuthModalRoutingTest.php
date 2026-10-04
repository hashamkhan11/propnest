<?php

namespace Tests\Feature\Public;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AuthModalRoutingTest extends TestCase
{
    use RefreshDatabase;

    private const HOMEPAGE_MARKER = 'Find your next home,';

    public function test_login_url_renders_the_homepage_with_the_login_modal_open(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee(self::HOMEPAGE_MARKER);
        $response->assertSeeVolt('pages.auth.login');
        $response->assertSee('data-initial-open="true"', false);
        $response->assertSee('data-initial-view="login"', false);
    }

    public function test_register_url_renders_the_homepage_with_the_register_modal_open(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee(self::HOMEPAGE_MARKER);
        $response->assertSeeVolt('pages.auth.register');
        $response->assertSee('data-initial-view="register"', false);
    }

    public function test_forgot_password_url_renders_the_homepage_with_the_forgot_password_modal_open(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertSee(self::HOMEPAGE_MARKER);
        $response->assertSeeVolt('pages.auth.forgot-password');
        $response->assertSee('data-initial-view="forgot-password"', false);
    }

    public function test_reset_password_url_renders_the_homepage_with_the_reset_password_modal_open(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Volt::test('pages.auth.forgot-password')
            ->set('email', $user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertOk();
            $response->assertSee(self::HOMEPAGE_MARKER);
            $response->assertSeeVolt('pages.auth.reset-password');
            $response->assertSee('data-initial-view="reset-password"', false);

            return true;
        });
    }
}
