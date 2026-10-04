<?php

namespace Tests\Feature\Agent;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_profile_shows_reminder_banner(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->get('/agent/dashboard');

        $response->assertSee('Complete your agent profile');
    }

    public function test_complete_profile_hides_reminder_banner(): void
    {
        $agent = User::factory()->agent()->create();
        $agent->agentProfile->update([
            'agency_name' => 'Acme Realty',
            'phone' => '555-1234',
            'bio' => 'Experienced local agent.',
        ]);

        $response = $this->actingAs($agent)->get('/agent/dashboard');

        $response->assertDontSee('Complete your agent profile');
    }
}
