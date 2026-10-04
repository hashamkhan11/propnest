<?php

namespace App\Livewire\Property;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Jobs\MatchSavedSearchesForProperty;
use App\Jobs\UnfeatureListing;
use App\Models\AgentSubscription;
use App\Models\FeaturedPricingTier;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RefundRequest;
use App\Services\Payment\FeaturedListingActivator;
use App\Services\Payment\FeaturedListingRefundCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManageProperties extends Component
{
    use WithPagination;

    /** @var array<int, string> */
    #[Url]
    public array $statuses = [];

    public ?int $requestingRefundPropertyId = null;

    public string $refundReason = '';

    public function clearFilter(): void
    {
        $this->statuses = [];
    }

    public function startRefundRequest(int $propertyId): void
    {
        $this->requestingRefundPropertyId = $propertyId;
        $this->refundReason = '';
    }

    public function cancelRefundRequest(): void
    {
        $this->requestingRefundPropertyId = null;
    }

    public function submitRefundRequest(Property $property): void
    {
        $this->authorize('update', $property);
        $this->validate(['refundReason' => 'required|string|max:1000']);

        $blockedMessage = null;

        DB::transaction(function () use ($property, &$blockedMessage) {
            $locked = Property::whereKey($property->id)->lockForUpdate()->firstOrFail();

            if (! $locked->is_featured || ! $locked->featured_until?->isFuture()) {
                $blockedMessage = 'This listing is not currently featured.';

                return;
            }

            $payment = Payment::where('property_id', $locked->id)
                ->where('status', PaymentStatus::Completed)
                ->where('featured_until', $locked->featured_until)
                ->latest()
                ->lockForUpdate()
                ->first();

            if ($payment === null) {
                $blockedMessage = 'No payment was found for this listing.';

                return;
            }

            $hasPendingRequest = RefundRequest::where('payment_id', $payment->id)
                ->where('status', RefundRequestStatus::Pending)
                ->exists();

            if ($hasPendingRequest) {
                $blockedMessage = 'A refund request for this listing is already pending.';

                return;
            }

            $hasRejectedRequest = RefundRequest::where('payment_id', $payment->id)
                ->where('status', RefundRequestStatus::Rejected)
                ->exists();

            if ($hasRejectedRequest) {
                $blockedMessage = 'A refund request for this payment was already rejected and cannot be resubmitted.';

                return;
            }

            $refundAmountCents = app(FeaturedListingRefundCalculator::class)->calculateCents($payment, now());

            RefundRequest::create([
                'payment_id' => $payment->id,
                'property_id' => $locked->id,
                'agent_id' => auth()->id(),
                'reason' => $this->refundReason,
                'refund_amount_cents' => $refundAmountCents,
                'status' => RefundRequestStatus::Pending,
            ]);
        });

        $this->requestingRefundPropertyId = null;

        if ($blockedMessage !== null) {
            session()->flash('error', $blockedMessage);
        } else {
            session()->flash('success', 'Refund request submitted. An admin will review it shortly.');
        }

        $this->redirect(route('agent.properties.index'), navigate: true);
    }

    public function feature(Property $property, int $tierId): void
    {
        $this->authorize('feature', $property);

        $tier = \App\Models\FeaturedPricingTier::where('is_active', true)->findOrFail($tierId);

        $blockedMessage = null;
        $payment = null;

        DB::transaction(function () use ($property, $tier, &$blockedMessage, &$payment) {
            $locked = Property::whereKey($property->id)->lockForUpdate()->firstOrFail();

            if ($locked->is_featured && $locked->featured_until?->isFuture()) {
                $blockedMessage = 'This listing is already featured.';

                return;
            }

            $pending = Payment::where('property_id', $locked->id)
                ->where('status', PaymentStatus::Pending)
                ->lockForUpdate()
                ->first();

            if ($pending !== null) {
                $blockedMessage = 'A payment for this listing is already in progress. You can cancel it below to try again.';

                return;
            }

            $maxFeatured = \App\Support\Settings::maxFeaturedListings();
            $activeFeaturedCount = Property::where('is_featured', true)->where('featured_until', '>', now())->lockForUpdate()->count();

            if ($maxFeatured !== null && $activeFeaturedCount >= $maxFeatured) {
                $blockedMessage = 'All featured slots are currently full. Please try again later.';

                return;
            }

            $payment = Payment::create([
                'property_id' => $locked->id,
                'agent_id' => auth()->id(),
                'featured_pricing_tier_id' => $tier->id,
                'stripe_checkout_session_id' => null,
                'amount' => $tier->price_cents,
                'status' => PaymentStatus::Pending,
                'featured_from' => now(),
                'featured_until' => now()->addDays($tier->duration_days),
            ]);
        });

        if ($blockedMessage !== null) {
            session()->flash('error', $blockedMessage);

            $this->redirect(route('agent.properties.index'), navigate: true);

            return;
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'pkr',
                        'unit_amount' => $tier->price_cents,
                        'product_data' => [
                            'name' => "Feature listing for {$tier->duration_days} days: {$property->title}",
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'property_id' => (string) $property->id,
                    'agent_id' => (string) auth()->id(),
                    'payment_id' => (string) $payment->id,
                ],
                'success_url' => route('agent.properties.feature.success', $property).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('agent.properties.feature.cancel', $property).'?session_id={CHECKOUT_SESSION_ID}',
                'expires_at' => now()->addMinutes(60)->timestamp,
            ], [
                'idempotency_key' => "feature-payment-{$payment->id}",
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            report($e);

            $payment->update([
                'status' => PaymentStatus::Failed,
                'failure_reason' => 'Unable to start Stripe checkout.',
            ]);

            session()->flash('error', 'Unable to start checkout right now. Please try again in a moment.');

            $this->redirect(route('agent.properties.index'), navigate: true);

            return;
        }

        $payment->update([
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => $session->payment_intent,
        ]);

        $this->redirect($session->url, navigate: false);
    }

    /**
     * Feature a listing using one of the agent's bundled subscription
     * featured credits instead of a Stripe payment. Uses the cheapest active
     * pricing tier's duration as the credit's featured period.
     */
    public function featureWithCredit(Property $property): void
    {
        $this->authorize('feature', $property);

        $blockedMessage = null;

        DB::transaction(function () use ($property, &$blockedMessage) {
            $locked = Property::whereKey($property->id)->lockForUpdate()->firstOrFail();

            if ($locked->is_featured && $locked->featured_until?->isFuture()) {
                $blockedMessage = 'This listing is already featured.';

                return;
            }

            $subscription = AgentSubscription::where('agent_id', auth()->id())
                ->where('status', AgentSubscriptionStatus::Active)
                ->where('expires_at', '>', now())
                ->where('featured_credits_remaining', '>', 0)
                ->lockForUpdate()
                ->first();

            if ($subscription === null) {
                $blockedMessage = 'You have no featured credits available.';

                return;
            }

            $maxFeatured = \App\Support\Settings::maxFeaturedListings();
            $activeFeaturedCount = Property::where('is_featured', true)->where('featured_until', '>', now())->lockForUpdate()->count();

            if ($maxFeatured !== null && $activeFeaturedCount >= $maxFeatured) {
                $blockedMessage = 'All featured slots are currently full. Please try again later.';

                return;
            }

            $durationDays = FeaturedPricingTier::where('is_active', true)->orderBy('sort_order')->value('duration_days') ?? 30;
            $featuredUntil = now()->addDays($durationDays);

            $subscription->decrement('featured_credits_remaining');

            $locked->update([
                'is_featured' => true,
                'featured_until' => $featuredUntil,
            ]);

            UnfeatureListing::dispatch($locked)->delay($featuredUntil);
        });

        if ($blockedMessage !== null) {
            session()->flash('error', $blockedMessage);
        } else {
            session()->flash('success', 'Listing featured using a subscription credit.');
        }

        $this->redirect(route('agent.properties.index'), navigate: true);
    }

    /**
     * Let an agent unblock themselves after abandoning a Stripe Checkout page
     * (closed the tab, never paid) instead of waiting for it to expire on its
     * own. Verifies with Stripe first so a payment that actually succeeded is
     * activated rather than discarded.
     */
    public function cancelPendingPayment(Property $property): void
    {
        $this->authorize('update', $property);

        $payment = Payment::where('property_id', $property->id)
            ->where('status', PaymentStatus::Pending)
            ->latest()
            ->first();

        if ($payment === null) {
            $this->redirect(route('agent.properties.index'), navigate: true);

            return;
        }

        if ($payment->stripe_checkout_session_id !== null) {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $session = \Stripe\Checkout\Session::retrieve($payment->stripe_checkout_session_id);

                if ($session->payment_status === 'paid') {
                    app(FeaturedListingActivator::class)->activate($payment, $session->payment_intent);

                    session()->flash('success', 'Your previous payment had already succeeded — this listing is now featured.');

                    $this->redirect(route('agent.properties.index'), navigate: true);

                    return;
                }

                if ($session->status === 'open') {
                    $session->expire();
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                report($e);
                // Don't let a Stripe API hiccup leave the agent stuck — fall through
                // and cancel the local payment record regardless.
            }
        }

        Payment::where('id', $payment->id)
            ->where('status', PaymentStatus::Pending)
            ->update(['status' => PaymentStatus::Cancelled]);

        session()->flash('success', 'Pending payment cancelled. You can feature this listing again.');

        $this->redirect(route('agent.properties.index'), navigate: true);
    }

    public function filterLabel(): string
    {
        return collect($this->statuses)
            ->map(fn (string $status) => Str::headline($status))
            ->implode(' / ');
    }

    public function transition(Property $property, string $status): void
    {
        $this->authorize('transitionStatus', $property);

        $target = PropertyStatus::from($status);
        $wasPublished = $property->status === PropertyStatus::Published;

        if (! $property->transitionTo($target)) {
            session()->flash('error', "Cannot move this listing from {$this->label($property->status)} to {$this->label($target)}.");

            $this->redirect(route('agent.properties.index'), navigate: true);

            return;
        }

        if (! $wasPublished && $target === PropertyStatus::Published) {
            MatchSavedSearchesForProperty::dispatch($property);
        }

        session()->flash('success', "Listing moved to {$this->label($target)}.");

        $this->redirect(route('agent.properties.index'), navigate: true);
    }

    /**
     * Re-submit a Sold/Rented/Archived listing for moderation without
     * re-entering the full new-listing form: chains the existing
     * status-machine transitions back to Draft then PendingReview.
     */
    public function relist(Property $property): void
    {
        $this->authorize('transitionStatus', $property);

        if (! in_array($property->status, [PropertyStatus::Sold, PropertyStatus::Rented, PropertyStatus::Archived], true)) {
            session()->flash('error', 'This listing cannot be re-listed from its current status.');

            $this->redirect(route('agent.properties.index'), navigate: true);

            return;
        }

        DB::transaction(function () use ($property) {
            $property->transitionTo(PropertyStatus::Draft);
            $property->transitionTo(PropertyStatus::PendingReview);
        });

        session()->flash('success', 'Listing submitted for re-review.');

        $this->redirect(route('agent.properties.index'), navigate: true);
    }

    public function delete(Property $property): void
    {
        $this->authorize('delete', $property);

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $property->delete();

        session()->flash('success', 'Listing deleted.');

        $this->redirect(route('agent.properties.index'), navigate: true);
    }

    private function label(PropertyStatus $status): string
    {
        return Str::headline($status->value);
    }

    public function render()
    {
        $properties = Property::query()
            ->where('agent_id', Auth::id())
            ->withCount('inquiries')
            ->with(['pendingPayment', 'coverImage', 'latestFeaturedPayment.latestRefundRequest'])
            ->when($this->statuses !== [], fn ($query) => $query->whereIn('status', $this->statuses))
            ->latest()
            ->paginate(10);

        return view('livewire.property.manage-properties', [
            'properties' => $properties,
            'activeTiers' => FeaturedPricingTier::where('is_active', true)->orderBy('sort_order')->get(),
            'activeSubscription' => AgentSubscription::where('agent_id', Auth::id())
                ->where('status', AgentSubscriptionStatus::Active)
                ->where('expires_at', '>', now())
                ->latest()
                ->first(),
        ]);
    }
}
