<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_no_demo_properties(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, Property::count());
    }

    public function test_seeder_still_creates_test_accounts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, User::query()->where('role', 'admin')->count());
        $this->assertSame(1, User::query()->where('role', 'super_admin')->count());
        $this->assertSame(3, User::query()->where('role', 'agent')->count());
        $this->assertSame(5, User::query()->where('role', 'buyer')->count());
    }
}
