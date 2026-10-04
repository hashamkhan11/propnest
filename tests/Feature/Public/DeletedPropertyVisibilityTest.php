<?php

namespace Tests\Feature\Public;

use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletedPropertyVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_property_disappears_from_homepage(): void
    {
        $property = Property::factory()->create(['status' => PropertyStatus::Published, 'title' => 'Soon Deleted']);
        $property->delete();

        $response = $this->get('/');

        $response->assertDontSee('Soon Deleted');
    }

    public function test_deleted_property_disappears_from_browse_listings(): void
    {
        $property = Property::factory()->create(['status' => PropertyStatus::Published, 'title' => 'Soon Deleted']);
        $property->delete();

        $response = $this->get('/properties');

        $response->assertDontSee('Soon Deleted');
    }

    public function test_deleted_property_detail_page_404s(): void
    {
        $property = Property::factory()->create(['status' => PropertyStatus::Published]);
        $propertyId = $property->id;
        $property->delete();

        $response = $this->get("/properties/{$propertyId}");

        $response->assertNotFound();
    }
}
