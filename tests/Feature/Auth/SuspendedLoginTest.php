<?php

namespace Tests\Feature\Auth;

use App\Enums\User\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class SuspendedLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspended_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'status' => UserStatus::Suspended,
        ]);

        Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasErrors('form.email');

        $this->assertGuest();
    }
}
