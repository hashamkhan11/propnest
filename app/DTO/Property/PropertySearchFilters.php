<?php

namespace App\DTO\Property;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyType;

readonly class PropertySearchFilters
{
    /**
     * @param  array<int, int>  $amenityIds
     * @param  ?array{north: float, south: float, east: float, west: float}  $bounds
     * @param  ?array{lat: float, lng: float, km: float}  $radius
     * @param  ?array<int, array{0: float, 1: float}>  $polygon
     */
    public function __construct(
        public ?string $keyword = null,
        public ?string $location = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?PropertyType $propertyType = null,
        public ?PropertyPurpose $purpose = null,
        public ?int $bedrooms = null,
        public ?int $bathrooms = null,
        public ?float $minArea = null,
        public array $amenityIds = [],
        public string $sort = 'newest',
        public bool $featuredOnly = false,
        public string $viewMode = 'grid',
        public ?array $bounds = null,
        public ?array $radius = null,
        public ?array $polygon = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            keyword: ($data['keyword'] ?? '') !== '' ? $data['keyword'] : null,
            location: ($data['location'] ?? '') !== '' ? $data['location'] : null,
            minPrice: ($data['minPrice'] ?? '') !== '' ? (float) $data['minPrice'] : null,
            maxPrice: ($data['maxPrice'] ?? '') !== '' ? (float) $data['maxPrice'] : null,
            propertyType: ($data['propertyType'] ?? '') !== '' ? PropertyType::from($data['propertyType']) : null,
            purpose: ($data['purpose'] ?? '') !== '' ? PropertyPurpose::from($data['purpose']) : null,
            bedrooms: ($data['bedrooms'] ?? '') !== '' ? (int) $data['bedrooms'] : null,
            bathrooms: ($data['bathrooms'] ?? '') !== '' ? (int) $data['bathrooms'] : null,
            minArea: ($data['minArea'] ?? '') !== '' ? (float) $data['minArea'] : null,
            amenityIds: $data['amenityIds'] ?? [],
            sort: $data['sort'] ?? 'newest',
            featuredOnly: filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOL),
            viewMode: $data['viewMode'] ?? 'grid',
            bounds: $data['bounds'] ?? null,
            radius: $data['radius'] ?? null,
            polygon: $data['polygon'] ?? null,
        );
    }
}
