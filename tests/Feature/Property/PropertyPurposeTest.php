<?php

namespace Tests\Feature\Property;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyPurposeTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_can_be_created_for_sale(): void
    {
        $property = Property::factory()->create(['purpose' => PropertyPurpose::ForSale]);

        $this->assertSame(PropertyPurpose::ForSale, $property->fresh()->purpose);
    }

    public function test_property_can_be_created_for_rent(): void
    {
        $property = Property::factory()->create(['purpose' => PropertyPurpose::ForRent]);

        $this->assertSame(PropertyPurpose::ForRent, $property->fresh()->purpose);
    }

    public function test_existing_rows_default_to_for_sale_when_purpose_is_omitted(): void
    {
        $property = Property::create([
            'agent_id' => User::factory()->agent()->create()->id,
            'title' => 'Test Property',
            'description' => 'Test description',
            'price' => 100000,
            'property_type' => PropertyType::House,
            'category_id' => PropertyCategory::where('slug', 'house')->value('id'),
            'city_id' => City::factory()->create(['name' => 'Testville'])->id,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'area' => 1200,
            'address' => '123 Test St',
            'city' => 'Testville',
            'status' => PropertyStatus::Draft,
        ]);

        $this->assertSame(PropertyPurpose::ForSale, $property->fresh()->purpose);
    }
}
