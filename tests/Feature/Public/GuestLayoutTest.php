<?php

namespace Tests\Feature\Public;

use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_properties_index_uses_public_layout(): void
    {
        $response = $this->get('/properties');

        $response->assertOk();
        $response->assertSee('PropNest');
        $response->assertSee('All rights reserved');
        $response->assertDontSee('My Favorites');
    }

    public function test_property_detail_uses_public_layout(): void
    {
        $property = Property::factory()->create(['status' => PropertyStatus::Published]);

        $response = $this->get(route('properties.show', $property));

        $response->assertOk();
        $response->assertSee('PropNest');
        $response->assertSee('All rights reserved');
    }

    public function test_agent_public_profile_uses_public_layout(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->get(route('agents.show', $agent));

        $response->assertOk();
        $response->assertSee('PropNest');
        $response->assertSee('All rights reserved');
    }
}
