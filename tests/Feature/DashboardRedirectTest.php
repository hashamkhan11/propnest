<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_is_redirected_to_buyer_dashboard(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->get('/dashboard')->assertRedirect(route('buyer.dashboard'));
    }

    public function test_agent_is_redirected_to_agent_dashboard(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)->get('/dashboard')->assertRedirect(route('agent.dashboard'));
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_super_admin_is_redirected_to_admin_dashboard(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/dashboard')->assertRedirect(route('admin.dashboard'));
    }

    public function test_super_admin_can_access_admin_routes(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_buyer_dashboard_shows_quick_links(): void
    {
        $buyer = User::factory()->create();

        $response = $this->actingAs($buyer)->get('/buyer/dashboard');

        $response->assertOk();
        $response->assertSee('Favorites');
        $response->assertSee('Saved Searches');
    }
}
