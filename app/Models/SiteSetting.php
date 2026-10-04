<?php

namespace App\Models;

use App\Enums\Settings\Currency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
