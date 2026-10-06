<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h1 class="display text-[2.5rem] mb-2">Forgot your password?</h1>
    <p class="text-[15px] text-gray-600 mb-8 leading-relaxed">It happens. Enter your email and we will send you a link to set a new one.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-5">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon.mail class="w-5 h-5" />
                </span>
                <x-text-input wire:model="email" id="email" class="block w-full pl-10" type="email" name="email" required autofocus :invalid="$errors->has('email')" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-button class="w-full justify-center" wire:loading.attr="disabled" wire:target="sendPasswordResetLink">
            <span wire:loading.remove wire:target="sendPasswordResetLink">{{ __('Email me a reset link') }}</span>
            <span wire:loading wire:target="sendPasswordResetLink" class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                Sending…
            </span>
        </x-button>
    </form>

    <p class="text-sm text-gray-600 text-center mt-8">
        <a class="text-primary-900 font-medium link-underline" href="{{ route('login') }}" wire:navigate>{{ __('Back to sign in') }}</a>
    </p>
</div>
