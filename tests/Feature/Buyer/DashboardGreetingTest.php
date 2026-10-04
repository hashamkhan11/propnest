<?php

namespace Tests\Feature\Buyer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardGreetingTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_time_buyer_sees_hello_greeting(): void
    {
        $user = User::factory()->create(['name' => 'Jamie Rivera']);
        $this->actingAs($user);
        session(['is_first_login' => true]);

        $response = $this->get('/buyer/dashboard');

        $response->assertOk();
        $response->assertSee('Hello, Jamie');
        $response->assertDontSee('Welcome back, Jamie');
    }

    public function test_returning_buyer_sees_welcome_back_greeting(): void
    {
        $user = User::factory()->create(['name' => 'Jamie Rivera']);
        $this->actingAs($user);
        session(['is_first_login' => false]);

        $response = $this->get('/buyer/dashboard');

        $response->assertOk();
        $response->assertSee('Welcome back, Jamie');
    }

    public function test_missing_session_flag_defaults_to_welcome_back(): void
    {
        $user = User::factory()->create(['name' => 'Jamie Rivera']);
        $this->actingAs($user);

        $response = $this->get('/buyer/dashboard');

        $response->assertOk();
        $response->assertSee('Welcome back, Jamie');
    }
}
