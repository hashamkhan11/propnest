<?php

namespace App\Livewire\Buyer;

use App\Enums\User\UserRole;
use App\Models\Property;
use Livewire\Component;

class FavoriteButton extends Component
{
    public Property $property;

    public bool $isFavorited = false;

    public function mount(Property $property): void
    {
        $this->property = $property;

        if ($this->isBuyer()) {
            $this->isFavorited = auth()->user()->favorites()
                ->where('property_id', $this->property->id)
                ->exists();
        }
    }

    public function toggle(): void
    {
        abort_unless($this->isBuyer(), 403);

        $existing = auth()->user()->favorites()
            ->where('property_id', $this->property->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->isFavorited = false;
            $this->dispatch('toast', type: 'success', message: 'Removed from favorites.');
        } else {
            auth()->user()->favorites()->create(['property_id' => $this->property->id]);
            $this->isFavorited = true;
            $this->dispatch('toast', type: 'success', message: 'Added to favorites.');
        }
    }

    private function isBuyer(): bool
    {
        return auth()->check() && auth()->user()->role === UserRole::Buyer;
    }

    public function render()
    {
        return view('livewire.buyer.favorite-button');
    }
}
