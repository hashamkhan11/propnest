<?php

namespace App\Models;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $agent_id
 * @property string $title
 * @property string $description
 * @property numeric $price
 * @property PropertyType $property_type
 * @property int $bedrooms
 * @property int $bathrooms
 * @property numeric $area
 * @property string $address
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property PropertyStatus $status
 * @property bool $is_featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $featured_until
 * @property string|null $rejection_reason
 * @property string $city
 * @property PropertyPurpose $purpose
 * @property int $views_count
 * @property int $category_id
 * @property int $city_id
 * @property bool $has_been_published
 * @property-read User $agent
 * @property-read Collection<int, Amenity> $amenities
 * @property-read int|null $amenities_count
 * @property-read PropertyCategory $category
 * @property-read City $cityRecord
 * @property-read PropertyImage|null $coverImage
 * @property-read Collection<int, PropertyImage> $images
 * @property-read int|null $images_count
 * @property-read Collection<int, Inquiry> $inquiries
 * @property-read int|null $inquiries_count
 * @property-read Payment|null $latestFeaturedPayment
 * @property-read Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read Payment|null $pendingPayment
 * @property-read Collection<int, Report> $reports
 * @property-read int|null $reports_count
 *
 * @method static \Database\Factories\PropertyFactory factory($count = null, $state = [])
 */
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

    /** @return BelongsTo<User, $this> */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /** @return BelongsTo<PropertyCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    /**
     * Named cityRecord (not city) because `city` is already a real string
     * column on this model — Eloquent attribute access would always win over
     * a same-named relation method.
     *
     * @return BelongsTo<City, $this>
     */
    public function cityRecord(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /** @return HasMany<PropertyImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    /** @return HasOne<PropertyImage, $this> */
    public function coverImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class)->where('is_cover', true);
    }

    /** @return BelongsToMany<Amenity, $this> */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    /** @return HasMany<Inquiry, $this> */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /** @return HasMany<Report, $this> */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** @return HasOne<Payment, $this> */
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
     *
     * @return HasOne<Payment, $this>
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
