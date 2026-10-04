<?php

namespace Database\Seeders;

use App\Enums\Settings\Currency;
use App\Models\Amenity;
use App\Models\FeaturedPricingTier;
use App\Models\SiteSetting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Everything a fresh install needs to be usable: settings, pricing, amenities
 * and one Super Admin. It is safe to run in production and creates no demo
 * content; run DemoSeeder for a populated marketplace.
 */
class DatabaseSeeder extends Seeder
{
    public const AMENITIES = [
        'Pool', 'Garage', 'Air Conditioning', 'Balcony', 'Garden',
        'Parking', 'Furnished', 'Pet Friendly', 'Security', 'Elevator',
    ];

    public function run(): void
    {
        $this->seedSettings();
        $this->seedPricing();

        foreach (self::AMENITIES as $name) {
            Amenity::firstOrCreate(['name' => $name]);
        }

        User::factory()->superAdmin()->create([
            'name' => config('propnest.admin.name'),
            'email' => config('propnest.admin.email'),
            'password' => $this->adminPassword(),
        ]);
    }

    private function seedSettings(): void
    {
        SiteSetting::query()->firstOrFail()->update([
            'currency' => Currency::USD,
            'max_featured_listings' => 12,
            'free_listing_limit' => 5,
        ]);
    }

    private function seedPricing(): void
    {
        // The migration ships a placeholder "Standard" tier; give it a real price.
        FeaturedPricingTier::updateOrCreate(
            ['name' => 'Standard'],
            ['duration_days' => 30, 'price_cents' => 4900, 'is_active' => true, 'sort_order' => 2],
        );
        FeaturedPricingTier::updateOrCreate(
            ['name' => 'Weekend Boost'],
            ['duration_days' => 7, 'price_cents' => 1900, 'is_active' => true, 'sort_order' => 1],
        );

        $plans = [
            ['name' => 'Starter', 'duration_days' => 30, 'price_cents' => 2900, 'listing_limit' => 15, 'featured_credits' => 1],
            ['name' => 'Professional', 'duration_days' => 30, 'price_cents' => 7900, 'listing_limit' => 50, 'featured_credits' => 5],
            ['name' => 'Agency', 'duration_days' => 90, 'price_cents' => 19900, 'listing_limit' => null, 'featured_credits' => 15],
        ];

        foreach ($plans as $order => $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan + ['is_active' => true, 'sort_order' => $order + 1],
            );
        }
    }

    private function adminPassword(): string
    {
        $password = config('propnest.admin.password');

        if (filled($password)) {
            return $password;
        }

        if (app()->isProduction()) {
            throw new RuntimeException('Set ADMIN_PASSWORD before seeding in production.');
        }

        return 'password';
    }
}
