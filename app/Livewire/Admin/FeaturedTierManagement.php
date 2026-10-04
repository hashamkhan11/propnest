<?php

namespace App\Livewire\Admin;

use App\Models\FeaturedPricingTier;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Featured Pricing'])]
class FeaturedTierManagement extends Component
{
    public string $name = '';

    public string $durationDays = '';

    public string $priceDollars = '';

    public ?int $editingId = null;

    public function startCreate(): void
    {
        $this->reset(['editingId', 'name', 'durationDays', 'priceDollars']);
    }

    public function startEdit(FeaturedPricingTier $tier): void
    {
        $this->editingId = $tier->id;
        $this->name = $tier->name;
        $this->durationDays = (string) $tier->duration_days;
        $this->priceDollars = number_format($tier->price_cents / 100, 2, '.', '');
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'durationDays' => 'required|integer|min:1',
            'priceDollars' => 'required|numeric|min:0.01',
        ]);

        $attributes = [
            'name' => $this->name,
            'duration_days' => (int) $this->durationDays,
            'price_cents' => (int) round(((float) $this->priceDollars) * 100),
        ];

        if ($this->editingId) {
            FeaturedPricingTier::whereKey($this->editingId)->update($attributes);
            session()->flash('success', 'Tier updated.');
        } else {
            FeaturedPricingTier::create($attributes + ['is_active' => true, 'sort_order' => FeaturedPricingTier::max('sort_order') + 1]);
            session()->flash('success', 'Tier created.');
        }

        $this->startCreate();
    }

    public function toggleActive(FeaturedPricingTier $tier): void
    {
        $tier->update(['is_active' => ! $tier->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.featured-tier-management', [
            'tiers' => FeaturedPricingTier::orderBy('sort_order')->get(),
        ]);
    }
}
