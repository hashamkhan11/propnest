<?php

namespace App\Livewire\Buyer;

use App\Enums\Property\PropertyStatus;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.buyer.dashboard', [
            'favoritesCount' => Auth::user()->favorites()->count(),
            'savedSearchesCount' => Auth::user()->savedSearches()->count(),
            'isFirstLogin' => session('is_first_login', false),
            'listings' => Property::query()
                ->where('status', PropertyStatus::Published)
                ->with(['coverImage', 'images'])
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
