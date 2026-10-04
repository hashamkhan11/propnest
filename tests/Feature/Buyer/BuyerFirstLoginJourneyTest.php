<?php

namespace Tests\Feature\Buyer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class BuyerFirstLoginJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_buyer_sees_hello_right_after_registering(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Jamie Rivera')
            ->set('email', 'jamie@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role', 'buyer')
            ->call('register');

        $response = $this->get('/buyer/dashboard');

        $response->assertOk();
        $response->assertSee('Hello, Jamie');
    }

    public function test_same_buyer_sees_welcome_back_on_next_login(): void
    {
        $user = User::factory()->create([
            'name' => 'Jamie Rivera',
            'last_login_at' => now()->subWeek(),
        ]);

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $response = $this->get('/buyer/dashboard');

        $response->assertOk();
        $response->assertSee('Welcome back, Jamie');
    }
}
