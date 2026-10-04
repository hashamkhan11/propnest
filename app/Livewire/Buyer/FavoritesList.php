<?php

namespace App\Livewire\Buyer;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class FavoritesList extends Component
{
    use WithPagination;

    public function render()
    {
        $favorites = auth()->user()->favorites()
            ->with(['property.coverImage', 'property.images'])
            ->latest()
            ->paginate(12);

        return view('livewire.buyer.favorites-list', [
            'favorites' => $favorites,
        ]);
    }
}
