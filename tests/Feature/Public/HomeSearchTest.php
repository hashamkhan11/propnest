<?php

namespace Tests\Feature\Public;

use App\Livewire\Public\Home;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HomeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_search_does_not_navigate_and_shows_a_validation_message(): void
    {
        Livewire::test(Home::class)
            ->set('location', '')
            ->set('purpose', '')
            ->call('search')
            ->assertHasErrors(['location'])
            ->assertNoRedirect();
    }

    public function test_search_with_a_location_navigates_to_browse_listings(): void
    {
        Livewire::test(Home::class)
            ->set('location', 'Lahore')
            ->set('purpose', '')
            ->call('search')
            ->assertRedirect(route('properties.index', ['location' => 'Lahore']));
    }

    public function test_search_with_only_a_purpose_navigates_to_browse_listings(): void
    {
        Livewire::test(Home::class)
            ->set('location', '')
            ->set('purpose', 'for_rent')
            ->call('search')
            ->assertRedirect(route('properties.index', ['purpose' => 'for_rent']));
    }
}
