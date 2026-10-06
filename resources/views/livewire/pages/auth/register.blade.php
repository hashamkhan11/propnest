<?php

use App\Models\User;
use App\Enums\User\UserRole;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'buyer';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:buyer,agent'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = UserRole::from($validated['role']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="display text-[2.5rem] mb-2">Make yourself at home.</h1>
    <p class="text-[15px] text-gray-600 mb-8 leading-relaxed">Save homes, message agents, or list your own. It takes a minute.</p>

    <form wire:submit="register" class="space-y-5">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon.user class="w-5 h-5" />
                </span>
                <x-text-input wire:model="name" id="name" class="block w-full pl-10" type="text" name="name" required autofocus autocomplete="name" :invalid="$errors->has('name')" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-icon.mail class="w-5 h-5" />
                </span>
                <x-text-input wire:model="email" id="email" class="block w-full pl-10" type="email" name="email" required autocomplete="username" :invalid="$errors->has('email')" />
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

        <div>
            <x-input-label value="I am here to" class="mb-2.5" />
            <x-role-selector name="role" :value="$role" />
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <x-button class="w-full justify-center" wire:loading.attr="disabled" wire:target="register">
            <span wire:loading.remove wire:target="register">{{ __('Create account') }}</span>
            <span wire:loading wire:target="register" class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                Creating your account…
            </span>
        </x-button>
    </form>

    <div class="relative my-7">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-900/10"></div></div>
        <div class="relative flex justify-center text-xs"><span class="bg-white px-3 font-mono text-[11px] text-gray-400 uppercase tracking-[0.14em]">or</span></div>
    </div>

    <a
        href="{{ route('auth.google.redirect') }}"
        x-data
        @click="$el.classList.add('opacity-50', 'pointer-events-none')"
        class="w-full inline-flex items-center justify-center gap-3 px-4 py-2.5 border border-gray-300 rounded-md font-medium text-sm leading-none text-primary-900 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2 transition-colors"
    >
        <x-icon.google class="w-5 h-5" />
        Continue with Google
    </a>

    <p class="text-sm text-gray-600 text-center mt-8">
        {{ __('Already have an account?') }}
        <a class="text-primary-900 font-medium link-underline" href="{{ route('login') }}" wire:navigate>{{ __('Sign in') }}</a>
    </p>
</div>
