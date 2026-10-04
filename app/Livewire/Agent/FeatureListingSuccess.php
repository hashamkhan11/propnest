<?php

namespace App\Livewire\Agent;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use App\Services\Payment\FeaturedListingActivator;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class FeatureListingSuccess extends Component
{
    public Property $property;

    public Payment $payment;

    public bool $paymentConfirmed = false;

    public function mount(Property $property, FeaturedListingActivator $activator): void
    {
        $this->authorize('update', $property);

        $this->property = $property;

        $sessionId = request()->query('session_id');

        $this->payment = Payment::where('property_id', $property->id)
            ->where('stripe_checkout_session_id', $sessionId)
            ->firstOrFail();

        if ($this->payment->status === PaymentStatus::Completed) {
            $this->paymentConfirmed = true;
            $this->property->refresh();

            return;
        }

        if ($this->payment->status !== PaymentStatus::Pending) {
            // Already resolved as failed/cancelled/expired, most likely by the webhook.
            return;
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);

            return;
        }

        if ($session->payment_status === 'paid') {
            $activator->activate($this->payment, $session->payment_intent);

            $this->payment->refresh();
            $this->property->refresh();
            $this->paymentConfirmed = $this->payment->status === PaymentStatus::Completed;

            return;
        }

        if ($session->status === 'expired') {
            $this->payment->update(['status' => PaymentStatus::Expired]);
            $this->payment->refresh();
        }
    }

    public function render()
    {
        return view('livewire.agent.feature-listing-success');
    }
}
