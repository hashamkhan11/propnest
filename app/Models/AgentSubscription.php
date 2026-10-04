<?php

namespace App\Models;

use App\Enums\Subscription\AgentSubscriptionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
