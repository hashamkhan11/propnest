<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'duration_days', 'price_cents', 'listing_limit', 'featured_credits', 'is_active', 'sort_order'])]
class SubscriptionPlan extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
