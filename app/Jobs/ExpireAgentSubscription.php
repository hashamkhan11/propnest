<?php

namespace App\Jobs;

use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Models\AgentSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireAgentSubscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public AgentSubscription $subscription) {}

    public function handle(): void
    {
        $subscription = $this->subscription->fresh();

        if ($subscription === null) {
            return;
        }

        if ($subscription->status === AgentSubscriptionStatus::Active && $subscription->expires_at !== null && $subscription->expires_at->lessThanOrEqualTo(now())) {
            $subscription->update(['status' => AgentSubscriptionStatus::Expired]);
        }
    }
}
