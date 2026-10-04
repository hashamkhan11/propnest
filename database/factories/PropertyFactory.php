<?php

namespace Database\Factories;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $category = PropertyCategory::inRandomOrder()->first();
        $city = City::inRandomOrder()->first() ?? City::factory()->create();

        return [
            'agent_id' => User::factory()->agent(),
            'title' => fake()->streetName().' '.fake()->randomElement(['Home', 'Residence', 'Estate', 'Villa']),
            'description' => fake()->paragraphs(3, true),
            'price' => fake()->numberBetween(100_000, 2_000_000),
            'property_type' => PropertyType::tryFrom($category->slug) ?? PropertyType::Other,
            'category_id' => $category->id,
            'purpose' => fake()->randomElement(PropertyPurpose::cases()),
            'bedrooms' => fake()->numberBetween(1, 6),
            'bathrooms' => fake()->numberBetween(1, 4),
            'area' => fake()->numberBetween(500, 5000),
            'address' => fake()->address(),
            'city' => $city->name,
            'city_id' => $city->id,
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'status' => PropertyStatus::Published,
            'is_featured' => false,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PropertyStatus::Draft]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => ['status' => PropertyStatus::Archived]);
    }
}
