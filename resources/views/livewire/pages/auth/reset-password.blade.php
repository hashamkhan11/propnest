<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <h1 class="display text-[2.5rem] mb-2">Set a new password</h1>
    <p class="text-[15px] text-gray-600 mb-8 leading-relaxed">Pick something you have not used here before.</p>

    <form wire:submit="resetPassword" class="space-y-5">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon.mail class="w-5 h-5" />
                </span>
                <x-text-input wire:model="email" id="email" class="block w-full pl-10" type="email" name="email" required autofocus autocomplete="username" :invalid="$errors->has('email')" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon.lock class="w-5 h-5" />
                </span>
                <x-text-input
                    wire:model="password" id="password" class="block w-full pl-10 pr-10"
                    type="password" x-bind:type="show ? 'text' : 'password'"
                    name="password" required autocomplete="new-password"
                    :invalid="$errors->has('password')"
                />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <x-icon.eye x-show="!show" class="w-5 h-5" />
                    <x-icon.eye-off x-show="show" x-cloak class="w-5 h-5" />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon.lock class="w-5 h-5" />
                </span>
                <x-text-input
                    wire:model="password_confirmation" id="password_confirmation" class="block w-full pl-10 pr-10"
                    type="password" x-bind:type="show ? 'text' : 'password'"
                    name="password_confirmation" required autocomplete="new-password"
                    :invalid="$errors->has('password_confirmation')"
                />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <x-icon.eye x-show="!show" class="w-5 h-5" />
                    <x-icon.eye-off x-show="show" x-cloak class="w-5 h-5" />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-button class="w-full justify-center" wire:loading.attr="disabled" wire:target="resetPassword">
            <span wire:loading.remove wire:target="resetPassword">{{ __('Save new password') }}</span>
            <span wire:loading wire:target="resetPassword" class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                Saving…
            </span>
        </x-button>
    </form>
</div>
