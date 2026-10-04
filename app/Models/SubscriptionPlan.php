<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int $duration_days
 * @property int $price_cents
 * @property int|null $listing_limit
 * @property int $featured_credits
 * @property bool $is_active
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
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
