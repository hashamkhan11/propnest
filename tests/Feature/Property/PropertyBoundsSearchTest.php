<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyBoundsSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_bounds_filter_returns_only_properties_inside_the_box(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 10, 'longitude' => 10, 'title' => 'Inside']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 50, 'longitude' => 50, 'title' => 'Outside']);

        $filters = PropertySearchFilters::fromArray([
            'bounds' => ['north' => 11, 'south' => 9, 'east' => 11, 'west' => 9],
        ]);

        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(1, $results);
        $this->assertSame('Inside', $results->first()->title);
    }

    public function test_bounds_filter_combines_with_existing_filters(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 10, 'longitude' => 10, 'price' => 100_000, 'title' => 'Cheap Inside']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 10, 'longitude' => 10, 'price' => 900_000, 'title' => 'Expensive Inside']);

        $filters = PropertySearchFilters::fromArray([
            'bounds' => ['north' => 11, 'south' => 9, 'east' => 11, 'west' => 9],
            'maxPrice' => '200000',
        ]);

        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(1, $results);
        $this->assertSame('Cheap Inside', $results->first()->title);
    }
}
