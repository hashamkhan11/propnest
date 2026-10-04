<?php

namespace App\Livewire\Auth;

use App\Enums\User\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class SocialRoleSelection extends Component
{
    public string $role = '';

    public string $pendingName = '';

    public string $pendingEmail = '';

    public function mount(): void
    {
        $pending = session('social_pending_user');

        if (! $pending) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->pendingName = $pending['name'];
        $this->pendingEmail = $pending['email'];
    }

    public function continue(): void
    {
        $this->validate([
            'role' => 'required|in:buyer,agent',
        ]);

        $pending = session('social_pending_user');

        if (! $pending) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => Hash::make(Str::random(40)),
            'role' => UserRole::from($this->role),
            'google_id' => $pending['google_id'],
            'avatar_url' => $pending['avatar_url'],
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        session()->forget('social_pending_user');

        Auth::login($user, remember: true);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function cancel(): void
    {
        session()->forget('social_pending_user');

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.social-role-selection');
    }
}
