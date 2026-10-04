<?php

namespace Tests\Feature\Agent;

use App\Enums\Property\PropertyStatus;
use App\Models\AgentProfile;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_agents_directory_lists_agents(): void
    {
        $agent = User::factory()->agent()->create(['name' => 'Jane Smith']);

        $response = $this->get('/agents');

        $response->assertOk();
        $response->assertSee('Jane Smith');
    }

    public function test_agents_directory_search_filters_by_name(): void
    {
        User::factory()->agent()->create(['name' => 'Jane Smith']);
        User::factory()->agent()->create(['name' => 'Bob Jones']);

        $response = $this->get('/agents?keyword=Jane');

        $response->assertSee('Jane Smith');
        $response->assertDontSee('Bob Jones');
    }

    public function test_agents_directory_search_filters_by_agency_name(): void
    {
        $agent = User::factory()->agent()->create(['name' => 'Jane Smith']);
        $agent->agentProfile()->update(['agency_name' => 'Ace Realty Group']);

        $other = User::factory()->agent()->create(['name' => 'Bob Jones']);
        $other->agentProfile()->update(['agency_name' => 'Other Realty']);

        $response = $this->get('/agents?keyword=Ace');

        $response->assertSee('Jane Smith');
        $response->assertDontSee('Bob Jones');
    }

    public function test_agents_directory_only_counts_published_listings(): void
    {
        $agent = User::factory()->agent()->create(['name' => 'Jane Smith']);
        Property::factory()->for($agent, 'agent')->create(['status' => PropertyStatus::Published]);
        Property::factory()->for($agent, 'agent')->create(['status' => PropertyStatus::Draft]);

        $response = $this->get('/agents');

        $response->assertOk();
        $response->assertSee('1 active listing');
    }
}
