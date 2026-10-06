<?php

use App\Enums\User\UserRole;
use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-cream/90 backdrop-blur-md border-b border-gray-900/10 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" wire:navigate class="text-primary-900" aria-label="PropNest home">
                        <x-brand-logo icon-size="h-8 w-8" text-size="text-[22px]" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden gap-7 sm:ms-12 sm:flex items-center">
                    <x-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.index')" wire:navigate>
                        {{ __('Search homes') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('agent.dashboard') || request()->routeIs('buyer.dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        @if (auth()->user()->role === UserRole::Buyer)
                            <x-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.index')" wire:navigate>
                                {{ __('Saved homes') }}
                            </x-nav-link>

                            <x-nav-link :href="route('saved-searches.index')" :active="request()->routeIs('saved-searches.index')" wire:navigate>
                                {{ __('Saved searches') }}
                            </x-nav-link>

                            <x-nav-link :href="route('compare.index')" :active="request()->routeIs('compare.index')" wire:navigate>
                                {{ __('Compare') }}
                            </x-nav-link>
                        @elseif (auth()->user()->role === UserRole::Agent)
                            <x-nav-link :href="route('agent.properties.index')" :active="request()->routeIs('agent.properties.*')" wire:navigate>
                                {{ __('Listings') }}
                            </x-nav-link>

                            <x-nav-link :href="route('agent.inquiries.index')" :active="request()->routeIs('agent.inquiries.*')" wire:navigate>
                                {{ __('Inquiries') }}
                            </x-nav-link>

                            <x-nav-link :href="route('agent.subscriptions.index')" :active="request()->routeIs('agent.subscriptions.*')" wire:navigate>
                                {{ __('Plans') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2.5 pl-1 pr-2.5 py-1 rounded-full border border-gray-900/10 bg-white hover:border-gray-900/25 focus:outline-none transition-colors">
                                <span
                                    class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-900 text-white font-medium text-sm shrink-0 overflow-hidden"
                                    x-data="{{ json_encode(['name' => auth()->user()->name, 'photo' => auth()->user()->profile_photo_url]) }}"
                                    x-on:profile-updated.window="name = $event.detail.name"
                                    x-on:profile-photo-updated.window="photo = $event.detail.url"
                                >
                                    <img x-show="photo" :src="photo" x-cloak class="h-full w-full object-cover">
                                    <span x-show="!photo" x-text="name.trim().charAt(0).toUpperCase()"></span>
                                </span>

                                <span class="text-sm font-medium text-primary-900 max-w-[10rem] truncate" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>

                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Account settings') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Sign out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" wire:navigate>
                            <x-button type="button" variant="ghost">{{ __('Sign in') }}</x-button>
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" wire:navigate>
                                <x-button type="button" variant="accent">{{ __('Create account') }}</x-button>
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-900/10">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.index')" wire:navigate>
                {{ __('Search homes') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('agent.dashboard') || request()->routeIs('buyer.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                @if (auth()->user()->role === UserRole::Buyer)
                    <x-responsive-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.index')" wire:navigate>
                        {{ __('Saved homes') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('saved-searches.index')" :active="request()->routeIs('saved-searches.index')" wire:navigate>
                        {{ __('Saved searches') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('compare.index')" :active="request()->routeIs('compare.index')" wire:navigate>
                        {{ __('Compare') }}
                    </x-responsive-nav-link>
                @elseif (auth()->user()->role === UserRole::Agent)
                    <x-responsive-nav-link :href="route('agent.properties.index')" :active="request()->routeIs('agent.properties.*')" wire:navigate>
                        {{ __('Listings') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('agent.inquiries.index')" :active="request()->routeIs('agent.inquiries.*')" wire:navigate>
                        {{ __('Inquiries') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('agent.subscriptions.index')" :active="request()->routeIs('agent.subscriptions.*')" wire:navigate>
                        {{ __('Plans') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-3 border-t border-gray-900/10">
                <div class="flex items-center px-4 gap-3">
                    <span
                        class="flex items-center justify-center h-9 w-9 rounded-full bg-primary-900 text-white font-medium text-sm shrink-0 overflow-hidden"
                        x-data="{{ json_encode(['name' => auth()->user()->name, 'photo' => auth()->user()->profile_photo_url]) }}"
                        x-on:profile-updated.window="name = $event.detail.name"
                        x-on:profile-photo-updated.window="photo = $event.detail.url"
                    >
                        <img x-show="photo" :src="photo" x-cloak class="h-full w-full object-cover">
                        <span x-show="!photo" x-text="name.trim().charAt(0).toUpperCase()"></span>
                    </span>
                    <div>
                        <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Account settings') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Sign out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-900/10">
                <div class="px-4 flex gap-3">
                    <a href="{{ route('login') }}" wire:navigate class="flex-1">
                        <x-button type="button" variant="secondary" class="w-full justify-center">{{ __('Sign in') }}</x-button>
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate class="flex-1">
                            <x-button type="button" variant="accent" class="w-full justify-center">{{ __('Create account') }}</x-button>
                        </a>
                    @endif
                </div>
            </div>
        @endauth
    </div>
</nav>
