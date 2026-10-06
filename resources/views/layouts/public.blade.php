<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PropNest') }} · Homes, honestly listed</title>
        <meta name="description" content="Search homes for sale and rent from verified agents. Real photos, real prices, and a direct line to the agent.">
        <meta name="theme-color" content="#F6F4EF">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

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
    <body class="font-sans antialiased bg-cream text-primary-900" x-data="{
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
                ['label' => 'All homes', 'href' => route('properties.index'), 'active' => request()->routeIs('properties.index') && !request()->query('purpose')],
                ['label' => 'Agents', 'href' => route('agents.index'), 'active' => request()->routeIs('agents.*')],
                ['label' => 'Compare', 'href' => route('compare.index'), 'active' => request()->routeIs('compare.index')],
            ];
        @endphp

        <header
            class="sticky top-0 z-40 transition-colors duration-300 border-b"
            :class="scrolled ? 'bg-cream/85 backdrop-blur-md border-gray-900/10' : 'bg-cream border-transparent'"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center h-16">
                    <a href="{{ route('home') }}" wire:navigate class="shrink-0 text-primary-900" aria-label="PropNest home">
                        <x-brand-logo icon-size="h-8 w-8" text-size="text-[22px]" />
                    </a>

                    <nav class="hidden lg:flex items-center gap-7 ml-12 text-[14px] text-gray-600">
                        @foreach ($__navLinks as $link)
                            <a
                                href="{{ $link['href'] }}"
                                wire:navigate
                                @class([
                                    'relative py-1 transition-colors hover:text-primary-900',
                                    'text-primary-900 font-medium after:absolute after:left-0 after:right-0 after:-bottom-[21px] after:h-[2px] after:bg-accent-500' => $link['active'],
                                ])
                            >{{ $link['label'] }}</a>
                        @endforeach
                    </nav>

                    <div class="hidden lg:flex items-center gap-2 ml-auto">
                        @auth
                            <a href="{{ route('dashboard') }}" wire:navigate>
                                <x-button variant="ghost">Dashboard</x-button>
                            </a>
                            <a href="{{ route('profile') }}" wire:navigate>
                                <x-button variant="secondary">Account</x-button>
                            </a>
                        @else
                            <a href="{{ route('login') }}" @click="openAuth('login', $event)">
                                <x-button variant="ghost">Sign in</x-button>
                            </a>
                            <a href="{{ route('register') }}" @click="openAuth('register', $event)">
                                <x-button>Create account</x-button>
                            </a>
                        @endauth
                    </div>

                    <button @click="mobileOpen = !mobileOpen" class="lg:hidden ml-auto -mr-2 p-2 text-primary-900" aria-label="Toggle navigation menu">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ hidden: mobileOpen }" stroke-linecap="round" stroke-width="1.75" d="M4 8h16M4 16h16" />
                            <path :class="{ hidden: !mobileOpen }" class="hidden" stroke-linecap="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div
                    x-show="mobileOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="lg:hidden pb-5 border-t border-gray-900/10"
                >
                    @foreach ($__navLinks as $link)
                        <a
                            href="{{ $link['href'] }}"
                            wire:navigate
                            @class([
                                'flex items-center justify-between py-3.5 border-b border-gray-900/5 text-[17px]',
                                'text-primary-900 font-medium' => $link['active'],
                                'text-gray-700' => ! $link['active'],
                            ])
                        >
                            {{ $link['label'] }}
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @endforeach
                    <div class="pt-4 grid grid-cols-2 gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" wire:navigate class="col-span-2"><x-button class="w-full">Dashboard</x-button></a>
                        @else
                            <a href="{{ route('login') }}" @click="openAuth('login', $event)"><x-button variant="secondary" class="w-full">Sign in</x-button></a>
                            <a href="{{ route('register') }}" @click="openAuth('register', $event)"><x-button class="w-full">Create account</x-button></a>
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

        <footer class="bg-primary-900 text-gray-400 mt-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-16 grid grid-cols-1 lg:grid-cols-12 gap-12 border-b border-white/10">
                    <div class="lg:col-span-5">
                        <x-brand-logo tone="light" icon-size="h-8 w-8" text-size="text-[22px]" class="text-white" />
                        <p class="display !text-white text-4xl sm:text-5xl mt-8 max-w-md">Homes, <em class="text-accent-300">honestly</em> listed.</p>
                        <p class="text-sm mt-5 max-w-sm leading-relaxed">Every listing comes from an agent we have checked. You see the real price, the real photos and who you are talking to.</p>
                    </div>

                    <div class="lg:col-span-2">
                        <h4 class="kicker !text-gray-500">Find</h4>
                        <ul class="mt-5 space-y-3 text-sm">
                            <li><a href="{{ route('properties.index', ['purpose' => 'for_sale']) }}" wire:navigate class="text-gray-300 hover:text-white transition-colors">Homes for sale</a></li>
                            <li><a href="{{ route('properties.index', ['purpose' => 'for_rent']) }}" wire:navigate class="text-gray-300 hover:text-white transition-colors">Homes for rent</a></li>
                            <li><a href="{{ route('properties.index') }}" wire:navigate class="text-gray-300 hover:text-white transition-colors">Search on the map</a></li>
                            <li><a href="{{ route('agents.index') }}" wire:navigate class="text-gray-300 hover:text-white transition-colors">Find an agent</a></li>
                        </ul>
                    </div>

                    <div class="lg:col-span-2">
                        <h4 class="kicker !text-gray-500">PropNest</h4>
                        <ul class="mt-5 space-y-3 text-sm">
                            <li><a href="{{ route('home') }}#about" class="text-gray-300 hover:text-white transition-colors">How it works</a></li>
                            <li><a href="{{ route('home') }}#contact" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
                            @auth
                                <li><a href="{{ route('dashboard') }}" wire:navigate class="text-gray-300 hover:text-white transition-colors">Dashboard</a></li>
                            @else
                                <li><a href="{{ route('register') }}" @click="openAuth('register', $event)" class="text-gray-300 hover:text-white transition-colors">List with us</a></li>
                            @endauth
                        </ul>
                    </div>

                    <div class="lg:col-span-3">
                        <h4 class="kicker !text-gray-500">New listings, weekly</h4>
                        <p class="text-sm mt-5 mb-4">One short email with the homes that went live this week. Nothing else.</p>
                        <livewire:public.newsletter-form />
                    </div>
                </div>

                <div class="py-6 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
                    <span>&copy; {{ date('Y') }} PropNest. All rights reserved.</span>
                    <span class="font-mono uppercase tracking-[0.14em]">Verified agents · Real prices · Direct messages</span>
                </div>
            </div>
        </footer>
    </body>
</html>
