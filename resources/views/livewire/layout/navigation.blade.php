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

<nav x-data="{ open: false }" class="bg-white/95 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="text-gray-900">
                        <x-brand-logo icon-size="h-9 w-9" text-size="text-lg" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.index')" wire:navigate>
                        {{ __('Browse Listings') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('agent.dashboard') || request()->routeIs('buyer.dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </x-nav-link>

                        @if (auth()->user()->role === UserRole::Buyer)
                            <x-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.index')" wire:navigate class="!inline-flex !items-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0" fill="{{ request()->routeIs('favorites.index') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 20 20"><path stroke-width="1.5" d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-1.9C4.045 12.733 2 10.352 2 7.5 2 5.015 3.989 3 6.5 3c1.343 0 2.622.68 3.5 1.72C10.878 3.68 12.157 3 13.5 3 16.011 3 18 5.015 18 7.5c0 2.852-2.045 5.233-3.885 6.82a22.049 22.049 0 01-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 01-.69.001l-.002-.001z" /></svg>
                                {{ __('My Favorites') }}
                            </x-nav-link>

                            <x-nav-link :href="route('saved-searches.index')" :active="request()->routeIs('saved-searches.index')" wire:navigate class="!inline-flex !items-center gap-1.5">
                                <x-icon.house-search class="w-4 h-4 shrink-0" />
                                {{ __('Saved Searches') }}
                            </x-nav-link>

                            <x-nav-link :href="route('compare.index')" :active="request()->routeIs('compare.index')" wire:navigate class="!inline-flex !items-center gap-1.5">
                                <x-icon.columns class="w-4 h-4 shrink-0" />
                                {{ __('Compare') }}
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
                            <button class="inline-flex items-center gap-2.5 pl-1.5 pr-3 py-1.5 rounded-full border border-transparent hover:border-gray-200 hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                                <span
                                    class="flex items-center justify-center h-8 w-8 rounded-full bg-primary-100 text-primary-700 font-heading font-700 text-sm shrink-0 overflow-hidden"
                                    x-data="{{ json_encode(['name' => auth()->user()->name, 'photo' => auth()->user()->profile_photo_url]) }}"
                                    x-on:profile-updated.window="name = $event.detail.name"
                                    x-on:profile-photo-updated.window="photo = $event.detail.url"
                                >
                                    <img x-show="photo" :src="photo" x-cloak class="h-full w-full object-cover">
                                    <span x-show="!photo" x-text="name.trim().charAt(0).toUpperCase()"></span>
                                </span>

                                <span class="text-sm font-medium text-gray-700 max-w-[10rem] truncate" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></span>

                                <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" wire:navigate>
                            <x-button type="button" variant="ghost">{{ __('Log in') }}</x-button>
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" wire:navigate>
                                <x-button type="button" variant="accent">{{ __('Register') }}</x-button>
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
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('properties.index')" :active="request()->routeIs('properties.index')" wire:navigate>
                {{ __('Browse Listings') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard') || request()->routeIs('agent.dashboard') || request()->routeIs('buyer.dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                @if (auth()->user()->role === UserRole::Buyer)
                    <x-responsive-nav-link :href="route('favorites.index')" :active="request()->routeIs('favorites.index')" wire:navigate class="!flex !items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="{{ request()->routeIs('favorites.index') ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 20 20"><path stroke-width="1.5" d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-1.9C4.045 12.733 2 10.352 2 7.5 2 5.015 3.989 3 6.5 3c1.343 0 2.622.68 3.5 1.72C10.878 3.68 12.157 3 13.5 3 16.011 3 18 5.015 18 7.5c0 2.852-2.045 5.233-3.885 6.82a22.049 22.049 0 01-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 01-.69.001l-.002-.001z" /></svg>
                        {{ __('My Favorites') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('saved-searches.index')" :active="request()->routeIs('saved-searches.index')" wire:navigate class="!flex !items-center gap-2">
                        <x-icon.house-search class="w-4 h-4 shrink-0" />
                        {{ __('Saved Searches') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('compare.index')" :active="request()->routeIs('compare.index')" wire:navigate class="!flex !items-center gap-2">
                        <x-icon.columns class="w-4 h-4 shrink-0" />
                        {{ __('Compare') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-3 border-t border-gray-100">
                <div class="flex items-center px-4 gap-3">
                    <span
                        class="flex items-center justify-center h-9 w-9 rounded-full bg-primary-100 text-primary-700 font-heading font-700 text-sm shrink-0 overflow-hidden"
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
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-100">
                <div class="px-4 flex gap-3">
                    <a href="{{ route('login') }}" wire:navigate class="flex-1">
                        <x-button type="button" variant="secondary" class="w-full justify-center">{{ __('Log in') }}</x-button>
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate class="flex-1">
                            <x-button type="button" variant="accent" class="w-full justify-center">{{ __('Register') }}</x-button>
                        </a>
                    @endif
                </div>
            </div>
        @endauth
    </div>
</nav>
