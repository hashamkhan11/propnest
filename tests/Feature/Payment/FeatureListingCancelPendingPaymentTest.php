<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Livewire\Property\ManageProperties;
use App\Models\FeaturedPricingTier;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Tests\TestCase;

class FeatureListingCancelPendingPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);

        parent::tearDown();
    }

    public function test_agent_can_cancel_an_abandoned_pending_payment_and_retry(): void
    {
        $property = Property::factory()->create();

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            // Not a real Stripe session — Stripe will report it as unknown, which
            // must fall through to cancelling locally rather than leaving the
            // agent stuck (this is exactly the "closed the tab, never paid" case).
            'stripe_checkout_session_id' => 'cs_test_abandoned_never_existed',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('cancelPendingPayment', $property->id);

        $this->assertSame(PaymentStatus::Cancelled, $payment->fresh()->status);
        $this->assertSame('Pending payment cancelled. You can feature this listing again.', session('success'));

        // The listing is no longer blocked: a fresh feature() attempt is allowed
        // to create a new payment instead of hitting the "already in progress" guard.
        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('feature', $property->id, FeaturedPricingTier::first()->id);

        $this->assertNotSame(
            'A payment for this listing is already in progress. You can cancel it below to try again.',
            session('error')
        );
        $this->assertSame(2, Payment::where('property_id', $property->id)->count());
    }

    public function test_agent_can_cancel_a_still_open_checkout_session(): void
    {
        $property = Property::factory()->create();

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_test_still_open',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        ApiRequestor::setHttpClient(new class implements ClientInterface
        {
            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                if ($method === 'get') {
                    $body = [
                        'id' => 'cs_test_still_open',
                        'object' => 'checkout.session',
                        'status' => 'open',
                        'payment_status' => 'unpaid',
                    ];
                } else {
                    $body = [
                        'id' => 'cs_test_still_open',
                        'object' => 'checkout.session',
                        'status' => 'expired',
                        'payment_status' => 'unpaid',
                    ];
                }

                return [json_encode($body), 200, []];
            }
        });

        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('cancelPendingPayment', $property->id);

        $this->assertSame(PaymentStatus::Cancelled, $payment->fresh()->status);
        $this->assertSame('Pending payment cancelled. You can feature this listing again.', session('success'));
    }

    public function test_cancelling_with_no_pending_payment_is_a_safe_no_op(): void
    {
        $property = Property::factory()->create();

        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('cancelPendingPayment', $property->id);

        $this->assertSame(0, Payment::where('property_id', $property->id)->count());
    }

    public function test_cancel_does_not_touch_an_already_completed_payment(): void
    {
        $property = Property::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->addDays(30),
        ]);

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_test_already_completed',
            'amount' => 1000000,
            'status' => PaymentStatus::Completed,
            'featured_until' => now()->addDays(30),
        ]);

        Livewire::actingAs($property->agent)
            ->test(ManageProperties::class)
            ->call('cancelPendingPayment', $property->id);

        $this->assertSame(PaymentStatus::Completed, $payment->fresh()->status);
    }
}
