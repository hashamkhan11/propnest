<?php

namespace Tests\Feature\Buyer;

use App\Livewire\Buyer\ComparePage;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\User;
use App\Services\Property\CompareListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ComparePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('compare.index'))->assertRedirect(route('login'));
    }

    public function test_non_buyer_is_forbidden(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)->get(route('compare.index'))->assertForbidden();
    }

    public function test_shows_empty_state_when_nothing_selected(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->get(route('compare.index'))
            ->assertSee("haven't selected any properties");
    }

    public function test_shows_selected_properties_side_by_side(): void
    {
        $buyer = User::factory()->create();
        $pool = Amenity::create(['name' => 'Pool']);
        $propertyA = Property::factory()->create(['title' => 'Sunny Villa', 'bedrooms' => 3]);
        $propertyB = Property::factory()->create(['title' => 'Downtown Loft', 'bedrooms' => 1]);
        $propertyA->amenities()->attach($pool);

        app(CompareListService::class)->add($propertyA->id);
        app(CompareListService::class)->add($propertyB->id);

        Livewire::actingAs($buyer)
            ->test(ComparePage::class)
            ->assertSee('Sunny Villa')
            ->assertSee('Downtown Loft')
            ->assertSee('Pool');
    }

    public function test_prunes_unpublished_properties_from_the_session_list(): void
    {
        $buyer = User::factory()->create();
        $published = Property::factory()->create();
        $draft = Property::factory()->draft()->create();

        $compareList = app(CompareListService::class);
        $compareList->add($published->id);
        $compareList->add($draft->id);

        Livewire::actingAs($buyer)->test(ComparePage::class);

        $this->assertSame([$published->id], $compareList->ids());
    }

    public function test_remove_action_takes_a_property_out_of_comparison(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create();
        app(CompareListService::class)->add($property->id);

        Livewire::actingAs($buyer)
            ->test(ComparePage::class)
            ->call('remove', $property->id);

        $this->assertFalse(app(CompareListService::class)->has($property->id));
    }

    public function test_non_buyer_cannot_remove_via_the_component(): void
    {
        $agent = User::factory()->agent()->create();
        $property = Property::factory()->create();
        app(CompareListService::class)->add($property->id);

        Livewire::actingAs($agent)
            ->test(ComparePage::class)
            ->call('remove', $property->id)
            ->assertForbidden();

        $this->assertTrue(app(CompareListService::class)->has($property->id));
    }

    public function test_reflects_an_external_removal_when_compare_updated_fires(): void
    {
        $buyer = User::factory()->create();
        $propertyA = Property::factory()->create(['title' => 'Sunny Villa']);
        $propertyB = Property::factory()->create(['title' => 'Downtown Loft']);

        app(CompareListService::class)->add($propertyA->id);
        app(CompareListService::class)->add($propertyB->id);

        $component = Livewire::actingAs($buyer)
            ->test(ComparePage::class)
            ->assertSee('Sunny Villa')
            ->assertSee('Downtown Loft');

        // Simulate another component (e.g. CompareBar) removing an item and
        // dispatching the compare-updated event that ComparePage listens for.
        app(CompareListService::class)->remove($propertyB->id);

        $component->call('refresh')
            ->assertSee('Sunny Villa')
            ->assertDontSee('Downtown Loft');
    }

    public function test_compare_bar_does_not_reappear_on_the_compare_page(): void
    {
        $buyer = User::factory()->create();
        $propertyA = Property::factory()->create();
        $propertyB = Property::factory()->create();

        app(CompareListService::class)->add($propertyA->id);
        app(CompareListService::class)->add($propertyB->id);

        $this->actingAs($buyer)
            ->get(route('compare.index'))
            ->assertOk()
            ->assertDontSee('Compare Now');
    }
}
