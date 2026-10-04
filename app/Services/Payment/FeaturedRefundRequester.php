<?php

namespace App\Services\Payment;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Exceptions\RefundRequestBlocked;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RefundRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FeaturedRefundRequester
{
    public function __construct(private FeaturedListingRefundCalculator $calculator) {}

    /**
     * Opens a refund request for the payment behind a listing's current
     * featured placement. The amount is prorated and frozen now, so the admin
     * approves what the agent saw.
     *
     * @throws RefundRequestBlocked when the listing or payment isn't eligible
     */
    public function request(Property $property, User $agent, string $reason): RefundRequest
    {
        return DB::transaction(function () use ($property, $agent, $reason) {
            // Lock the listing and its payment so two clicks can't open two requests.
            $locked = Property::whereKey($property->id)->lockForUpdate()->firstOrFail();

            if (! $locked->is_featured || ! $locked->featured_until?->isFuture()) {
                throw new RefundRequestBlocked('This listing is not currently featured.');
            }

            $payment = Payment::where('property_id', $locked->id)
                ->where('status', PaymentStatus::Completed)
                ->where('featured_until', $locked->featured_until)
                ->latest()
                ->lockForUpdate()
                ->first();

            if ($payment === null) {
                throw new RefundRequestBlocked('No payment was found for this listing.');
            }

            $existing = RefundRequest::where('payment_id', $payment->id)
                ->whereIn('status', [RefundRequestStatus::Pending, RefundRequestStatus::Rejected])
                ->pluck('status');

            if ($existing->contains(RefundRequestStatus::Pending)) {
                throw new RefundRequestBlocked('A refund request for this listing is already pending.');
            }

            if ($existing->contains(RefundRequestStatus::Rejected)) {
                throw new RefundRequestBlocked('A refund request for this payment was already rejected and cannot be resubmitted.');
            }

            return RefundRequest::create([
                'payment_id' => $payment->id,
                'property_id' => $locked->id,
                'agent_id' => $agent->id,
                'reason' => $reason,
                'refund_amount_cents' => $this->calculator->calculateCents($payment, now()),
                'status' => RefundRequestStatus::Pending,
            ]);
        });
    }
}
