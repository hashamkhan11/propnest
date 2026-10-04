<?php

namespace Tests\Feature\Property;

use App\Enums\Property\PropertyStatus;
use App\Livewire\Property\PropertySearch;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyMapSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_mode_toggles_between_grid_and_map(): void
    {
        Livewire::test(PropertySearch::class)
            ->assertSet('viewMode', 'grid')
            ->call('setViewMode', 'map')
            ->assertSet('viewMode', 'map')
            ->call('setViewMode', 'grid')
            ->assertSet('viewMode', 'grid');
    }

    public function test_search_this_area_filters_to_bounds_and_clears_other_geo_filters(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 10, 'longitude' => 10, 'title' => 'Inside Bounds']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'latitude' => 50, 'longitude' => 50, 'title' => 'Outside Bounds']);

        Livewire::test(PropertySearch::class)
            ->call('setViewMode', 'map')
            ->call('searchThisArea', ['north' => 11, 'south' => 9, 'east' => 11, 'west' => 9])
            ->assertSet('mapRadius', null)
            ->assertSet('mapPolygon', null)
            ->assertSee('Inside Bounds')
            ->assertDontSee('Outside Bounds');
    }

    public function test_radius_and_polygon_search_clear_each_other(): void
    {
        Livewire::test(PropertySearch::class)
            ->call('setViewMode', 'map')
            ->call('applyRadiusSearch', ['lat' => 0, 'lng' => 0], 10.0)
            ->assertSet('mapRadius', ['lat' => 0, 'lng' => 0, 'km' => 10.0])
            ->call('applyPolygonSearch', [[0, 0], [0, 1], [1, 1], [1, 0]])
            ->assertSet('mapRadius', null)
            ->assertSet('mapPolygon', [[0, 0], [0, 1], [1, 1], [1, 0]]);
    }

    public function test_clear_geo_search_resets_all_geo_filters(): void
    {
        Livewire::test(PropertySearch::class)
            ->call('setViewMode', 'map')
            ->call('searchThisArea', ['north' => 11, 'south' => 9, 'east' => 11, 'west' => 9])
            ->call('clearGeoSearch')
            ->assertSet('mapBounds', null)
            ->assertSet('mapRadius', null)
            ->assertSet('mapPolygon', null);
    }

    public function test_radius_search_clamps_an_excessively_large_km_value(): void
    {
        Livewire::test(PropertySearch::class)
            ->call('setViewMode', 'map')
            ->call('applyRadiusSearch', ['lat' => 0, 'lng' => 0], 999999.0)
            ->assertSet('mapRadius', ['lat' => 0, 'lng' => 0, 'km' => 50.0]);
    }
}
