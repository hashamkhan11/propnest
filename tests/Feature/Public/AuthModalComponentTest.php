<?php

namespace Tests\Feature\Public;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class AuthModalComponentTest extends TestCase
{
    public function test_modal_always_renders_login_register_and_forgot_password(): void
    {
        $html = Blade::render('<x-auth-modal :initial-view="$view" :reset-token="$token" />', ['view' => null, 'token' => null]);

        $this->assertStringContainsString('Welcome back', $html);
        $this->assertStringContainsString('Make yourself at home.', $html);
        $this->assertStringContainsString('Forgot your password?', $html);
    }

    public function test_modal_omits_reset_password_without_a_token(): void
    {
        $html = Blade::render('<x-auth-modal :initial-view="$view" :reset-token="$token" />', ['view' => null, 'token' => null]);

        $this->assertStringNotContainsString('Set a new password', $html);
    }

    public function test_modal_renders_reset_password_when_a_token_is_given(): void
    {
        $html = Blade::render('<x-auth-modal :initial-view="$view" :reset-token="$token" />', ['view' => 'reset-password', 'token' => 'sample-token-123']);

        $this->assertStringContainsString('Set a new password', $html);
    }

    public function test_modal_defaults_to_closed_with_no_initial_view(): void
    {
        $html = Blade::render('<x-auth-modal :initial-view="$view" :reset-token="$token" />', ['view' => null, 'token' => null]);

        $this->assertStringContainsString('data-initial-open="false"', $html);
        $this->assertStringContainsString('data-initial-view="login"', $html);
    }

    public function test_modal_seeds_open_and_the_given_view_from_props(): void
    {
        $html = Blade::render('<x-auth-modal :initial-view="$view" :reset-token="$token" />', ['view' => 'register', 'token' => null]);

        $this->assertStringContainsString('data-initial-open="true"', $html);
        $this->assertStringContainsString('data-initial-view="register"', $html);
    }

    public function test_modal_exposes_auth_route_urls_for_click_delegation(): void
    {
        $html = Blade::render('<x-auth-modal :initial-view="$view" :reset-token="$token" />', ['view' => null, 'token' => null]);

        $this->assertStringContainsString('data-login-url="'.route('login').'"', $html);
        $this->assertStringContainsString('data-register-url="'.route('register').'"', $html);
        $this->assertStringContainsString('data-forgot-url="'.route('password.request').'"', $html);
        $this->assertStringContainsString('data-home-url="'.route('home').'"', $html);
    }
}
