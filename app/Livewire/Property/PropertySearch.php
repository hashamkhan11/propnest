<?php

namespace App\Livewire\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyType;
use App\Enums\User\UserRole;
use App\Models\Amenity;
use App\Models\PropertyCategory;
use App\Models\SavedSearch;
use App\Services\Property\PropertySearchService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class PropertySearch extends Component
{
    use WithPagination;

    #[Url]
    public string $keyword = '';

    #[Url]
    public string $location = '';

    #[Url]
    public string $minPrice = '';

    #[Url]
    public string $maxPrice = '';

    #[Url]
    public string $propertyType = '';

    #[Url]
    public string $purpose = '';

    #[Url]
    public string $bedrooms = '';

    #[Url]
    public string $bathrooms = '';

    #[Url]
    public string $minArea = '';

    /** @var array<int, int> */
    #[Url]
    public array $amenityIds = [];

    #[Url]
    public string $sort = 'newest';

    #[Url]
    public bool $featured = false;

    public string $viewMode = 'grid';

    public ?array $mapBounds = null;

    public ?array $mapRadius = null;

    public ?array $mapPolygon = null;

    public function updating(string $name): void
    {
        if ($name !== 'page') {
            $this->resetPage();
        }
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode === 'map' ? 'map' : 'grid';
        $this->resetPage();
    }

    public function searchThisArea(array $bounds): void
    {
        $this->mapBounds = $bounds;
        $this->mapRadius = null;
        $this->mapPolygon = null;
        $this->resetPage();
    }

    public function applyRadiusSearch(array $center, float $km): void
    {
        $this->mapRadius = ['lat' => $center['lat'], 'lng' => $center['lng'], 'km' => min(max($km, 1.0), 50.0)];
        $this->mapBounds = null;
        $this->mapPolygon = null;
        $this->resetPage();
    }

    public function applyPolygonSearch(array $points): void
    {
        $this->mapPolygon = array_slice($points, 0, 100);
        $this->mapBounds = null;
        $this->mapRadius = null;
        $this->resetPage();
    }

    public function clearGeoSearch(): void
    {
        $this->mapBounds = null;
        $this->mapRadius = null;
        $this->mapPolygon = null;
        $this->resetPage();
    }

    public function saveSearch(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === UserRole::Buyer, 403);

        $filters = [
            'keyword' => $this->keyword,
            'location' => $this->location,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'propertyType' => $this->propertyType,
            'purpose' => $this->purpose,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'minArea' => $this->minArea,
            'amenityIds' => collect($this->amenityIds)->sort()->values()->all(),
        ];

        // Query fresh rather than the cached `savedSearches` relation property:
        // within a single authenticated session (e.g. this component handling
        // repeated actions), Eloquent caches the relation on first access, so a
        // second save in that same lifecycle would otherwise compare against a
        // stale, pre-creation snapshot and let a duplicate slip through.
        $alreadySaved = auth()->user()->savedSearches()->get()
            ->contains(fn (SavedSearch $saved) => $saved->filters === $filters);

        if ($alreadySaved) {
            $this->dispatch('toast', type: 'error', message: "You've already saved this search.");

            return;
        }

        auth()->user()->savedSearches()->create(['filters' => $filters]);

        $this->dispatch('toast', type: 'success', message: 'Search saved! Manage it from My Saved Searches.');
    }

    public function render()
    {
        $filters = PropertySearchFilters::fromArray([
            'keyword' => $this->keyword,
            'location' => $this->location,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'propertyType' => $this->propertyType,
            'purpose' => $this->purpose,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'minArea' => $this->minArea,
            'amenityIds' => $this->amenityIds,
            'sort' => $this->sort,
            'featured' => $this->featured,
            'viewMode' => $this->viewMode,
            'bounds' => $this->mapBounds,
            'radius' => $this->mapRadius,
            'polygon' => $this->mapPolygon,
        ]);

        // Filter options must stay in sync with admin-managed categories: a
        // type only appears here while a matching, active PropertyCategory
        // row exists (deleting a category is blocked while properties still
        // use it, so this can't orphan an already-selected filter). "Other"
        // is the fixed catch-all bucket, not backed by any single category.
        $activeCategorySlugs = PropertyCategory::where('is_active', true)->pluck('slug');

        $data = [
            'properties' => app(PropertySearchService::class)->search($filters),
            'propertyTypes' => collect(PropertyType::cases())
                ->filter(fn (PropertyType $type) => $type === PropertyType::Other || $activeCategorySlugs->contains($type->value))
                ->values()
                ->all(),
            'purposes' => PropertyPurpose::cases(),
            'amenities' => Amenity::orderBy('name')->get(),
        ];

        if ($this->viewMode === 'map') {
            $pins = app(PropertySearchService::class)->pinsFor($filters);
            $data['pins'] = $pins;
            $this->dispatch('property-pins-updated', pins: $pins->all());
        }

        return view('livewire.property.property-search', $data);
    }
}
