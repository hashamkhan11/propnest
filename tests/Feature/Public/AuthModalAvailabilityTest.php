<?php

namespace Tests\Feature\Public;

use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthModalAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_modal_is_present_and_closed_on_a_public_listing_page(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published]);

        $response = $this->get('/properties');

        $response->assertOk();
        $response->assertSee('data-auth-modal', false);
        $response->assertSee('data-initial-open="false"', false);
        $response->assertSeeVolt('pages.auth.login');
        $response->assertSeeVolt('pages.auth.register');
        $response->assertSeeVolt('pages.auth.forgot-password');
    }

    public function test_header_login_and_register_links_open_the_modal_client_side(): void
    {
        $response = $this->get('/properties');

        $response->assertOk();
        $response->assertSee("openAuth('login'", false);
        $response->assertSee("openAuth('register'", false);
    }
}
