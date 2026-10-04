<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRadiusSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_radius_filter_includes_nearby_and_excludes_far_properties(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 0, 'longitude' => 0, 'title' => 'At Center']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 0.5, 'longitude' => 0, 'title' => 'Nearby']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 2, 'longitude' => 0, 'title' => 'Far Away']);

        $filters = PropertySearchFilters::fromArray([
            'radius' => ['lat' => 0, 'lng' => 0, 'km' => 100],
        ]);

        $results = app(PropertySearchService::class)->search($filters);
        $titles = collect($results->items())->pluck('title')->all();

        $this->assertContains('At Center', $titles);
        $this->assertContains('Nearby', $titles);
        $this->assertNotContains('Far Away', $titles);
    }

    public function test_radius_filter_excludes_properties_with_null_coordinates(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => null, 'longitude' => null, 'title' => 'No Coordinates']);

        $filters = PropertySearchFilters::fromArray([
            'radius' => ['lat' => 0, 'lng' => 0, 'km' => 1000],
        ]);

        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(0, $results);
    }

    public function test_radius_results_are_paginated_manually(): void
    {
        Property::factory()->count(15)->create(['status' => PropertyStatus::Published, 'latitude' => 0, 'longitude' => 0]);

        $filters = PropertySearchFilters::fromArray([
            'radius' => ['lat' => 0, 'lng' => 0, 'km' => 100],
        ]);

        $page1 = app(PropertySearchService::class)->search($filters, perPage: 12);

        $this->assertSame(15, $page1->total());
        $this->assertCount(12, $page1->items());
        $this->assertSame(2, $page1->lastPage());
    }

    public function test_geo_results_rank_is_featured_above_requested_sort_column(): void
    {
        // sort=price_asc alone would order these Cheap A (100k), Cheap B (200k), Expensive Featured (900k).
        // is_featured must win overall, putting the expensive featured property first despite the price_asc sort.
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Expensive Featured',
            'price' => 900_000,
            'is_featured' => true,
        ]);
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Cheap A',
            'price' => 100_000,
            'is_featured' => false,
        ]);
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Cheap B',
            'price' => 200_000,
            'is_featured' => false,
        ]);

        $filters = PropertySearchFilters::fromArray([
            'radius' => ['lat' => 0, 'lng' => 0, 'km' => 100],
            'sort' => 'price_asc',
        ]);

        $results = app(PropertySearchService::class)->search($filters);
        $titles = collect($results->items())->pluck('title')->all();

        $this->assertSame(['Expensive Featured', 'Cheap A', 'Cheap B'], $titles);
    }

    public function test_geo_results_rank_keyword_relevance_above_sort_column(): void
    {
        // Both properties share is_featured=false, so is_featured cannot influence order here.
        // sort=price_asc alone would put the cheaper description-only match first, but a title
        // match on the keyword is a stronger relevance signal and should rank above the plain
        // sort column (while still losing to is_featured, which is equal for both here).
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Pool House Deluxe',
            'description' => 'A lovely home with modern finishes.',
            'price' => 500_000,
            'is_featured' => false,
        ]);
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Budget Home',
            'description' => 'Comes with a private pool in the backyard.',
            'price' => 100_000,
            'is_featured' => false,
        ]);

        $filters = PropertySearchFilters::fromArray([
            'radius' => ['lat' => 0, 'lng' => 0, 'km' => 100],
            'sort' => 'price_asc',
            'keyword' => 'pool',
        ]);

        $results = app(PropertySearchService::class)->search($filters);
        $titles = collect($results->items())->pluck('title')->all();

        $this->assertSame(['Pool House Deluxe', 'Budget Home'], $titles);
    }

    public function test_geo_results_are_sorted_by_the_requested_sort_column(): void
    {
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Cheap',
            'price' => 100_000,
            'is_featured' => false,
        ]);
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Mid',
            'price' => 200_000,
            'is_featured' => false,
        ]);
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'latitude' => 0, 'longitude' => 0,
            'title' => 'Expensive',
            'price' => 300_000,
            'is_featured' => false,
        ]);

        $filters = PropertySearchFilters::fromArray([
            'radius' => ['lat' => 0, 'lng' => 0, 'km' => 100],
            'sort' => 'price_asc',
        ]);

        $results = app(PropertySearchService::class)->search($filters);
        $titles = collect($results->items())->pluck('title')->all();

        $this->assertSame(['Cheap', 'Mid', 'Expensive'], $titles);
    }
}
