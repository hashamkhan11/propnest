<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyPolygonSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_polygon_filter_includes_points_inside_and_excludes_points_outside(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 0.5, 'longitude' => 0.5, 'title' => 'Inside Square']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 2, 'longitude' => 2, 'title' => 'Outside Square']);

        $polygon = [[0, 0], [0, 1], [1, 1], [1, 0]];

        $filters = PropertySearchFilters::fromArray(['polygon' => $polygon]);

        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(1, $results);
        $this->assertSame('Inside Square', $results->first()->title);
    }

    public function test_polygon_filter_with_fewer_than_three_points_matches_nothing(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 0.5, 'longitude' => 0.5, 'title' => 'Somewhere']);

        $filters = PropertySearchFilters::fromArray(['polygon' => [[0, 0], [0, 1]]]);

        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(0, $results);
    }
}
