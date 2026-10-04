<?php

namespace App\Livewire\Agent;

use App\Enums\Property\PropertyStatus;
use App\Enums\User\UserRole;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class Directory extends Component
{
    use WithPagination;

    #[Url]
    public string $keyword = '';

    public function updating(string $name): void
    {
        if ($name !== 'page') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $agents = User::query()
            ->where('role', UserRole::Agent)
            ->with('agentProfile')
            ->withCount(['properties as active_listings_count' => function ($query) {
                $query->where('status', PropertyStatus::Published);
            }])
            ->when($this->keyword !== '', function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->keyword}%")
                        ->orWhereHas('agentProfile', function ($profileQuery) {
                            $profileQuery->where('agency_name', 'like', "%{$this->keyword}%");
                        });
                });
            })
            ->latest()
            ->paginate(12);

        return view('livewire.agent.directory', [
            'agents' => $agents,
        ]);
    }
}
