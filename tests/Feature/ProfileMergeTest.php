<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileMergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_does_not_see_agent_profile_section(): void
    {
        $buyer = User::factory()->create();

        $response = $this->actingAs($buyer)->get('/profile');

        $response->assertOk();
        $response->assertDontSee('Agency Name');
    }

    public function test_agent_sees_agent_profile_section_on_the_same_page(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->get('/profile');

        $response->assertOk();
        $response->assertSee('Agency Name');
    }

    public function test_agent_profile_redirects_to_profile(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)->get('/agent/profile')->assertRedirect('/profile');
    }
}
