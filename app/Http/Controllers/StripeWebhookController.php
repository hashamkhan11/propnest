<?php

namespace App\Http\Controllers;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Models\AgentSubscription;
use App\Models\Payment;
use App\Services\Payment\FeaturedListingActivator;
use App\Services\Payment\SubscriptionActivator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, FeaturedListingActivator $activator, SubscriptionActivator $subscriptionActivator): Response
    {
        $secret = config('services.stripe.webhook_secret');

        if (! $secret) {
            report(new \RuntimeException('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured.'));

            return response('Webhook not configured', 500);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                $secret,
            );
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            report($e);

            return response('Invalid payload or signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event, $activator, $subscriptionActivator),
            'checkout.session.expired' => $this->handleCheckoutExpired($event),
            'payment_intent.payment_failed' => $this->handlePaymentFailed($event),
            default => null,
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted(Event $event, FeaturedListingActivator $activator, SubscriptionActivator $subscriptionActivator): void
    {
        $session = $event->data->object;

        if ($session->payment_status !== 'paid') {
            return;
        }

        $payment = Payment::where('stripe_checkout_session_id', $session->id)
            ->whereNull('agent_subscription_id')
            ->first();

        if ($payment !== null) {
            $activator->activate($payment, $session->payment_intent ?? null);

            return;
        }

        $subscription = AgentSubscription::where('stripe_checkout_session_id', $session->id)->first();

        if ($subscription !== null) {
            $subscriptionActivator->activate($subscription, $session->payment_intent ?? null);
        }
    }

    private function handleCheckoutExpired(Event $event): void
    {
        $session = $event->data->object;

        Payment::where('stripe_checkout_session_id', $session->id)
            ->where('status', PaymentStatus::Pending)
            ->update(['status' => PaymentStatus::Expired]);

        AgentSubscription::where('stripe_checkout_session_id', $session->id)
            ->where('status', AgentSubscriptionStatus::Pending)
            ->update(['status' => AgentSubscriptionStatus::Expired]);
    }

    private function handlePaymentFailed(Event $event): void
    {
        $intent = $event->data->object;
        $reason = $intent->last_payment_error->message ?? 'Payment failed.';

        Payment::where('stripe_payment_intent_id', $intent->id)
            ->where('status', PaymentStatus::Pending)
            ->update([
                'status' => PaymentStatus::Failed,
                'failure_reason' => $reason,
            ]);

        AgentSubscription::where('stripe_payment_intent_id', $intent->id)
            ->where('status', AgentSubscriptionStatus::Pending)
            ->update(['status' => AgentSubscriptionStatus::Failed]);
    }
}
