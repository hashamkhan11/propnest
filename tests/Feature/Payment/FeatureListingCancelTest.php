<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureListingCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiting_the_cancel_url_marks_the_pending_payment_cancelled(): void
    {
        $property = Property::factory()->create();

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_cancel_1',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        $response = $this->actingAs($property->agent)
            ->get(route('agent.properties.feature.cancel', $property).'?session_id=cs_cancel_1');

        $response->assertRedirect(route('agent.properties.index'));
        $this->assertSame(PaymentStatus::Cancelled, $payment->fresh()->status);
    }

    public function test_cancel_does_not_downgrade_an_already_completed_payment(): void
    {
        $property = Property::factory()->create();

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_cancel_2',
            'amount' => 1000000,
            'status' => PaymentStatus::Completed,
            'featured_until' => now()->addDays(30),
        ]);

        $this->actingAs($property->agent)
            ->get(route('agent.properties.feature.cancel', $property).'?session_id=cs_cancel_2');

        $this->assertSame(PaymentStatus::Completed, $payment->fresh()->status);
    }
}
