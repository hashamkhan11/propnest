<?php

namespace App\Console\Commands;

use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Models\AgentSubscription;
use Illuminate\Console\Command;

class ExpireAgentSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire agent subscriptions whose period has ended (fallback for when the queued expiry job never ran).';

    public function handle(): int
    {
        $count = AgentSubscription::query()
            ->where('status', AgentSubscriptionStatus::Active)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->update(['status' => AgentSubscriptionStatus::Expired]);

        $this->info("Expired {$count} agent subscription(s).");

        return self::SUCCESS;
    }
}
