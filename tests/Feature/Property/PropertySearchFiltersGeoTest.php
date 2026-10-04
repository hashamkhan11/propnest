<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use Tests\TestCase;

class PropertySearchFiltersGeoTest extends TestCase
{
    public function test_geo_fields_default_to_null_and_grid_view_mode(): void
    {
        $filters = PropertySearchFilters::fromArray([]);

        $this->assertSame('grid', $filters->viewMode);
        $this->assertNull($filters->bounds);
        $this->assertNull($filters->radius);
        $this->assertNull($filters->polygon);
    }

    public function test_geo_fields_pass_through_from_array(): void
    {
        $filters = PropertySearchFilters::fromArray([
            'viewMode' => 'map',
            'bounds' => ['north' => 1.0, 'south' => 0.0, 'east' => 1.0, 'west' => 0.0],
            'radius' => ['lat' => 0.5, 'lng' => 0.5, 'km' => 10.0],
            'polygon' => [[0, 0], [0, 1], [1, 1]],
        ]);

        $this->assertSame('map', $filters->viewMode);
        $this->assertSame(['north' => 1.0, 'south' => 0.0, 'east' => 1.0, 'west' => 0.0], $filters->bounds);
        $this->assertSame(['lat' => 0.5, 'lng' => 0.5, 'km' => 10.0], $filters->radius);
        $this->assertSame([[0, 0], [0, 1], [1, 1]], $filters->polygon);
    }
}
