<?php

namespace App\Console\Commands;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use Illuminate\Console\Command;

class ExpireStalePayments extends Command
{
    protected $signature = 'payments:expire-stale';

    protected $description = 'Mark long-pending featured-listing payments as expired (fallback for missed checkout.session.expired webhooks or abandoned checkouts).';

    /**
     * Slightly longer than the 60-minute expires_at set on Checkout Sessions
     * (see ManageProperties::feature()), so this only catches strays that the
     * webhook missed rather than racing it under normal conditions.
     */
    private const STALE_AFTER_MINUTES = 75;

    public function handle(): int
    {
        $count = Payment::query()
            ->where('status', PaymentStatus::Pending)
            ->where('created_at', '<=', now()->subMinutes(self::STALE_AFTER_MINUTES))
            ->update(['status' => PaymentStatus::Expired]);

        $this->info("Expired {$count} stale pending payment(s).");

        return self::SUCCESS;
    }
}
