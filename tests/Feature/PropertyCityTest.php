<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyCityTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_be_created_with_a_city(): void
    {
        $property = Property::factory()->create(['city' => 'Karachi']);

        $this->assertDatabaseHas('properties', ['id' => $property->id, 'city' => 'Karachi']);
    }

    public function test_existing_rows_default_to_unspecified_when_city_is_omitted(): void
    {
        $property = Property::create([
            'agent_id' => \App\Models\User::factory()->agent()->create()->id,
            'title' => 'Test Property',
            'description' => 'Test description',
            'price' => 100000,
            'property_type' => \App\Enums\Property\PropertyType::House,
            'category_id' => \App\Models\PropertyCategory::where('slug', 'house')->value('id'),
            'city_id' => \App\Models\City::factory()->create()->id,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 1200,
            'address' => '123 Test St',
            'status' => \App\Enums\Property\PropertyStatus::Draft,
        ]);

        $this->assertSame('Unspecified', $property->fresh()->city);
    }
}
