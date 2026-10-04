<?php

namespace App\Models;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'agent_id', 'title', 'description', 'price', 'property_type', 'purpose',
    'bedrooms', 'bathrooms', 'area', 'address', 'city', 'latitude', 'longitude',
    'status', 'is_featured', 'featured_until', 'rejection_reason', 'category_id', 'city_id',
    'has_been_published',
])]
class Property extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'property_type' => PropertyType::class,
            'purpose' => PropertyPurpose::class,
            'status' => PropertyStatus::class,
            'is_featured' => 'boolean',
            'has_been_published' => 'boolean',
            'featured_until' => 'datetime',
            'price' => 'decimal:2',
            'area' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'views_count' => 'integer',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    /**
     * Named cityRecord (not city) because `city` is already a real string
     * column on this model — Eloquent attribute access would always win over
     * a same-named relation method.
     */
    public function cityRecord(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_cover', true);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function pendingPayment(): HasOne
    {
        return $this->hasOne(Payment::class)
            ->where('status', PaymentStatus::Pending)
            ->latestOfMany();
    }

    /**
     * Most recent featured-listing payment attempt for this property,
     * regardless of status — used to surface refund/rejection status even
     * after the property has been unfeatured (e.g. following a refund).
     */
    public function latestFeaturedPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function transitionTo(PropertyStatus $status): bool
    {
        if (! $this->status->canTransitionTo($status)) {
            return false;
        }

        $this->status = $status;

        return $this->save();
    }

    /**
     * Whether $newAttributes changes any field considered a "major" listing
     * detail (price, category/type, purpose, bedrooms, bathrooms, area, or
     * location) relative to this property's current, persisted values.
     */
    public function hasMajorChange(array $newAttributes): bool
    {
        foreach (['price', 'area', 'bedrooms', 'bathrooms'] as $field) {
            if ((float) $this->{$field} !== (float) $newAttributes[$field]) {
                return true;
            }
        }

        foreach (['category_id', 'city_id', 'address'] as $field) {
            if ((string) $this->{$field} !== (string) $newAttributes[$field]) {
                return true;
            }
        }

        return $this->purpose !== $newAttributes['purpose'];
    }
}
