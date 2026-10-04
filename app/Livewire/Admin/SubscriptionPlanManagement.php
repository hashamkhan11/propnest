<?php

namespace App\Livewire\Admin;

use App\Models\SubscriptionPlan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Agent Subscription Plans'])]
class SubscriptionPlanManagement extends Component
{
    public string $name = '';

    public string $durationDays = '';

    public string $priceDollars = '';

    public string $listingLimit = '';

    public string $featuredCredits = '0';

    public ?int $editingId = null;

    public function startCreate(): void
    {
        $this->reset(['editingId', 'name', 'durationDays', 'priceDollars', 'listingLimit']);
        $this->featuredCredits = '0';
    }

    public function startEdit(SubscriptionPlan $plan): void
    {
        $this->editingId = $plan->id;
        $this->name = $plan->name;
        $this->durationDays = (string) $plan->duration_days;
        $this->priceDollars = number_format($plan->price_cents / 100, 2, '.', '');
        $this->listingLimit = $plan->listing_limit !== null ? (string) $plan->listing_limit : '';
        $this->featuredCredits = (string) $plan->featured_credits;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'durationDays' => 'required|integer|min:1',
            'priceDollars' => 'required|numeric|min:0.01',
            'listingLimit' => 'nullable|integer|min:0',
            'featuredCredits' => 'required|integer|min:0',
        ]);

        $attributes = [
            'name' => $this->name,
            'duration_days' => (int) $this->durationDays,
            'price_cents' => (int) round(((float) $this->priceDollars) * 100),
            'listing_limit' => $this->listingLimit !== '' ? (int) $this->listingLimit : null,
            'featured_credits' => (int) $this->featuredCredits,
        ];

        if ($this->editingId) {
            SubscriptionPlan::whereKey($this->editingId)->update($attributes);
            session()->flash('success', 'Plan updated.');
        } else {
            SubscriptionPlan::create($attributes + ['is_active' => true, 'sort_order' => SubscriptionPlan::max('sort_order') + 1]);
            session()->flash('success', 'Plan created.');
        }

        $this->startCreate();
    }

    public function toggleActive(SubscriptionPlan $plan): void
    {
        $plan->update(['is_active' => ! $plan->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.subscription-plan-management', [
            'plans' => SubscriptionPlan::orderBy('sort_order')->get(),
        ]);
    }
}
