<?php

namespace Tests\Feature\Property;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyFormPurposeTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_create_a_for_rent_listing(): void
    {
        $agent = User::factory()->agent()->create();
        $category = \App\Models\PropertyCategory::where('slug', PropertyType::Apartment->value)->firstOrFail();
        $city = \App\Models\City::factory()->create(['name' => 'Karachi']);

        Livewire::actingAs($agent)
            ->test(\App\Livewire\Property\PropertyForm::class)
            ->set('title', 'Test Property')
            ->set('description', 'A description')
            ->set('price', '2500')
            ->set('categoryId', (string) $category->id)
            ->set('purpose', PropertyPurpose::ForRent->value)
            ->set('bedrooms', '2')
            ->set('bathrooms', '1')
            ->set('area', '900')
            ->set('address', '123 Main St')
            ->set('cityId', (string) $city->id)
            ->set('coverImage', UploadedFile::fake()->image('cover.jpg'))
            ->call('save');

        $this->assertDatabaseHas('properties', ['title' => 'Test Property', 'purpose' => 'for_rent']);
    }

    public function test_new_listing_defaults_to_for_sale(): void
    {
        $agent = User::factory()->agent()->create();

        Livewire::actingAs($agent)
            ->test(\App\Livewire\Property\PropertyForm::class)
            ->assertSet('purpose', PropertyPurpose::ForSale->value);
    }

    public function test_editing_a_property_loads_its_current_purpose(): void
    {
        $agent = User::factory()->agent()->create();
        $property = \App\Models\Property::factory()->for($agent, 'agent')->create(['purpose' => PropertyPurpose::ForRent]);

        Livewire::actingAs($agent)
            ->test(\App\Livewire\Property\PropertyForm::class, ['property' => $property])
            ->assertSet('purpose', PropertyPurpose::ForRent->value);
    }
}
