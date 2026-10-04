<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyMapPinsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pins_for_excludes_properties_without_coordinates(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => null, 'longitude' => null, 'title' => 'No Coordinates']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 10, 'longitude' => 10, 'title' => 'Has Coordinates']);

        $pins = app(PropertySearchService::class)->pinsFor(PropertySearchFilters::fromArray([]));

        $this->assertCount(1, $pins);
        $this->assertSame('Has Coordinates', $pins->first()['title']);
    }

    public function test_pins_for_matches_the_same_set_as_search_for_a_geo_filter(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 10, 'longitude' => 10, 'title' => 'Inside']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 50, 'longitude' => 50, 'title' => 'Outside']);

        $filters = PropertySearchFilters::fromArray([
            'bounds' => ['north' => 11, 'south' => 9, 'east' => 11, 'west' => 9],
        ]);

        $service = app(PropertySearchService::class);
        $pins = $service->pinsFor($filters);
        $results = $service->search($filters);

        $this->assertSame(
            collect($results->items())->pluck('id')->sort()->values()->all(),
            $pins->pluck('id')->sort()->values()->all()
        );
    }

    public function test_pins_for_returns_expected_shape(): void
    {
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 10,
            'longitude' => 20,
            'title' => 'Shaped Property',
            'price' => 250_000,
        ]);

        $pin = app(PropertySearchService::class)->pinsFor(PropertySearchFilters::fromArray([]))->first();

        $this->assertSame(['id', 'lat', 'lng', 'price', 'formattedPrice', 'title', 'purpose', 'thumbnail', 'url'], array_keys($pin));
        $this->assertSame(10.0, $pin['lat']);
        $this->assertSame(20.0, $pin['lng']);
    }

    public function test_pins_for_includes_thumbnail_url_from_cover_image(): void
    {
        $property = Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 15,
            'longitude' => 25,
            'title' => 'Property With Cover',
        ]);

        // Create a cover image with a specific path
        $imagePath = 'properties/1/cover-photo.jpg';
        $property->images()->create([
            'path' => $imagePath,
            'is_cover' => true,
        ]);

        $pins = app(PropertySearchService::class)->pinsFor(PropertySearchFilters::fromArray([]));
        $pin = $pins->first();

        $this->assertCount(1, $pins);
        $this->assertNotNull($pin['thumbnail']);
        $this->assertSame(Storage::url($imagePath), $pin['thumbnail']);
    }
}
