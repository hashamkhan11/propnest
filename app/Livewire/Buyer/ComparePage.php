<?php

namespace App\Livewire\Buyer;

use App\Enums\User\UserRole;
use App\Models\Property;
use App\Services\Property\CompareListService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class ComparePage extends Component
{
    public function remove(int $propertyId): void
    {
        abort_unless($this->isBuyer(), 403);

        app(CompareListService::class)->remove($propertyId);
        $this->dispatch('compare-updated');
    }

    #[On('compare-updated')]
    public function refresh(): void
    {
        //
    }

    private function isBuyer(): bool
    {
        return auth()->check() && auth()->user()->role === UserRole::Buyer;
    }

    private function loadProperties(): Collection
    {
        return app(CompareListService::class)->publishedProperties(['coverImage', 'amenities']);
    }

    #[Layout('layouts.public')]
    public function render()
    {
        $properties = $this->loadProperties();

        $amenityNames = $properties
            ->flatMap(fn (Property $property) => $property->amenities->pluck('name'))
            ->unique()
            ->sort()
            ->values();

        return view('livewire.buyer.compare-page', [
            'properties' => $properties,
            'amenityNames' => $amenityNames,
        ]);
    }
}
