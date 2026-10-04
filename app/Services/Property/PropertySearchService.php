<?php

namespace App\Services\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Support\Settings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PropertySearchService
{
    public function search(PropertySearchFilters $filters, int $perPage = 12): LengthAwarePaginator
    {
        if ($filters->radius || $filters->polygon) {
            return $this->searchWithExactGeoShape($filters, $perPage);
        }

        $query = $this->applyFilters(Property::query()->with(['coverImage', 'images']), $filters)
            ->orderBy('is_featured', 'desc');

        if ($filters->keyword) {
            // Title matches are a stronger signal of relevance than a keyword
            // only appearing in the description, so rank them first.
            $query->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', ["%{$filters->keyword}%"]);
        }

        return $query
            ->orderBy(...$this->sortColumn($filters->sort))
            ->paginate($perPage)
            ->withQueryString();
    }

    public function matches(Property $property, PropertySearchFilters $filters): bool
    {
        return $this->applyFilters(Property::query()->whereKey($property->id), $filters)->exists();
    }

    /**
     * @return Collection<int, array{id: int, lat: float, lng: float, price: float, formattedPrice: string, title: string, purpose: string, thumbnail: ?string, url: string}>
     */
    public function pinsFor(PropertySearchFilters $filters, int $limit = 500): Collection
    {
        $candidates = $this->applyFilters(Property::query()->with('coverImage'), $filters)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($filters->radius || $filters->polygon) {
            $candidates = $candidates->filter(fn (Property $property) => $this->matchesExactGeoShape($property, $filters))->values();
        }

        return $candidates->take($limit)->map(fn (Property $property) => [
            'id' => $property->id,
            'lat' => (float) $property->latitude,
            'lng' => (float) $property->longitude,
            'price' => (float) $property->price,
            'formattedPrice' => Settings::currency()->format($property->price),
            'title' => $property->title,
            'purpose' => $property->purpose->value,
            'thumbnail' => $property->coverImage ? Storage::url($property->coverImage->thumbnailDisplayPath()) : null,
            'url' => route('properties.show', $property),
        ])->values();
    }

    private function applyFilters(Builder $query, PropertySearchFilters $filters): Builder
    {
        return $query
            ->where(fn ($q) => $q->where('status', PropertyStatus::Published)
                ->orWhere(fn ($q) => $q->where('status', PropertyStatus::Rented)
                    ->where('purpose', PropertyPurpose::ForRent)))
            ->when($filters->keyword, fn ($query, $keyword) => $query->where(
                fn ($q) => $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
            ))
            ->when($filters->location, fn ($query, $location) => $query->where(
                fn ($q) => $q->where('address', 'like', "%{$location}%")
                    ->orWhere('city', 'like', "%{$location}%")
            ))
            ->when($filters->minPrice, fn ($query, $price) => $query->where('price', '>=', $price))
            ->when($filters->maxPrice, fn ($query, $price) => $query->where('price', '<=', $price))
            ->when($filters->propertyType, fn ($query, $type) => $query->where('property_type', $type))
            ->when($filters->purpose, fn ($query, $purpose) => $query->where('purpose', $purpose))
            ->when($filters->featuredOnly, fn ($query) => $query->where('is_featured', true))
            ->when($filters->bedrooms, fn ($query, $bedrooms) => $query->where('bedrooms', '>=', $bedrooms))
            ->when($filters->bathrooms, fn ($query, $bathrooms) => $query->where('bathrooms', '>=', $bathrooms))
            ->when($filters->minArea, fn ($query, $area) => $query->where('area', '>=', $area))
            ->when($filters->amenityIds !== [], fn ($query) => $query->whereHas(
                'amenities',
                fn ($q) => $q->whereIn('amenities.id', $filters->amenityIds),
                '=',
                count($filters->amenityIds)
            ))
            ->when($filters->bounds, fn ($query, $bounds) => $query
                ->whereBetween('latitude', [$bounds['south'], $bounds['north']])
                ->whereBetween('longitude', [$bounds['west'], $bounds['east']]))
            ->when($filters->radius, function ($query, $radius) {
                $box = $this->boundingBoxForRadius((float) $radius['lat'], (float) $radius['lng'], (float) $radius['km']);
                $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->whereBetween('latitude', [$box['south'], $box['north']])
                    ->whereBetween('longitude', [$box['west'], $box['east']]);
            })
            ->when($filters->polygon, function ($query, $polygon) {
                $box = $this->boundingBoxForPolygon($polygon);
                $query->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->whereBetween('latitude', [$box['south'], $box['north']])
                    ->whereBetween('longitude', [$box['west'], $box['east']]);
            });
    }

    private function searchWithExactGeoShape(PropertySearchFilters $filters, int $perPage): LengthAwarePaginator
    {
        $candidates = $this->applyFilters(Property::query()->with(['coverImage', 'images']), $filters)->get();

        $matching = $candidates->filter(fn (Property $property) => $this->matchesExactGeoShape($property, $filters))->values();

        $sorted = $this->sortGeoResults($matching, $filters);

        $page = Paginator::resolveCurrentPage('page');
        $items = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

        return (new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $sorted->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page']
        ))->withQueryString();
    }

    private function sortGeoResults(Collection $properties, PropertySearchFilters $filters): Collection
    {
        [$column, $direction] = $this->sortColumn($filters->sort);

        $sorted = $properties->sortBy(
            fn (Property $property) => $property->{$column},
            SORT_REGULAR,
            $direction === 'desc'
        )->values();

        if ($filters->keyword) {
            $sorted = $sorted->sortBy(
                fn (Property $property) => str_contains(strtolower($property->title), strtolower($filters->keyword)) ? 0 : 1
            )->values();
        }

        return $sorted->sortByDesc('is_featured')->values();
    }

    private function matchesExactGeoShape(Property $property, PropertySearchFilters $filters): bool
    {
        if ($property->latitude === null || $property->longitude === null) {
            return false;
        }

        $lat = (float) $property->latitude;
        $lng = (float) $property->longitude;

        if ($filters->radius) {
            return $this->haversineKm($lat, $lng, (float) $filters->radius['lat'], (float) $filters->radius['lng']) <= (float) $filters->radius['km'];
        }

        if ($filters->polygon) {
            return $this->pointInPolygon($lat, $lng, $filters->polygon);
        }

        return true;
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusKm = 6371.0;

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * @return array{north: float, south: float, east: float, west: float}
     */
    private function boundingBoxForRadius(float $lat, float $lng, float $km): array
    {
        $latDelta = $km / 111.045;
        $lngDelta = $km / (111.045 * max(cos(deg2rad($lat)), 0.000001));

        return [
            'north' => $lat + $latDelta,
            'south' => $lat - $latDelta,
            'east' => $lng + $lngDelta,
            'west' => $lng - $lngDelta,
        ];
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $polygon
     */
    private function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $inside = false;
        $count = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            [$latI, $lngI] = $polygon[$i];
            [$latJ, $lngJ] = $polygon[$j];

            $intersects = ($lngI > $lng) !== ($lngJ > $lng)
                && $lat < ($latJ - $latI) * ($lng - $lngI) / ($lngJ - $lngI) + $latI;

            if ($intersects) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $polygon
     * @return array{north: float, south: float, east: float, west: float}
     */
    private function boundingBoxForPolygon(array $polygon): array
    {
        $lats = array_column($polygon, 0);
        $lngs = array_column($polygon, 1);

        return [
            'north' => max($lats),
            'south' => min($lats),
            'east' => max($lngs),
            'west' => min($lngs),
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function sortColumn(string $sort): array
    {
        return match ($sort) {
            'featured' => ['is_featured', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            default => ['created_at', 'desc'],
        };
    }
}
