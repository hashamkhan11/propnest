<?php

namespace App\Livewire\Agent;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyStatus;
use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Models\AgentSubscription;
use App\Models\Payment;
use App\Models\Property;
use App\Models\SubscriptionPlan;
use App\Services\Payment\SubscriptionActivator;
use App\Support\Settings;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

#[Layout('layouts.app')]
class SubscriptionPlans extends Component
{
    public function subscribe(int $planId): void
    {
        $plan = SubscriptionPlan::where('is_active', true)->findOrFail($planId);

        $blockedMessage = null;
        $subscription = null;
        $payment = null;

        DB::transaction(function () use ($plan, &$blockedMessage, &$subscription, &$payment) {
            $existing = AgentSubscription::where('agent_id', auth()->id())
                ->where('status', AgentSubscriptionStatus::Active)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if ($existing !== null) {
                $blockedMessage = 'You already have an active subscription.';

                return;
            }

            $pending = AgentSubscription::where('agent_id', auth()->id())
                ->where('status', AgentSubscriptionStatus::Pending)
                ->lockForUpdate()
                ->first();

            if ($pending !== null) {
                $blockedMessage = 'A subscription payment is already in progress. You can cancel it below to try again.';

                return;
            }

            $subscription = AgentSubscription::create([
                'agent_id' => auth()->id(),
                'subscription_plan_id' => $plan->id,
                'status' => AgentSubscriptionStatus::Pending,
                'amount_cents' => $plan->price_cents,
            ]);

            $payment = Payment::create([
                'agent_id' => auth()->id(),
                'agent_subscription_id' => $subscription->id,
                'amount' => $plan->price_cents,
                'status' => PaymentStatus::Pending,
            ]);
        });

        if ($blockedMessage !== null) {
            session()->flash('error', $blockedMessage);

            $this->redirect(route('agent.subscriptions.index'), navigate: true);

            return;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::create([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower(Settings::currency()->value),
                        'unit_amount' => $plan->price_cents,
                        'product_data' => [
                            'name' => "{$plan->name} subscription ({$plan->duration_days} days)",
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'agent_subscription_id' => (string) $subscription->id,
                    'agent_id' => (string) auth()->id(),
                ],
                'success_url' => route('agent.subscriptions.success', $subscription).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('agent.subscriptions.cancel', $subscription).'?session_id={CHECKOUT_SESSION_ID}',
                'expires_at' => now()->addMinutes(60)->timestamp,
            ], [
                'idempotency_key' => "agent-subscription-{$subscription->id}",
            ]);
        } catch (ApiErrorException $e) {
            report($e);

            $subscription->update([
                'status' => AgentSubscriptionStatus::Failed,
            ]);

            $payment->update([
                'status' => PaymentStatus::Failed,
                'failure_reason' => 'Unable to start Stripe checkout.',
            ]);

            session()->flash('error', 'Unable to start checkout right now. Please try again in a moment.');

            $this->redirect(route('agent.subscriptions.index'), navigate: true);

            return;
        }

        $subscription->update([
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
        ]);

        $payment->update([
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
        ]);

        $this->redirect($session->url, navigate: false);
    }

    /**
     * Let an agent unblock themselves after abandoning a Stripe Checkout page,
     * mirroring ManageProperties::cancelPendingPayment(). Verifies with Stripe
     * first so a payment that actually succeeded is activated rather than
     * discarded.
     */
    public function cancelPendingSubscription(SubscriptionActivator $activator): void
    {
        $subscription = AgentSubscription::where('agent_id', auth()->id())
            ->where('status', AgentSubscriptionStatus::Pending)
            ->latest()
            ->first();

        if ($subscription === null) {
            $this->redirect(route('agent.subscriptions.index'), navigate: true);

            return;
        }

        if ($subscription->stripe_checkout_session_id !== null) {
            Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $session = Session::retrieve($subscription->stripe_checkout_session_id);

                if ($session->payment_status === 'paid') {
                    $activator->activate($subscription, $session->payment_intent);

                    session()->flash('success', 'Your previous payment had already succeeded — your subscription is now active.');

                    $this->redirect(route('agent.subscriptions.index'), navigate: true);

                    return;
                }

                if ($session->status === 'open') {
                    $session->expire();
                }
            } catch (ApiErrorException $e) {
                report($e);
            }
        }

        AgentSubscription::where('id', $subscription->id)
            ->where('status', AgentSubscriptionStatus::Pending)
            ->update(['status' => AgentSubscriptionStatus::Cancelled]);

        Payment::where('agent_subscription_id', $subscription->id)
            ->where('status', PaymentStatus::Pending)
            ->update(['status' => PaymentStatus::Cancelled]);

        session()->flash('success', 'Pending subscription payment cancelled. You can subscribe again anytime.');

        $this->redirect(route('agent.subscriptions.index'), navigate: true);
    }

    public function render()
    {
        $agentId = auth()->id();

        return view('livewire.agent.subscription-plans', [
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get(),
            'activeSubscription' => AgentSubscription::with('plan')
                ->where('agent_id', $agentId)
                ->where('status', AgentSubscriptionStatus::Active)
                ->where('expires_at', '>', now())
                ->latest()
                ->first(),
            'pendingSubscription' => AgentSubscription::with('plan')
                ->where('agent_id', $agentId)
                ->where('status', AgentSubscriptionStatus::Pending)
                ->latest()
                ->first(),
            'activeListingCount' => Property::where('agent_id', $agentId)
                ->whereIn('status', [
                    PropertyStatus::Draft,
                    PropertyStatus::PendingReview,
                    PropertyStatus::Published,
                    PropertyStatus::UnderOffer,
                ])
                ->count(),
            'freeListingLimit' => Settings::freeListingLimit(),
        ]);
    }
}
