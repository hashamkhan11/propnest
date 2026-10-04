<?php

namespace Tests\Feature\Payment;

use App\Enums\Property\PropertyStatus;
use App\Livewire\Property\ManageProperties;
use App\Models\FeaturedPricingTier;
use App\Models\Property;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeaturedSlotCapTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_is_blocked_when_the_featured_slot_cap_is_reached(): void
    {
        Settings::setMaxFeaturedListings(1);

        $agent = User::factory()->agent()->create();
        Property::factory()->create([
            'agent_id' => $agent->id,
            'status' => PropertyStatus::Published,
            'is_featured' => true,
            'featured_until' => now()->addDays(10),
        ]);

        $newListing = Property::factory()->create([
            'agent_id' => $agent->id,
            'status' => PropertyStatus::Published,
        ]);

        $tier = FeaturedPricingTier::first();

        Livewire::actingAs($agent)->test(ManageProperties::class)
            ->call('feature', $newListing->id, $tier->id);

        $this->assertFalse($newListing->fresh()->is_featured);
        $this->assertDatabaseMissing('payments', ['property_id' => $newListing->id]);
    }
}
