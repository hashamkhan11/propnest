<?php

namespace App\Models;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use Carbon\CarbonInterface;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['property_id', 'agent_id', 'featured_pricing_tier_id', 'agent_subscription_id', 'stripe_checkout_session_id', 'stripe_payment_intent_id', 'amount', 'status', 'failure_reason', 'featured_from', 'featured_until', 'refunded_at', 'stripe_refund_id'])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'featured_from' => 'datetime',
            'featured_until' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Full length of the featured period, in whole days. Floored at 1 to
     * keep proration math safe even if a tier's duration is ever 0.
     */
    public function featuredTotalDays(): int
    {
        return max(1, (int) $this->featured_from->diffInDays($this->featured_until));
    }

    /**
     * Whole days elapsed since the featured period started, clamped to
     * [0, featuredTotalDays()] so it never overshoots the plan length.
     */
    public function featuredDaysUsed(CarbonInterface $asOf): int
    {
        $elapsed = (int) $this->featured_from->diffInDays($asOf, absolute: false);

        return max(0, min($this->featuredTotalDays(), $elapsed));
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function featuredPricingTier(): BelongsTo
    {
        return $this->belongsTo(FeaturedPricingTier::class);
    }

    public function agentSubscription(): BelongsTo
    {
        return $this->belongsTo(AgentSubscription::class);
    }

    public function isSubscriptionPayment(): bool
    {
        return $this->agent_subscription_id !== null;
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function pendingRefundRequest(): HasOne
    {
        return $this->hasOne(RefundRequest::class)
            ->where('status', RefundRequestStatus::Pending)
            ->latestOfMany();
    }

    public function latestRefundRequest(): HasOne
    {
        return $this->hasOne(RefundRequest::class)->latestOfMany();
    }
}
