<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\FeaturedPricingTier;
use App\Models\Property;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_no_demo_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, Property::count());
        $this->assertSame(1, User::count());
    }

    public function test_seeder_creates_one_super_admin(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::sole();

        $this->assertSame('super_admin', $admin->role->value);
        $this->assertSame('admin@propnest.test', $admin->email);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_seeder_uses_the_configured_admin_password(): void
    {
        config(['propnest.admin.password' => 'a-long-secret']);

        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Hash::check('a-long-secret', User::sole()->password));
    }

    public function test_seeder_refuses_the_default_password_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->expectException(RuntimeException::class);

        (new DatabaseSeeder)->run();
    }

    public function test_seeder_creates_pricing_and_amenities(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(4900, FeaturedPricingTier::where('name', 'Standard')->value('price_cents'));
        $this->assertSame(['Starter', 'Professional', 'Agency'], SubscriptionPlan::orderBy('sort_order')->pluck('name')->all());
        $this->assertSame(count(DatabaseSeeder::AMENITIES), Amenity::count());
    }

    public function test_seeder_can_run_twice_without_duplicating_lookups(): void
    {
        $this->seed(DatabaseSeeder::class);
        User::query()->delete();
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, FeaturedPricingTier::count());
        $this->assertSame(3, SubscriptionPlan::count());
        $this->assertSame(count(DatabaseSeeder::AMENITIES), Amenity::count());
    }
}
