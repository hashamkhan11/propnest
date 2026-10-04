<?php

namespace App\Livewire\Agent;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProfileForm extends Component
{
    public string $agency_name = '';

    public string $phone = '';

    public string $bio = '';

    public function mount(): void
    {
        $profile = Auth::user()->agentProfile;

        $this->agency_name = $profile->agency_name ?? '';
        $this->phone = $profile->phone ?? '';
        $this->bio = $profile->bio ?? '';
    }

    protected function rules(): array
    {
        return [
            'agency_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:2000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        Auth::user()->agentProfile->update([
            'agency_name' => $this->agency_name !== '' ? $this->agency_name : null,
            'phone' => $this->phone !== '' ? $this->phone : null,
            'bio' => $this->bio !== '' ? $this->bio : null,
        ]);

        session()->flash('success', 'Profile updated.');
    }

    public function render()
    {
        return view('livewire.agent.profile-form');
    }
}
