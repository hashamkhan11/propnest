<?php

namespace App\Services\Payment;

use App\Models\Payment;
use Carbon\CarbonInterface;

class FeaturedListingRefundCalculator
{
    /**
     * Daily-prorated refund for the unused portion of a featured listing,
     * in cents. Frozen at $asOf — callers should pass the request time and
     * store the result rather than recomputing it later.
     */
    public function calculateCents(Payment $payment, CarbonInterface $asOf): int
    {
        $totalDays = $payment->featuredTotalDays();
        $remainingDays = $totalDays - $payment->featuredDaysUsed($asOf);

        return (int) round($payment->amount * $remainingDays / $totalDays);
    }
}
