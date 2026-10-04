<?php

namespace Database\Factories;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Models\Payment;
use App\Models\RefundRequest;
use App\Services\Payment\FeaturedListingRefundCalculator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RefundRequest>
 */
class RefundRequestFactory extends Factory
{
    protected $model = RefundRequest::class;

    public function definition(): array
    {
        $payment = Payment::factory()->create(['status' => PaymentStatus::Completed]);

        return [
            'payment_id' => $payment->id,
            'property_id' => $payment->property_id,
            'agent_id' => $payment->agent_id,
            'reason' => fake()->sentence(),
            'refund_amount_cents' => app(FeaturedListingRefundCalculator::class)->calculateCents($payment, now()),
            'status' => RefundRequestStatus::Pending,
        ];
    }
}
