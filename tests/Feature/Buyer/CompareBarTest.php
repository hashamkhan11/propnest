<?php

namespace Tests\Feature\Buyer;

use App\Livewire\Buyer\CompareBar;
use App\Models\Property;
use App\Models\User;
use App\Services\Property\CompareListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompareBarTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_selected_properties_for_a_buyer(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create(['title' => 'Ocean View Villa']);
        app(CompareListService::class)->add($property->id);

        Livewire::actingAs($buyer)
            ->test(CompareBar::class)
            ->assertSet('ids', [$property->id])
            ->assertSee('Ocean View Villa');
    }

    public function test_remove_takes_a_property_out_of_the_list(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create();
        app(CompareListService::class)->add($property->id);

        Livewire::actingAs($buyer)
            ->test(CompareBar::class)
            ->call('remove', $property->id)
            ->assertSet('ids', []);

        $this->assertFalse(app(CompareListService::class)->has($property->id));
    }

    public function test_clear_all_empties_the_list(): void
    {
        $buyer = User::factory()->create();
        $properties = Property::factory()->count(2)->create();
        $compareList = app(CompareListService::class);
        $compareList->add($properties[0]->id);
        $compareList->add($properties[1]->id);

        Livewire::actingAs($buyer)
            ->test(CompareBar::class)
            ->call('clearAll')
            ->assertSet('ids', []);

        $this->assertSame([], app(CompareListService::class)->ids());
    }

    public function test_renders_nothing_for_a_guest(): void
    {
        $property = Property::factory()->create(['title' => 'Guest Should Not See This']);
        app(CompareListService::class)->add($property->id);

        Livewire::test(CompareBar::class)
            ->assertDontSee('Guest Should Not See This');
    }

    public function test_excludes_unpublished_properties_from_the_display_and_the_count(): void
    {
        $buyer = User::factory()->create();
        $published = Property::factory()->create(['title' => 'Published Villa']);
        $draft = Property::factory()->draft()->create(['title' => 'Draft Villa']);

        $compareList = app(CompareListService::class);
        $compareList->add($published->id);
        $compareList->add($draft->id);

        Livewire::actingAs($buyer)
            ->test(CompareBar::class)
            ->assertSee('Published Villa')
            ->assertDontSee('Draft Villa')
            ->assertSee('Compare (1/3)');

        $this->assertSame([$published->id], $compareList->ids());
    }
}
