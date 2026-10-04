<?php

namespace Database\Factories;

use App\Enums\Report\ReportReason;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'reported_by_user_id' => User::factory(),
            'reason' => ReportReason::Spam,
            'details' => null,
        ];
    }
}
