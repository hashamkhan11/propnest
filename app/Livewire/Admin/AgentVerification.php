<?php

namespace App\Livewire\Admin;

use App\Enums\User\AgentVerificationStatus;
use App\Enums\User\UserRole;
use App\Jobs\NotifyAgentOfVerification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin', ['title' => 'Agent Verification'])]
class AgentVerification extends Component
{
    public function verify(User $agent): void
    {
        $agent->agentProfile->update(['verification_status' => AgentVerificationStatus::Verified]);

        NotifyAgentOfVerification::dispatch($agent);

        session()->flash('success', "{$agent->name} is now verified.");
    }

    public function render()
    {
        $agents = User::query()
            ->where('role', UserRole::Agent)
            ->with('agentProfile')
            ->orderBy('name')
            ->get();

        return view('livewire.admin.agent-verification', [
            'agents' => $agents,
        ]);
    }
}
