<?php

namespace Tests\Feature\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Jobs\NotifyAgentOfRefund;
use App\Livewire\Admin\RefundRequestsQueue;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Livewire\Livewire;
use Stripe\ApiRequestor;
use Stripe\Exception\ApiConnectionException;
use Stripe\HttpClient\ClientInterface;
use Tests\TestCase;

class PaymentRefundTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);

        parent::tearDown();
    }

    private function fakeSuccessfulStripeRefund(): void
    {
        ApiRequestor::setHttpClient(new class implements ClientInterface
        {
            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                $body = ['id' => 're_test_123', 'object' => 'refund', 'status' => 'succeeded'];

                return [json_encode($body), 200, []];
            }
        });
    }

    public function test_admin_can_approve_a_pending_refund_request_and_it_processes_the_stripe_refund(): void
    {
        Bus::fake();

        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'status' => PaymentStatus::Completed,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);
        $refundRequest = RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $property->id,
            'agent_id' => $payment->agent_id,
            'reason' => 'No longer needed',
            'status' => RefundRequestStatus::Pending,
        ]);

        $this->fakeSuccessfulStripeRefund();

        Livewire::actingAs($admin)->test(RefundRequestsQueue::class)
            ->call('approve', $refundRequest->id);

        $payment->refresh();
        $refundRequest->refresh();

        $this->assertEquals(PaymentStatus::Refunded, $payment->status);
        $this->assertNotNull($payment->refunded_at);
        $this->assertEquals('re_test_123', $payment->stripe_refund_id);
        $this->assertFalse($property->fresh()->is_featured);
        $this->assertEquals(RefundRequestStatus::Approved, $refundRequest->status);
        $this->assertEquals($admin->id, $refundRequest->reviewed_by_user_id);

        Bus::assertDispatched(NotifyAgentOfRefund::class);
    }

    public function test_stripe_refund_uses_the_prorated_amount_not_the_full_payment_amount(): void
    {
        Bus::fake();

        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(27)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'status' => PaymentStatus::Completed,
            'stripe_payment_intent_id' => 'pi_test_123',
            'amount' => 1_000_000,
            'featured_from' => now()->subDays(3),
            'featured_until' => now()->addDays(27),
        ]);
        $refundRequest = RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $property->id,
            'agent_id' => $payment->agent_id,
            'reason' => 'Only needed it briefly',
            'refund_amount_cents' => 900_000,
            'status' => RefundRequestStatus::Pending,
        ]);

        $capture = new class
        {
            public ?array $params = null;
        };
        ApiRequestor::setHttpClient(new class($capture) implements ClientInterface
        {
            public function __construct(private object $capture) {}

            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                $this->capture->params = $params;
                $body = ['id' => 're_test_123', 'object' => 'refund', 'status' => 'succeeded'];

                return [json_encode($body), 200, []];
            }
        });

        Livewire::actingAs($admin)->test(RefundRequestsQueue::class)
            ->call('approve', $refundRequest->id);

        $this->assertSame(900_000, $capture->params['amount'] ?? null);
        $this->assertSame(1_000_000, $payment->amount);
    }

    public function test_admin_can_reject_a_pending_refund_request_and_payment_is_unchanged(): void
    {
        Bus::fake();

        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'status' => PaymentStatus::Completed,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);
        $refundRequest = RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $property->id,
            'agent_id' => $payment->agent_id,
            'reason' => 'Changed my mind',
            'status' => RefundRequestStatus::Pending,
        ]);

        Livewire::actingAs($admin)->test(RefundRequestsQueue::class)
            ->call('startReject', $refundRequest->id)
            ->set('rejectionNote', 'Listing already generated leads')
            ->call('reject', $refundRequest->id);

        $refundRequest->refresh();

        $this->assertEquals(RefundRequestStatus::Rejected, $refundRequest->status);
        $this->assertEquals('Listing already generated leads', $refundRequest->admin_notes);
        $this->assertEquals(PaymentStatus::Completed, $payment->fresh()->status);
        $this->assertTrue($property->fresh()->is_featured);

        Bus::assertNotDispatched(NotifyAgentOfRefund::class);
    }

    public function test_failed_stripe_refund_leaves_payment_and_listing_unchanged(): void
    {
        Bus::fake();

        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'status' => PaymentStatus::Completed,
            'stripe_payment_intent_id' => 'pi_test_123',
        ]);
        $refundRequest = RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $property->id,
            'agent_id' => $payment->agent_id,
            'reason' => 'No longer needed',
            'status' => RefundRequestStatus::Pending,
        ]);

        ApiRequestor::setHttpClient(new class implements ClientInterface
        {
            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                throw new ApiConnectionException('Network error');
            }
        });

        Livewire::actingAs($admin)->test(RefundRequestsQueue::class)
            ->call('approve', $refundRequest->id)
            ->assertDispatched('toast', fn (string $name, array $params) => $params['type'] === 'error');

        $payment->refresh();
        $refundRequest->refresh();

        $this->assertEquals(PaymentStatus::Completed, $payment->status);
        $this->assertNull($payment->stripe_refund_id);
        $this->assertTrue($property->fresh()->is_featured);
        $this->assertEquals(RefundRequestStatus::Pending, $refundRequest->status);

        Bus::assertNotDispatched(NotifyAgentOfRefund::class);
    }

    public function test_cannot_approve_an_already_reviewed_refund_request(): void
    {
        Bus::fake();

        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['is_featured' => true, 'featured_until' => now()->addDays(10)]);
        $payment = Payment::factory()->create([
            'property_id' => $property->id,
            'status' => PaymentStatus::Refunded,
            'stripe_payment_intent_id' => 'pi_test_123',
            'stripe_refund_id' => 're_already_done',
        ]);
        $refundRequest = RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $property->id,
            'agent_id' => $payment->agent_id,
            'status' => RefundRequestStatus::Approved,
            'reviewed_by_user_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        Livewire::actingAs($admin)->test(RefundRequestsQueue::class)
            ->call('approve', $refundRequest->id);

        Bus::assertNotDispatched(NotifyAgentOfRefund::class);
    }
}
