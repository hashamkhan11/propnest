<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="font-heading text-2xl md:text-3xl font-semibold text-primary-900 mb-2">Welcome back</h1>
    <p class="text-sm text-gray-600 mb-8">Log in to manage your listings, favorites, and inquiries.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary-400">
                    <x-icon.mail class="w-5 h-5" />
                </span>
                <x-text-input wire:model="form.email" id="email" class="block w-full pl-10" type="email" name="email" required autofocus autocomplete="username" :invalid="$errors->has('form.email')" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-primary-400">
                    <x-icon.lock class="w-5 h-5" />
                </span>
                <x-text-input
                    wire:model="form.password" id="password" class="block w-full pl-10 pr-10"
                    type="password" x-bind:type="show ? 'text' : 'password'"
                    name="password" required autocomplete="current-password"
                    :invalid="$errors->has('form.password')"
                />
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <x-icon.eye x-show="!show" class="w-5 h-5" />
                    <x-icon.eye-off x-show="show" x-cloak class="w-5 h-5" />
                </button>
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary-600 hover:text-primary-800 underline" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-button class="w-full justify-center" wire:loading.attr="disabled" wire:target="login">
            <span wire:loading.remove wire:target="login">{{ __('Log in') }}</span>
            <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                Signing in…
            </span>
        </x-button>
    </form>

    <div class="relative my-7">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
        <div class="relative flex justify-center text-xs"><span class="bg-white px-3 text-gray-400 font-medium uppercase tracking-wide">or</span></div>
    </div>

    <a
        href="{{ route('auth.google.redirect') }}"
        x-data
        @click="$el.classList.add('opacity-50', 'pointer-events-none')"
        class="w-full inline-flex items-center justify-center gap-3 px-4 py-2.5 border border-gray-200 rounded-lg font-semibold text-sm text-gray-700 bg-white shadow-sm hover:shadow-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-150"
    >
        <x-icon.google class="w-5 h-5" />
        Continue with Google
    </a>

    <p class="text-sm text-gray-600 text-center mt-8">
        {{ __("Don't have an account?") }}
        <a class="text-primary-600 hover:text-primary-800 underline font-medium" href="{{ route('register') }}" wire:navigate>{{ __('Register') }}</a>
    </p>
</div>
