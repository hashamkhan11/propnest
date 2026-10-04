<?php

namespace App\Services\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Jobs\UnfeatureListing;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class FeaturedListingActivator
{
    /**
     * Mark a payment as completed and feature its property, exactly once.
     *
     * Safe to call more than once for the same payment (e.g. the webhook and
     * the success-page fallback both racing to confirm the same checkout).
     */
    public function activate(Payment $payment, ?string $paymentIntentId = null): void
    {
        DB::transaction(function () use ($payment, $paymentIntentId) {
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->first();

            // Only activate a payment that is still awaiting confirmation. If it has
            // already been resolved — completed, cancelled by the agent, failed, or
            // expired — a late/duplicate "paid" signal must not resurrect it.
            if ($locked === null || $locked->status !== PaymentStatus::Pending) {
                return;
            }

            $locked->update([
                'status' => PaymentStatus::Completed,
                'stripe_payment_intent_id' => $paymentIntentId ?? $locked->stripe_payment_intent_id,
            ]);

            $property = $locked->property()->lockForUpdate()->first();

            $property->update([
                'is_featured' => true,
                'featured_until' => $locked->featured_until,
            ]);

            UnfeatureListing::dispatch($property)->delay($locked->featured_until);
        });
    }
}
