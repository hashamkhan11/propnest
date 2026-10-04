<?php

namespace Tests\Feature\Property;

use App\Livewire\Property\PropertyForm;
use App\Models\City;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyFormCityTest extends TestCase
{
    use RefreshDatabase;

    public function test_selecting_a_city_stores_its_name_on_save(): void
    {
        $agent = User::factory()->agent()->create();
        $category = PropertyCategory::where('slug', 'house')->firstOrFail();
        $city = City::factory()->create(['name' => 'Karachi']);

        Livewire::actingAs($agent)
            ->test(PropertyForm::class)
            ->set('title', 'Test Property')
            ->set('description', 'A description')
            ->set('price', '250000')
            ->set('categoryId', (string) $category->id)
            ->set('bedrooms', '3')
            ->set('bathrooms', '2')
            ->set('area', '1200')
            ->set('address', '123 Main St')
            ->set('cityId', (string) $city->id)
            ->set('coverImage', UploadedFile::fake()->image('cover.jpg'))
            ->call('save');

        $this->assertDatabaseHas('properties', ['title' => 'Test Property', 'city' => 'Karachi', 'city_id' => $city->id]);
    }

    public function test_city_is_required(): void
    {
        $agent = User::factory()->agent()->create();
        $category = PropertyCategory::where('slug', 'house')->firstOrFail();

        Livewire::actingAs($agent)
            ->test(PropertyForm::class)
            ->set('title', 'Test Property')
            ->set('description', 'A description')
            ->set('price', '250000')
            ->set('categoryId', (string) $category->id)
            ->set('bedrooms', '3')
            ->set('bathrooms', '2')
            ->set('area', '1200')
            ->set('address', '123 Main St')
            ->set('cityId', '')
            ->set('coverImage', UploadedFile::fake()->image('cover.jpg'))
            ->call('save')
            ->assertHasErrors(['cityId']);
    }
}
