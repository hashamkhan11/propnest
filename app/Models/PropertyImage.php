<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $property_id
 * @property string $path
 * @property bool $is_cover
 * @property int $sort_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $thumbnail_path
 * @property string|null $optimized_path
 * @property-read Property $property
 */
#[Fillable(['property_id', 'path', 'thumbnail_path', 'optimized_path', 'is_cover', 'sort_order'])]
class PropertyImage extends Model
{
    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
        ];
    }

    /** @return BelongsTo<Property, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * The path to use for small/card-sized displays: the generated thumbnail
     * when one exists, falling back to the original for images uploaded
     * before thumbnail generation existed.
     */
    public function thumbnailDisplayPath(): string
    {
        return $this->thumbnail_path ?? $this->path;
    }

    /**
     * The path to use for full-size gallery/detail displays: the optimized
     * (compressed) variant when one exists, falling back to the original
     * for images not yet processed or uploaded before optimization existed.
     */
    public function displayPath(): string
    {
        return $this->optimized_path ?? $this->path;
    }
}
