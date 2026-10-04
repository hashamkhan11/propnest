<?php

namespace App\Models;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use Carbon\CarbonInterface;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $property_id
 * @property int $agent_id
 * @property string|null $stripe_checkout_session_id
 * @property int $amount
 * @property PaymentStatus $status
 * @property Carbon|null $featured_until
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $stripe_payment_intent_id
 * @property string|null $failure_reason
 * @property int|null $featured_pricing_tier_id
 * @property Carbon|null $refunded_at
 * @property string|null $stripe_refund_id
 * @property Carbon|null $featured_from
 * @property int|null $agent_subscription_id
 * @property-read User $agent
 * @property-read AgentSubscription|null $agentSubscription
 * @property-read FeaturedPricingTier|null $featuredPricingTier
 * @property-read RefundRequest|null $latestRefundRequest
 * @property-read RefundRequest|null $pendingRefundRequest
 * @property-read Property|null $property
 * @property-read Collection<int, RefundRequest> $refundRequests
 * @property-read int|null $refund_requests_count
 *
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 */
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

    /** @return BelongsTo<Property, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /** @return BelongsTo<User, $this> */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /** @return BelongsTo<FeaturedPricingTier, $this> */
    public function featuredPricingTier(): BelongsTo
    {
        return $this->belongsTo(FeaturedPricingTier::class);
    }

    /** @return BelongsTo<AgentSubscription, $this> */
    public function agentSubscription(): BelongsTo
    {
        return $this->belongsTo(AgentSubscription::class);
    }

    public function isSubscriptionPayment(): bool
    {
        return $this->agent_subscription_id !== null;
    }

    /** @return HasMany<RefundRequest, $this> */
    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    /** @return HasOne<RefundRequest, $this> */
    public function pendingRefundRequest(): HasOne
    {
        return $this->hasOne(RefundRequest::class)
            ->where('status', RefundRequestStatus::Pending)
            ->latestOfMany();
    }

    /** @return HasOne<RefundRequest, $this> */
    public function latestRefundRequest(): HasOne
    {
        return $this->hasOne(RefundRequest::class)->latestOfMany();
    }
}
