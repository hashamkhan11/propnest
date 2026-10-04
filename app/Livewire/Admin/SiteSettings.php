<?php

namespace App\Livewire\Admin;

use App\Enums\Settings\Currency;
use App\Models\Property;
use App\Support\Settings;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Site Settings'])]
class SiteSettings extends Component
{
    public string $currency = '';

    public string $maxFeaturedListings = '';

    public string $freeListingLimit = '';

    public function mount(): void
    {
        $this->currency = Settings::currency()->value;
        $this->maxFeaturedListings = (string) (Settings::maxFeaturedListings() ?? '');
        $this->freeListingLimit = (string) (Settings::freeListingLimit() ?? '');
    }

    public function save(): void
    {
        $this->validate([
            'currency' => 'required|in:'.implode(',', array_column(Currency::cases(), 'value')),
            'maxFeaturedListings' => 'nullable|integer|min:1',
            'freeListingLimit' => 'nullable|integer|min:0',
        ]);

        Settings::setCurrency(Currency::from($this->currency));
        Settings::setMaxFeaturedListings($this->maxFeaturedListings !== '' ? (int) $this->maxFeaturedListings : null);
        Settings::setFreeListingLimit($this->freeListingLimit !== '' ? (int) $this->freeListingLimit : null);

        session()->flash('success', 'Site settings updated.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings', [
            'currencies' => Currency::cases(),
            'activeFeaturedCount' => Property::where('is_featured', true)->where('featured_until', '>', now())->count(),
        ]);
    }
}
