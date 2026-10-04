<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GoogleRedirectTest extends TestCase
{
    public function test_google_redirect_route_sends_the_user_to_google(): void
    {
        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');

        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect();
        $location = $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com', $location);
        $this->assertStringContainsString('test-client-id', $location);
    }
}
