<?php

namespace Database\Factories;

use App\Enums\Payment\PaymentStatus;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $property = Property::factory()->create();

        return [
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'stripe_checkout_session_id' => 'cs_test_'.fake()->unique()->uuid(),
            'amount' => 1000000,
            'status' => PaymentStatus::Pending,
            'featured_from' => now(),
            'featured_until' => now()->addDays(30),
        ];
    }
}
