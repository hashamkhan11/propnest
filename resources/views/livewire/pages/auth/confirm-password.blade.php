<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="display text-[2.5rem] mb-2">One more check</h1>
    <p class="text-[15px] text-gray-600 mb-8 leading-relaxed">This part of your account is sensitive, so we ask for your password again before you go on.</p>

    <form wire:submit="confirmPassword" class="space-y-5">
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password"
                          id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autofocus autocomplete="current-password"
                          :invalid="$errors->has('password')" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-button class="w-full justify-center">{{ __('Continue') }}</x-button>
    </form>
</div>
