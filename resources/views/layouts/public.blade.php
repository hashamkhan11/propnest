<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PropNest') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $__authViewByRoute = [
            'login' => 'login',
            'register' => 'register',
            'password.request' => 'forgot-password',
            'password.reset' => 'reset-password',
        ];
        $initialAuthView = $__authViewByRoute[Route::currentRouteName()] ?? null;
        $resetToken = $initialAuthView === 'reset-password' ? request()->route('token') : null;
    @endphp
    <body class="font-sans antialiased bg-white text-gray-900" x-data="{
        mobileOpen: false,
        scrolled: false,
        openAuth(view, event) {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.button !== 0) return;
            event.preventDefault();
            this.mobileOpen = false;
            window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { view } }));
        }
    }" x-init="scrolled = window.scrollY > 8" @scroll.window="scrolled = window.scrollY > 8">
        <x-flash-messages />
        <x-toast-listener />

        @php
            $__navLinks = [
                ['label' => 'Buy', 'href' => route('properties.index', ['purpose' => 'for_sale']), 'active' => request()->routeIs('properties.index') && request()->query('purpose') === 'for_sale'],
                ['label' => 'Rent', 'href' => route('properties.index', ['purpose' => 'for_rent']), 'active' => request()->routeIs('properties.index') && request()->query('purpose') === 'for_rent'],
                ['label' => 'Browse Properties', 'href' => route('properties.index'), 'active' => request()->routeIs('properties.index') && !request()->query('purpose')],
                ['label' => 'Agents', 'href' => route('agents.index'), 'active' => request()->routeIs('agents.*')],
                ['label' => 'Compare', 'href' => route('compare.index'), 'active' => request()->routeIs('compare.index')],
                ['label' => 'Contact', 'href' => route('home').'#contact', 'active' => false],
            ];
        @endphp

        <header
            class="sticky top-0 z-40 transition-all duration-300"
            :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100' : 'bg-white border-b border-transparent'"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 lg:h-[4.5rem]">
                    <a href="{{ route('home') }}" wire:navigate class="text-primary-700 shrink-0 transition-transform hover:scale-[1.02]">
                        <x-brand-logo icon-size="h-9 w-9" text-size="text-xl" />
                    </a>

                    <nav class="hidden lg:flex items-center gap-1 text-sm font-medium text-gray-600">
                        @foreach ($__navLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                wire:navigate
                                class="relative px-3.5 py-2 rounded-md transition-colors duration-200 hover:text-primary-700 hover:bg-primary-50/70 {{ $link['active'] ? 'text-primary-800' : '' }}"
                            >
                                {{ $link['label'] }}
                                <span class="absolute left-3.5 right-3.5 -bottom-0.5 h-0.5 rounded-full bg-accent-500 origin-left transition-transform duration-200 {{ $link['active'] ? 'scale-x-100' : 'scale-x-0' }}"></span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="hidden lg:flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" wire:navigate>
                                <x-button variant="ghost">Dashboard</x-button>
                            </a>
                            <a href="{{ route('profile') }}" wire:navigate>
                                <x-button variant="secondary">Profile</x-button>
                            </a>
                        @else
                            <a href="{{ route('login') }}" @click="openAuth('login', $event)">
                                <x-button variant="ghost">Login</x-button>
                            </a>
                            <a href="{{ route('register') }}" @click="openAuth('register', $event)">
                                <x-button variant="accent">Register</x-button>
                            </a>
                        @endauth
                    </div>

                    <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-500" aria-label="Toggle navigation menu">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ hidden: mobileOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ hidden: !mobileOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div
                    x-show="mobileOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="lg:hidden pb-4 space-y-1 border-t border-gray-100 pt-2"
                >
                    @foreach ($__navLinks as $link)
                        <a
                            href="{{ $link['href'] }}"
                            wire:navigate
                            class="block px-2 py-2.5 rounded-md text-gray-700 font-medium transition-colors {{ $link['active'] ? 'text-primary-800 bg-primary-50' : 'hover:text-primary-700 hover:bg-primary-50/70' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <div class="pt-3 flex gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" wire:navigate><x-button variant="ghost">Dashboard</x-button></a>
                        @else
                            <a href="{{ route('login') }}" @click="openAuth('login', $event)"><x-button variant="ghost">Login</x-button></a>
                            <a href="{{ route('register') }}" @click="openAuth('register', $event)"><x-button variant="accent">Register</x-button></a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <x-auth-modal :initial-view="$initialAuthView" :reset-token="$resetToken" />

        <livewire:buyer.compare-bar />

        <x-confirm-dialog />

        <footer class="bg-primary-900 text-primary-50 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10">
                <div class="lg:col-span-2">
                    <x-brand-logo icon-size="h-9 w-9" text-size="text-xl" class="text-white" />
                    <p class="text-sm text-primary-300 mt-3 max-w-xs leading-relaxed">
                        A premium real-estate marketplace connecting buyers and renters with verified agents and listings, in every city you love.
                    </p>
                </div>

                <div>
                    <h4 class="font-heading font-700 text-sm text-white uppercase tracking-wide">Explore</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-primary-300">
                        <li><a href="{{ route('properties.index', ['purpose' => 'for_sale']) }}" wire:navigate class="hover:text-white transition-colors">Buy</a></li>
                        <li><a href="{{ route('properties.index', ['purpose' => 'for_rent']) }}" wire:navigate class="hover:text-white transition-colors">Rent</a></li>
                        <li><a href="{{ route('properties.index') }}" wire:navigate class="hover:text-white transition-colors">Browse Properties</a></li>
                        <li><a href="{{ route('agents.index') }}" wire:navigate class="hover:text-white transition-colors">Find an Agent</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-700 text-sm text-white uppercase tracking-wide">Company</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-primary-300">
                        <li><a href="{{ route('home') }}#about" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('home') }}#contact" class="hover:text-white transition-colors">Contact</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" wire:navigate class="hover:text-white transition-colors">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('register') }}" @click="openAuth('register', $event)" class="hover:text-white transition-colors">Register as Agent</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading font-700 text-sm text-white uppercase tracking-wide">Stay Updated</h4>
                    <p class="text-sm text-primary-300 mt-4">Get new listings and market updates in your inbox.</p>
                    <div class="mt-3">
                        <livewire:public.newsletter-form />
                    </div>
                </div>
            </div>

            <div class="border-t border-primary-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-primary-400">
                    <span>&copy; {{ date('Y') }} PropNest. All rights reserved.</span>
                    <span class="text-primary-500">Verified listings &middot; Trusted agents &middot; Secure inquiries</span>
                </div>
            </div>
        </footer>
    </body>
</html>
