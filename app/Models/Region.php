<?php

namespace App\Models;

use Database\Factories\RegionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Region extends Model
{
    /** @use HasFactory<RegionFactory> */
    use HasFactory;

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
