<?php

namespace App\Livewire\Property;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Enums\User\UserRole;
use App\Jobs\ProcessPropertyImage;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Support\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class PropertyForm extends Component
{
    use WithFileUploads;

    public ?Property $property = null;

    public string $title = '';

    public string $description = '';

    public string $price = '';

    public string $categoryId = '';

    public string $purpose = PropertyPurpose::ForSale->value;

    public string $bedrooms = '';

    public string $bathrooms = '';

    public string $area = '';

    public string $address = '';

    public string $cityId = '';

    public ?string $latitude = null;

    public ?string $longitude = null;

    /** @var array<int, int> */
    public array $selectedAmenities = [];

    public $coverImage;

    /** @var array<int, mixed> */
    public array $galleryImages = [];

    public function mount(?Property $property = null): void
    {
        if ($property?->exists) {
            $this->authorize('update', $property);

            $this->property = $property;
            $this->title = $property->title;
            $this->description = $property->description;
            $this->price = (string) $property->price;
            $this->categoryId = (string) $property->category_id;
            $this->purpose = $property->purpose->value;
            $this->bedrooms = (string) $property->bedrooms;
            $this->bathrooms = (string) $property->bathrooms;
            $this->area = (string) $property->area;
            $this->address = $property->address;
            $this->cityId = (string) $property->city_id;
            $this->latitude = $property->latitude !== null ? (string) $property->latitude : null;
            $this->longitude = $property->longitude !== null ? (string) $property->longitude : null;
            $this->selectedAmenities = $property->amenities->pluck('id')->all();
        } else {
            $this->authorize('create', Property::class);

            $limit = $this->activeListingLimit();

            if ($limit !== null && $this->activeListingCount() >= $limit) {
                session()->flash('error', "You've reached your active listing limit ({$limit}). Subscribe to a plan for more capacity.");

                $this->redirect(route('agent.subscriptions.index'), navigate: true);
            }
        }
    }

    private function activeListingCount(): int
    {
        return Property::where('agent_id', auth()->id())
            ->whereIn('status', [
                PropertyStatus::Draft,
                PropertyStatus::PendingReview,
                PropertyStatus::Published,
                PropertyStatus::UnderOffer,
            ])
            ->count();
    }

    /**
     * The agent's current active-listing cap: their subscription's limit if
     * they have one (null = unlimited), otherwise the site-wide free-tier
     * default (also null = unlimited).
     */
    private function activeListingLimit(): ?int
    {
        $subscription = auth()->user()->activeAgentSubscription;

        if ($subscription !== null) {
            return $subscription->listing_limit;
        }

        return Settings::freeListingLimit();
    }

    /**
     * Property types for which bedrooms/bathrooms don't apply (e.g. vacant
     * land or a commercial unit), so the fields become optional and hidden.
     */
    private function roomsNotApplicable(): bool
    {
        if ($this->categoryId === '') {
            return false;
        }

        $slug = PropertyCategory::find($this->categoryId)?->slug;

        return in_array($slug, [PropertyType::Land->value, PropertyType::Commercial->value], true);
    }

    protected function rules(): array
    {
        $roomsRule = $this->roomsNotApplicable() ? 'nullable|integer|min:0' : 'required|integer|min:0';

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'categoryId' => 'required|exists:property_categories,id',
            'purpose' => 'required|in:'.implode(',', array_column(PropertyPurpose::cases(), 'value')),
            'bedrooms' => $roomsRule,
            'bathrooms' => $roomsRule,
            'area' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'cityId' => 'required|exists:cities,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'selectedAmenities' => 'array',
            'selectedAmenities.*' => 'exists:amenities,id',
            'coverImage' => ($this->property ? 'nullable' : 'required').'|image|max:5120',
            'galleryImages' => 'array',
            'galleryImages.*' => 'image|max:5120',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $category = PropertyCategory::findOrFail($this->categoryId);
        $city = City::findOrFail($this->cityId);

        $attributes = [
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $category->id,
            'property_type' => PropertyType::tryFrom($category->slug) ?? PropertyType::Other,
            'purpose' => PropertyPurpose::from($this->purpose),
            'bedrooms' => $this->bedrooms !== '' ? $this->bedrooms : 0,
            'bathrooms' => $this->bathrooms !== '' ? $this->bathrooms : 0,
            'area' => $this->area,
            'address' => $this->address,
            'city_id' => $city->id,
            'city' => $city->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        $needsReReview = false;

        if (
            $this->property
            && $this->property->status === PropertyStatus::Published
            && auth()->user()->role !== UserRole::Admin
        ) {
            $imagesChanged = $this->coverImage !== null || count($this->galleryImages) > 0;

            $needsReReview = $imagesChanged || $this->property->hasMajorChange($attributes);
        }

        if ($needsReReview) {
            $attributes['status'] = PropertyStatus::PendingReview;
        }

        DB::transaction(function () use ($attributes) {
            if ($this->property) {
                $this->property->update($attributes);
                $property = $this->property;
            } else {
                $attributes['agent_id'] = auth()->id();
                $property = Property::create($attributes);
            }

            $property->amenities()->sync($this->selectedAmenities);

            if ($this->coverImage) {
                $oldCover = $property->images()->where('is_cover', true)->first();

                if ($oldCover) {
                    Storage::disk('public')->delete(array_filter([$oldCover->path, $oldCover->thumbnail_path, $oldCover->optimized_path]));
                    $oldCover->delete();
                }

                $path = $this->coverImage->store('properties', 'public');

                $coverImage = $property->images()->create([
                    'path' => $path,
                    'is_cover' => true,
                    'sort_order' => 0,
                ]);

                ProcessPropertyImage::dispatch($coverImage);
            }

            $nextSortOrder = (int) $property->images()->max('sort_order') + 1;

            foreach ($this->galleryImages as $image) {
                $path = $image->store('properties', 'public');

                $galleryImage = $property->images()->create([
                    'path' => $path,
                    'is_cover' => false,
                    'sort_order' => $nextSortOrder++,
                ]);

                ProcessPropertyImage::dispatch($galleryImage);
            }
        });

        $message = match (true) {
            $needsReReview => 'Listing updated. Because you changed major details, it has been sent back for admin approval and is hidden from public view until then.',
            (bool) $this->property => 'Listing updated.',
            default => 'Listing created as a draft.',
        };

        session()->flash('success', $message);

        $this->redirect(
            auth()->user()->role === UserRole::Admin ? route('admin.properties.index') : route('agent.properties.index'),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.property.property-form', [
            'categories' => PropertyCategory::where('is_active', true)->orderBy('name')->get(),
            'purposes' => PropertyPurpose::cases(),
            'amenities' => Amenity::orderBy('name')->get(),
            'citiesByRegion' => City::with('region')->orderBy('name')->get()->groupBy(fn ($c) => $c->region->name),
        ]);
    }
}
