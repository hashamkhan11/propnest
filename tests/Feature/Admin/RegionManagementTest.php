<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\RegionManagement;
use App\Models\City;
use App\Models\Property;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_a_region_and_a_city(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(RegionManagement::class)
            ->set('newRegionName', 'Punjab')
            ->call('addRegion');

        $region = Region::where('slug', 'punjab')->firstOrFail();

        Livewire::actingAs($admin)->test(RegionManagement::class)
            ->set('newCityRegionId', $region->id)
            ->set('newCityName', 'Lahore')
            ->call('addCity');

        $this->assertDatabaseHas('cities', ['name' => 'Lahore', 'region_id' => $region->id]);
    }

    public function test_cannot_delete_a_city_with_listings(): void
    {
        $admin = User::factory()->admin()->create();
        $region = Region::factory()->create();
        $city = City::factory()->create(['region_id' => $region->id]);
        Property::factory()->create(['city_id' => $city->id, 'city' => $city->name]);

        Livewire::actingAs($admin)->test(RegionManagement::class)
            ->call('deleteCity', $city->id);

        $this->assertNotNull($city->fresh());
    }
}
