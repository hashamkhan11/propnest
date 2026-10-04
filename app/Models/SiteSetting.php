<?php

namespace App\Models;

use App\Enums\Settings\Currency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Currency $currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $max_featured_listings
 * @property int|null $free_listing_limit
 */
#[Fillable(['currency', 'max_featured_listings', 'free_listing_limit'])]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'currency' => Currency::class,
        ];
    }
}
