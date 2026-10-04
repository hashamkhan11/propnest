<?php

namespace App\Livewire\Agent;

use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Models\AgentSubscription;
use App\Services\Payment\SubscriptionActivator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

#[Layout('layouts.app')]
class SubscriptionSuccess extends Component
{
    public AgentSubscription $subscription;

    public bool $paymentConfirmed = false;

    public function mount(AgentSubscription $subscription, SubscriptionActivator $activator): void
    {
        abort_unless($subscription->agent_id === auth()->id(), 403);

        $this->subscription = $subscription;

        $sessionId = request()->query('session_id');

        abort_unless($sessionId !== null && $subscription->stripe_checkout_session_id === $sessionId, 404);

        if ($this->subscription->status === AgentSubscriptionStatus::Active) {
            $this->paymentConfirmed = true;

            return;
        }

        if ($this->subscription->status !== AgentSubscriptionStatus::Pending) {
            // Already resolved as failed/cancelled/expired, most likely by the webhook.
            return;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = Session::retrieve($sessionId);
        } catch (ApiErrorException $e) {
            report($e);

            return;
        }

        if ($session->payment_status === 'paid') {
            $activator->activate($this->subscription, $session->payment_intent);

            $this->subscription->refresh();
            $this->paymentConfirmed = $this->subscription->status === AgentSubscriptionStatus::Active;

            return;
        }

        if ($session->status === 'expired') {
            $this->subscription->update(['status' => AgentSubscriptionStatus::Expired]);
            $this->subscription->refresh();
        }
    }

    public function render()
    {
        return view('livewire.agent.subscription-success');
    }
}
