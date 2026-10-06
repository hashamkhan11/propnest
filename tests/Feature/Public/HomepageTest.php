<?php

namespace Tests\Feature\Public;

use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Livewire\Public\Home;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSeeLivewire(Home::class);
    }

    public function test_homepage_shows_featured_and_latest_properties(): void
    {
        $featured = Property::factory()->create([
            'status' => PropertyStatus::Published,
            'is_featured' => true,
            'title' => 'Featured Villa',
        ]);

        $latest = Property::factory()->create([
            'status' => PropertyStatus::Published,
            'is_featured' => false,
            'title' => 'Latest Apartment',
        ]);

        $response = $this->get('/');

        $response->assertSee('Featured Villa');
        $response->assertSee('Latest Apartment');
    }

    public function test_homepage_hides_unpublished_properties(): void
    {
        Property::factory()->draft()->create(['title' => 'Hidden Draft']);

        $response = $this->get('/');

        $response->assertDontSee('Hidden Draft');
    }

    public function test_homepage_shows_browse_by_city(): void
    {
        Property::factory()->count(3)->create(['status' => PropertyStatus::Published, 'city' => 'Karachi']);

        $response = $this->get('/');

        $response->assertSee('Where people are looking');
        $response->assertSee('Karachi');
    }

    public function test_homepage_shows_browse_by_type(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'property_type' => PropertyType::House]);

        $response = $this->get('/');

        $response->assertSee('House');
    }

    public function test_homepage_hides_property_types_with_no_published_listings(): void
    {
        Property::factory()->create(['status' => PropertyStatus::Published, 'property_type' => PropertyType::House]);

        $response = $this->get('/');

        $response->assertDontSee('Commercial');
    }

    public function test_homepage_does_not_show_fake_testimonials(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('Sarah M.');
        $response->assertDontSee('What People Say');
    }
}
