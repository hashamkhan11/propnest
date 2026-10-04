<?php

namespace App\Services\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Jobs\ExpireAgentSubscription;
use App\Models\AgentSubscription;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SubscriptionActivator
{
    /**
     * Mark an agent subscription as completed and start its active period,
     * exactly once. Safe to call more than once for the same subscription
     * (e.g. the webhook and the success-page fallback both racing to confirm
     * the same checkout).
     */
    public function activate(AgentSubscription $subscription, ?string $paymentIntentId = null): void
    {
        DB::transaction(function () use ($subscription, $paymentIntentId) {
            $locked = AgentSubscription::whereKey($subscription->id)->lockForUpdate()->first();

            if ($locked === null || $locked->status !== AgentSubscriptionStatus::Pending) {
                return;
            }

            $plan = $locked->plan()->firstOrFail();
            $expiresAt = now()->addDays($plan->duration_days);

            $locked->update([
                'status' => AgentSubscriptionStatus::Active,
                'stripe_payment_intent_id' => $paymentIntentId ?? $locked->stripe_payment_intent_id,
                'listing_limit' => $plan->listing_limit,
                'featured_credits_remaining' => $plan->featured_credits,
                'started_at' => now(),
                'expires_at' => $expiresAt,
            ]);

            $paymentUpdate = ['status' => PaymentStatus::Completed];

            if ($paymentIntentId !== null) {
                $paymentUpdate['stripe_payment_intent_id'] = $paymentIntentId;
            }

            Payment::where('agent_subscription_id', $locked->id)
                ->where('status', PaymentStatus::Pending)
                ->update($paymentUpdate);

            ExpireAgentSubscription::dispatch($locked)->delay($expiresAt);
        });
    }
}
