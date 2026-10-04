<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Region;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Regions & Cities'])]
class RegionManagement extends Component
{
    public string $newRegionName = '';

    public string $newCityName = '';

    public ?int $newCityRegionId = null;

    public function addRegion(): void
    {
        $this->validate(['newRegionName' => 'required|string|max:255']);

        Region::create(['name' => $this->newRegionName, 'slug' => Str::slug($this->newRegionName)]);

        $this->newRegionName = '';

        session()->flash('success', 'Region added.');
    }

    public function deleteRegion(Region $region): void
    {
        if ($region->cities()->exists()) {
            session()->flash('error', 'Cannot delete a region that still has cities — delete its cities first.');

            return;
        }

        $region->delete();

        session()->flash('success', 'Region deleted.');
    }

    public function addCity(): void
    {
        $this->validate([
            'newCityName' => 'required|string|max:255',
            'newCityRegionId' => 'required|exists:regions,id',
        ]);

        City::create([
            'region_id' => $this->newCityRegionId,
            'name' => $this->newCityName,
            'slug' => Str::slug($this->newCityName),
        ]);

        $this->newCityName = '';

        session()->flash('success', 'City added.');
    }

    public function deleteCity(City $city): void
    {
        if ($city->properties()->exists()) {
            session()->flash('error', 'Cannot delete a city that has listings — move or delete those listings first.');

            return;
        }

        $city->delete();

        session()->flash('success', 'City deleted.');
    }

    public function render()
    {
        return view('livewire.admin.region-management', [
            'regions' => Region::withCount('cities')->orderBy('name')->get(),
            'cities' => City::with('region')->withCount('properties')->orderBy('name')->get(),
        ]);
    }
}
