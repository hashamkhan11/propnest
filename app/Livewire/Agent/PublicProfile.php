<?php

namespace App\Livewire\Agent;

use App\Enums\Property\PropertyStatus;
use App\Enums\User\UserRole;
use App\Models\Property;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class PublicProfile extends Component
{
    use WithPagination;

    public User $agent;

    public function mount(User $user): void
    {
        abort_unless($user->role === UserRole::Agent, 404);

        $this->agent = $user->load('agentProfile');
    }

    public function render()
    {
        $base = Property::query()
            ->where('agent_id', $this->agent->id)
            ->where('status', PropertyStatus::Published);

        return view('livewire.agent.public-profile', [
            'listings' => (clone $base)->with(['coverImage', 'images'])->latest()->paginate(12),
            'listingCount' => (clone $base)->count(),
        ]);
    }
}
