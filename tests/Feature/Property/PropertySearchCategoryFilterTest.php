<?php

namespace Tests\Feature\Property;

use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Livewire\Property\PropertySearch;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertySearchCategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_deactivated_category_is_removed_from_buyer_search_filter(): void
    {
        PropertyCategory::where('slug', 'land')->update(['is_active' => false]);

        Livewire::test(PropertySearch::class)
            ->assertSee('House')
            ->assertDontSee('Land');
    }

    public function test_deleted_category_is_removed_from_buyer_search_filter(): void
    {
        // Only an unused category can be deleted (see PropertyCategoryManagement::delete()
        // guard), so this mirrors what a real admin deletion leaves behind.
        PropertyCategory::where('slug', 'land')->delete();

        Livewire::test(PropertySearch::class)
            ->assertSee('House')
            ->assertDontSee('Land');
    }

    public function test_existing_property_with_deactivated_category_still_appears_in_results(): void
    {
        $land = PropertyCategory::where('slug', 'land')->first();

        Property::factory()->create([
            'status' => PropertyStatus::Published,
            'category_id' => $land->id,
            'property_type' => PropertyType::Land,
            'title' => 'Riverside Plot',
        ]);

        $land->update(['is_active' => false]);

        // Unfiltered results still include it...
        Livewire::test(PropertySearch::class)
            ->assertSee('Riverside Plot');

        // ...and a pre-existing bookmark/link filtering on the now-hidden
        // category still resolves correctly instead of erroring out.
        Livewire::test(PropertySearch::class)
            ->set('propertyType', 'land')
            ->assertSee('Riverside Plot');
    }
}
