<?php

namespace App\Livewire\Buyer;

use App\Enums\User\UserRole;
use App\Services\Property\CompareListService;
use Livewire\Attributes\On;
use Livewire\Component;

class CompareBar extends Component
{
    public array $ids = [];

    public bool $onComparePage = false;

    public function mount(): void
    {
        $this->onComparePage = request()->routeIs('compare.index');
        $this->refresh();
    }

    #[On('compare-updated')]
    public function refresh(): void
    {
        $this->ids = app(CompareListService::class)->ids();
    }

    public function remove(int $propertyId): void
    {
        abort_unless($this->isBuyer(), 403);

        app(CompareListService::class)->remove($propertyId);
        $this->refresh();
        $this->dispatch('compare-updated');
    }

    public function clearAll(): void
    {
        abort_unless($this->isBuyer(), 403);

        app(CompareListService::class)->clear();
        $this->refresh();
        $this->dispatch('compare-updated');
    }

    private function isBuyer(): bool
    {
        return auth()->check() && auth()->user()->role === UserRole::Buyer;
    }

    public function render()
    {
        return view('livewire.buyer.compare-bar', [
            'properties' => app(CompareListService::class)->publishedProperties(),
        ]);
    }
}
