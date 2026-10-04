<?php

namespace Tests\Feature\Property;

use App\DTO\Property\PropertySearchFilters;
use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedOnlyFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_featured_filter_only_returns_featured_published_listings(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'is_featured' => true, 'title' => 'Featured One']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'is_featured' => false, 'title' => 'Not Featured']);
        Property::factory()->draft()->create(['is_featured' => true, 'title' => 'Featured Draft']);

        $filters = PropertySearchFilters::fromArray(['featured' => '1']);
        $results = app(PropertySearchService::class)->search($filters);

        $this->assertCount(1, $results);
        $this->assertSame('Featured One', $results->first()->title);
    }

    public function test_browse_listings_with_featured_param_shows_only_featured(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'is_featured' => true, 'title' => 'Featured One']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'is_featured' => false, 'title' => 'Not Featured']);

        $response = $this->get('/properties?featured=1');

        $response->assertSee('Featured One');
        $response->assertDontSee('Not Featured');
    }

    public function test_browse_listings_without_featured_param_shows_all_published(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'is_featured' => true, 'title' => 'Featured One']);
        Property::factory()->create(['status' => PropertyStatus::Published, 'is_featured' => false, 'title' => 'Not Featured']);

        $response = $this->get('/properties');

        $response->assertSee('Featured One');
        $response->assertSee('Not Featured');
    }
}
