<?php

namespace Tests\Feature\Console;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpireFeaturedListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_unfeatures_properties_past_their_featured_until_date(): void
    {
        $expired = Property::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->subMinute(),
        ]);

        $stillFeatured = Property::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->addDay(),
        ]);

        $neverFeatured = Property::factory()->create([
            'is_featured' => false,
            'featured_until' => null,
        ]);

        $this->artisan('properties:expire-featured')->assertExitCode(0);

        $this->assertFalse($expired->fresh()->is_featured);
        $this->assertTrue($stillFeatured->fresh()->is_featured);
        $this->assertFalse($neverFeatured->fresh()->is_featured);
    }
}
