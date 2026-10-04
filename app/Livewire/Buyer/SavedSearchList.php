<?php

namespace App\Livewire\Buyer;

use App\Models\SavedSearch;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SavedSearchList extends Component
{
    public function toggleAlerts(SavedSearch $savedSearch): void
    {
        abort_unless($savedSearch->user_id === auth()->id(), 403);

        $savedSearch->update(['alerts_enabled' => ! $savedSearch->alerts_enabled]);

        $this->dispatch(
            'toast',
            type: 'success',
            message: $savedSearch->alerts_enabled
                ? 'Email alerts turned on for this search.'
                : 'Email alerts turned off for this search.',
        );
    }

    public function delete(SavedSearch $savedSearch): void
    {
        abort_unless($savedSearch->user_id === auth()->id(), 403);

        $savedSearch->delete();

        $this->dispatch('toast', type: 'success', message: 'Saved search deleted.');
    }

    public function render()
    {
        return view('livewire.buyer.saved-search-list', [
            'savedSearches' => auth()->user()->savedSearches()->latest()->get(),
        ]);
    }
}
