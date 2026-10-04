<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Livewire\Property\ManageProperties;
use App\Models\FeaturedPricingTier;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeatureListingGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_start_a_second_checkout_while_a_payment_is_pending(): void
    {
        $property = Property::factory()->create();

        Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_existing_pending',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('feature', $property->id, FeaturedPricingTier::first()->id);

        $this->assertSame(1, Payment::where('property_id', $property->id)->count());
        $this->assertSame(
            'A payment for this listing is already in progress. You can cancel it below to try again.',
            session('error')
        );
    }

    public function test_cannot_feature_a_listing_that_is_already_featured(): void
    {
        $property = Property::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->addDays(10),
        ]);

        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('feature', $property->id, FeaturedPricingTier::first()->id);

        $this->assertSame(0, Payment::where('property_id', $property->id)->count());
        $this->assertSame('This listing is already featured.', session('error'));
    }
}
