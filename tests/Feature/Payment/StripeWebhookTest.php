<?php

namespace Tests\Feature\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test_secret';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.webhook_secret' => self::SECRET]);
    }

    public function test_checkout_session_completed_activates_the_feature(): void
    {
        $property = Property::factory()->create(['is_featured' => false]);
        $featuredUntil = now()->addDays(30);

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_test_completed',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => $featuredUntil,
        ]);

        $payload = json_encode([
            'id' => 'evt_test_1',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_completed',
                    'object' => 'checkout.session',
                    'payment_intent' => 'pi_test_completed',
                    'payment_status' => 'paid',
                ],
            ],
        ]);

        $response = $this->postSignedWebhook($payload);

        $response->assertOk();

        $payment->refresh();
        $property->refresh();

        $this->assertSame(PaymentStatus::Completed, $payment->status);
        $this->assertSame('pi_test_completed', $payment->stripe_payment_intent_id);
        $this->assertTrue($property->is_featured);
        $this->assertSame($featuredUntil->timestamp, $property->featured_until->timestamp);
    }

    public function test_checkout_session_expired_marks_the_payment_expired(): void
    {
        $property = Property::factory()->create();

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_test_expired',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        $payload = json_encode([
            'id' => 'evt_test_2',
            'object' => 'event',
            'type' => 'checkout.session.expired',
            'data' => [
                'object' => [
                    'id' => 'cs_test_expired',
                    'object' => 'checkout.session',
                ],
            ],
        ]);

        $this->postSignedWebhook($payload)->assertOk();

        $this->assertSame(PaymentStatus::Expired, $payment->fresh()->status);
        $this->assertFalse($property->fresh()->is_featured);
    }

    public function test_payment_intent_payment_failed_marks_the_payment_failed_with_a_reason(): void
    {
        $property = Property::factory()->create();

        $payment = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_test_failed',
            'stripe_payment_intent_id' => 'pi_test_failed',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        $payload = json_encode([
            'id' => 'evt_test_3',
            'object' => 'event',
            'type' => 'payment_intent.payment_failed',
            'data' => [
                'object' => [
                    'id' => 'pi_test_failed',
                    'object' => 'payment_intent',
                    'last_payment_error' => ['message' => 'Your card was declined.'],
                ],
            ],
        ]);

        $this->postSignedWebhook($payload)->assertOk();

        $payment->refresh();

        $this->assertSame(PaymentStatus::Failed, $payment->status);
        $this->assertSame('Your card was declined.', $payment->failure_reason);
    }

    public function test_an_event_with_an_invalid_signature_is_rejected(): void
    {
        $payload = json_encode([
            'id' => 'evt_test_bad',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => ['object' => ['id' => 'cs_does_not_matter']],
        ]);

        $response = $this->call('POST', '/api/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => 't='.time().',v1=not-a-real-signature',
        ], $payload);

        $response->assertStatus(400);
    }

    private function postSignedWebhook(string $payload)
    {
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", self::SECRET);
        $header = "t={$timestamp},v1={$signature}";

        return $this->call('POST', '/api/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => $header,
        ], $payload);
    }
}
