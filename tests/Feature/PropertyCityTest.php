<?php

namespace Tests\Feature;

use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
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
            'agent_id' => User::factory()->agent()->create()->id,
            'title' => 'Test Property',
            'description' => 'Test description',
            'price' => 100000,
            'property_type' => PropertyType::House,
            'category_id' => PropertyCategory::where('slug', 'house')->value('id'),
            'city_id' => City::factory()->create()->id,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 1200,
            'address' => '123 Test St',
            'status' => PropertyStatus::Draft,
        ]);

        $this->assertSame('Unspecified', $property->fresh()->city);
    }
}
