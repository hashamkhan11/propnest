<?php

namespace Tests\Feature\Console;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireStalePaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_expires_pending_payments_older_than_24_hours(): void
    {
        $property = Property::factory()->create();

        $stale = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_stale',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);
        $stale->forceFill(['created_at' => now()->subHours(30)])->save();

        $recent = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_recent',
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_until' => now()->addDays(30),
        ]);

        $completed = Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_old_completed',
            'amount' => 1000000,
            'status' => PaymentStatus::Completed,
            'featured_until' => now()->addDays(30),
        ]);
        $completed->forceFill(['created_at' => now()->subHours(30)])->save();

        $this->artisan('payments:expire-stale')->assertExitCode(0);

        $this->assertSame(PaymentStatus::Expired, $stale->fresh()->status);
        $this->assertSame(PaymentStatus::Pending, $recent->fresh()->status);
        $this->assertSame(PaymentStatus::Completed, $completed->fresh()->status);
    }
}
