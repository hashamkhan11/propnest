<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureListingSuccessStatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_confirmation_when_payment_already_completed(): void
    {
        $property = Property::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->addDays(30),
        ]);

        Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_done',
            'amount' => 1000000,
            'status' => PaymentStatus::Completed,
            'featured_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($property->agent)
            ->get(route('agent.properties.feature.success', $property).'?session_id=cs_done');

        $response->assertOk();
        $response->assertSee('is now featured until');
    }

    public function test_shows_failure_message_with_reason(): void
    {
        $property = Property::factory()->create();

        Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_failed',
            'amount' => 1000000,
            'status' => PaymentStatus::Failed,
            'failure_reason' => 'Your card was declined.',
            'featured_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($property->agent)
            ->get(route('agent.properties.feature.success', $property).'?session_id=cs_failed');

        $response->assertOk();
        $response->assertSee('Your card was declined.');
        $response->assertSee('Try again from My Listings');
    }

    public function test_shows_cancelled_message(): void
    {
        $property = Property::factory()->create();

        Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_cancelled',
            'amount' => 1000000,
            'status' => PaymentStatus::Cancelled,
            'featured_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($property->agent)
            ->get(route('agent.properties.feature.success', $property).'?session_id=cs_cancelled');

        $response->assertOk();
        $response->assertSee('Checkout was cancelled');
    }

    public function test_shows_expired_message(): void
    {
        $property = Property::factory()->create();

        Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_expired',
            'amount' => 1000000,
            'status' => PaymentStatus::Expired,
            'featured_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($property->agent)
            ->get(route('agent.properties.feature.success', $property).'?session_id=cs_expired');

        $response->assertOk();
        $response->assertSee('This checkout session expired');
    }
}
