<?php

namespace App\Models;

use App\Enums\Subscription\AgentSubscriptionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $agent_id
 * @property int $subscription_plan_id
 * @property AgentSubscriptionStatus $status
 * @property string|null $stripe_checkout_session_id
 * @property string|null $stripe_payment_intent_id
 * @property int $amount_cents
 * @property int|null $listing_limit
 * @property int $featured_credits_remaining
 * @property Carbon|null $started_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $agent
 * @property-read SubscriptionPlan $plan
 */
#[Fillable(['agent_id', 'subscription_plan_id', 'status', 'stripe_checkout_session_id', 'stripe_payment_intent_id', 'amount_cents', 'listing_limit', 'featured_credits_remaining', 'started_at', 'expires_at'])]
class AgentSubscription extends Model
{
    protected function casts(): array
    {
        return [
            'status' => AgentSubscriptionStatus::class,
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === AgentSubscriptionStatus::Active && $this->expires_at?->isFuture();
    }

    /** @return BelongsTo<User, $this> */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /** @return BelongsTo<SubscriptionPlan, $this> */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
