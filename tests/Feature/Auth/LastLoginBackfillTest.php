<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LastLoginBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_last_login_at_column_exists_and_is_nullable(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'last_login_at'));

        $user = User::factory()->create();

        $this->assertNull($user->fresh()->last_login_at);
    }

    public function test_migration_backfills_existing_users_last_login_at_to_created_at(): void
    {
        $this->artisan('migrate:rollback', ['--path' => 'database/migrations/2026_08_01_105911_add_last_login_at_to_users_table.php']);

        $oldCreatedAt = now()->subDays(10);
        $userId = DB::table('users')->insertGetId([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'created_at' => $oldCreatedAt,
            'updated_at' => $oldCreatedAt,
        ]);

        $this->artisan('migrate', ['--path' => 'database/migrations/2026_08_01_105911_add_last_login_at_to_users_table.php']);

        $lastLoginAt = DB::table('users')->where('id', $userId)->value('last_login_at');

        $this->assertNotNull($lastLoginAt);
        $this->assertEquals($oldCreatedAt->toDateTimeString(), Carbon::parse($lastLoginAt)->toDateTimeString());
    }
}
