<?php

namespace Tests\Unit\Services\Payment;

use App\Models\Payment;
use App\Services\Payment\FeaturedListingRefundCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FeaturedListingRefundCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function paymentStartingNow(int $amountCents = 1_000_000, int $durationDays = 30): Payment
    {
        $start = Carbon::parse('2026-07-01 09:00:00');

        return Payment::factory()->create([
            'amount' => $amountCents,
            'featured_from' => $start,
            'featured_until' => $start->copy()->addDays($durationDays),
        ]);
    }

    public function test_full_refund_when_no_days_have_elapsed(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_from);

        $this->assertSame(1_000_000, $cents);
    }

    public function test_matches_worked_example_after_one_day(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_from->copy()->addDay());

        $this->assertSame(966_667, $cents);
    }

    public function test_matches_worked_example_after_two_days(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_from->copy()->addDays(2));

        $this->assertSame(933_333, $cents);
    }

    public function test_matches_worked_example_after_three_days(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_from->copy()->addDays(3));

        $this->assertSame(900_000, $cents);
    }

    public function test_small_refund_on_the_last_day_before_expiry(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_from->copy()->addDays(29));

        $this->assertSame(33_333, $cents);
    }

    public function test_elapsed_days_clamp_at_zero_when_asked_before_the_start(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_from->copy()->subDay());

        $this->assertSame(1_000_000, $cents);
    }

    public function test_elapsed_days_clamp_at_total_when_asked_past_expiry(): void
    {
        $payment = $this->paymentStartingNow();

        $cents = (new FeaturedListingRefundCalculator)->calculateCents($payment, $payment->featured_until->copy()->addDays(5));

        $this->assertSame(0, $cents);
    }
}
