<?php

namespace App\Livewire\Buyer;

use App\Enums\User\UserRole;
use App\Models\Property;
use App\Services\Property\CompareListService;
use Livewire\Attributes\On;
use Livewire\Component;

class CompareToggle extends Component
{
    public Property $property;

    public bool $isSelected = false;

    public bool $isFull = false;

    public function mount(Property $property): void
    {
        $this->property = $property;
        $this->refreshState();
    }

    public function toggle(): void
    {
        abort_unless($this->isBuyer(), 403);

        $compareList = app(CompareListService::class);

        if ($compareList->has($this->property->id)) {
            $compareList->remove($this->property->id);
        } else {
            $compareList->add($this->property->id);
        }

        $this->refreshState();
        $this->dispatch('compare-updated');
    }

    #[On('compare-updated')]
    public function refreshState(): void
    {
        $compareList = app(CompareListService::class);

        $this->isSelected = $compareList->has($this->property->id);
        $this->isFull = $compareList->isFull();
    }

    private function isBuyer(): bool
    {
        return auth()->check() && auth()->user()->role === UserRole::Buyer;
    }

    public function render()
    {
        return view('livewire.buyer.compare-toggle');
    }
}
