<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleAccountFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_google_id_and_avatar(): void
    {
        $user = User::factory()->create([
            'google_id' => '109876543210',
            'avatar_url' => 'https://lh3.googleusercontent.com/a/avatar.jpg',
        ]);

        $this->assertSame('109876543210', $user->fresh()->google_id);
        $this->assertSame('https://lh3.googleusercontent.com/a/avatar.jpg', $user->fresh()->avatar_url);
    }

    public function test_google_id_must_be_unique(): void
    {
        User::factory()->create(['google_id' => 'dup-id']);

        $this->expectException(QueryException::class);

        User::factory()->create(['google_id' => 'dup-id']);
    }

    public function test_multiple_users_can_have_a_null_google_id(): void
    {
        User::factory()->create(['google_id' => null]);
        User::factory()->create(['google_id' => null]);

        $this->assertSame(2, User::whereNull('google_id')->count());
    }
}
