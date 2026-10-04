<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyPurposeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_for_sale(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'purpose' => PropertyPurpose::ForSale, 'title' => 'Sale House']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'purpose' => PropertyPurpose::ForRent, 'title' => 'Rent House']);

        $filters = PropertySearchFilters::fromArray(['purpose' => 'for_sale']);
        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(1, $results);
        $this->assertSame('Sale House', $results->first()->title);
    }

    public function test_search_filters_by_for_rent(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'purpose' => PropertyPurpose::ForSale, 'title' => 'Sale House']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'purpose' => PropertyPurpose::ForRent, 'title' => 'Rent House']);

        $filters = PropertySearchFilters::fromArray(['purpose' => 'for_rent']);
        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(1, $results);
        $this->assertSame('Rent House', $results->first()->title);
    }

    public function test_homepage_search_navigates_with_location_and_purpose(): void
    {
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'purpose' => PropertyPurpose::ForRent,
            'city' => 'Lahore',
            'title' => 'Lahore Rental',
        ]);
        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'purpose' => PropertyPurpose::ForSale,
            'city' => 'Lahore',
            'title' => 'Lahore For Sale',
        ]);

        $response = $this->get('/properties?'.http_build_query(['location' => 'Lahore', 'purpose' => 'for_rent']));

        $response->assertSee('Lahore Rental');
        $response->assertDontSee('Lahore For Sale');
    }
}
