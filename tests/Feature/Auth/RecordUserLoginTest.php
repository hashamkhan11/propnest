<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RecordUserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_login_ever_sets_session_flag_true_and_stamps_last_login_at(): void
    {
        $user = User::factory()->create(['last_login_at' => null]);

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $this->assertTrue(session('is_first_login'));
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_returning_login_sets_session_flag_false(): void
    {
        $user = User::factory()->create(['last_login_at' => now()->subDay()]);

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login');

        $this->assertFalse(session('is_first_login'));
    }
}
