<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Livewire\Property\ManageProperties;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RequestRefundTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_request_a_refund_for_a_currently_featured_listing(): void
    {
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'status' => PaymentStatus::Completed,
            'featured_until' => $property->featured_until,
        ]);

        Livewire::actingAs($property->agent)->test(ManageProperties::class)
            ->call('startRefundRequest', $property->id)
            ->set('refundReason', 'The listing is no longer relevant')
            ->call('submitRefundRequest', $property->id);

        $refundRequest = RefundRequest::first();

        $this->assertNotNull($refundRequest);
        $this->assertEquals($payment->id, $refundRequest->payment_id);
        $this->assertEquals($property->id, $refundRequest->property_id);
        $this->assertEquals($property->agent_id, $refundRequest->agent_id);
        $this->assertEquals(RefundRequestStatus::Pending, $refundRequest->status);
        $this->assertEquals('The listing is no longer relevant', $refundRequest->reason);

        // Payment/listing must be untouched by merely requesting a refund.
        $this->assertEquals(PaymentStatus::Completed, $payment->fresh()->status);
        $this->assertTrue($property->fresh()->is_featured);
    }

    public function test_refund_amount_is_prorated_for_the_days_the_listing_has_already_been_featured(): void
    {
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(27)]);
        Payment::factory()->create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'status' => PaymentStatus::Completed,
            'amount' => 1_000_000,
            'featured_from' => now()->subDays(3),
            'featured_until' => now()->addDays(27),
        ]);

        Livewire::actingAs($property->agent)->test(ManageProperties::class)
            ->call('startRefundRequest', $property->id)
            ->set('refundReason', 'Only needed it for a few days')
            ->call('submitRefundRequest', $property->id);

        $refundRequest = RefundRequest::first();

        $this->assertNotNull($refundRequest);
        $this->assertSame(900_000, $refundRequest->refund_amount_cents);
    }

    public function test_agent_cannot_submit_a_second_pending_refund_request_for_the_same_payment(): void
    {
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'status' => PaymentStatus::Completed,
        ]);
        RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'reason' => 'First request',
            'status' => RefundRequestStatus::Pending,
        ]);

        Livewire::actingAs($property->agent)->test(ManageProperties::class)
            ->set('refundReason', 'Second request')
            ->call('submitRefundRequest', $property->id)
            ->assertSessionHas('error');

        $this->assertEquals(1, RefundRequest::count());
    }

    public function test_agent_cannot_request_a_refund_for_a_listing_that_is_not_featured(): void
    {
        $property = Property::factory()->create(['is_featured' => false, 'featured_until' => null]);
        Payment::factory()->create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'status' => PaymentStatus::Refunded,
        ]);

        Livewire::actingAs($property->agent)->test(ManageProperties::class)
            ->set('refundReason', 'Not featured anymore')
            ->call('submitRefundRequest', $property->id)
            ->assertSessionHas('error');

        $this->assertEquals(0, RefundRequest::count());
    }

    public function test_another_agent_cannot_request_a_refund_for_someone_elses_listing(): void
    {
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        Payment::factory()->create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'status' => PaymentStatus::Completed,
        ]);

        $otherAgent = User::factory()->agent()->create();

        Livewire::actingAs($otherAgent)->test(ManageProperties::class)
            ->set('refundReason', 'Not mine')
            ->call('submitRefundRequest', $property->id)
            ->assertForbidden();

        $this->assertEquals(0, RefundRequest::count());
    }
}
