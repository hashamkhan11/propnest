<?php

namespace Tests\Feature\Buyer;

use App\Livewire\Buyer\CompareToggle;
use App\Models\Property;
use App\Models\User;
use App\Services\Property\CompareListService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CompareToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_add_a_property_to_comparison(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create();

        Livewire::actingAs($buyer)
            ->test(CompareToggle::class, ['property' => $property])
            ->assertSet('isSelected', false)
            ->call('toggle')
            ->assertSet('isSelected', true);

        $this->assertTrue(app(CompareListService::class)->has($property->id));
    }

    public function test_buyer_can_remove_a_property_from_comparison(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create();
        app(CompareListService::class)->add($property->id);

        Livewire::actingAs($buyer)
            ->test(CompareToggle::class, ['property' => $property])
            ->assertSet('isSelected', true)
            ->call('toggle')
            ->assertSet('isSelected', false);

        $this->assertFalse(app(CompareListService::class)->has($property->id));
    }

    public function test_checkbox_reports_full_once_three_properties_are_selected(): void
    {
        $buyer = User::factory()->create();
        $properties = Property::factory()->count(4)->create();

        $compareList = app(CompareListService::class);
        $compareList->add($properties[0]->id);
        $compareList->add($properties[1]->id);
        $compareList->add($properties[2]->id);

        Livewire::actingAs($buyer)
            ->test(CompareToggle::class, ['property' => $properties[3]])
            ->assertSet('isSelected', false)
            ->assertSet('isFull', true);
    }

    public function test_non_buyer_cannot_toggle_comparison(): void
    {
        $agent = User::factory()->agent()->create();
        $property = Property::factory()->create();

        Livewire::actingAs($agent)
            ->test(CompareToggle::class, ['property' => $property])
            ->call('toggle')
            ->assertForbidden();
    }

    public function test_guest_cannot_toggle_comparison(): void
    {
        $property = Property::factory()->create();

        Livewire::test(CompareToggle::class, ['property' => $property])
            ->call('toggle')
            ->assertForbidden();
    }
}
