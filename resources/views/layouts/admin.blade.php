<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ ($title ?? 'Admin') . ' - ' . config('app.name', 'PropNest') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-cream text-primary-900" x-data="{ sidebarOpen: false }">
        @php
            $adminNavGroups = [
                'Overview' => [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'squares-2x2'],
                    ['route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => 'chart-bar'],
                ],
                'People' => array_filter([
                    ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'users'],
                    ['route' => 'admin.agents.index', 'label' => 'Agent checks', 'icon' => 'identification'],
                    auth()->user()->role === \App\Enums\User\UserRole::SuperAdmin
                        ? ['route' => 'admin.create-admin', 'label' => 'Add an admin', 'icon' => 'user-plus']
                        : null,
                ]),
                'Listings' => [
                    ['route' => 'admin.moderation.index', 'label' => 'Review queue', 'icon' => 'shield-check'],
                    ['route' => 'admin.properties.index', 'label' => 'All listings', 'icon' => 'building-office-2'],
                    ['route' => 'admin.reports.index', 'label' => 'Reports', 'icon' => 'flag'],
                    ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'tag'],
                    ['route' => 'admin.regions.index', 'label' => 'Regions and cities', 'icon' => 'map'],
                ],
                'Money' => [
                    ['route' => 'admin.payments.index', 'label' => 'Payments', 'icon' => 'banknotes'],
                    ['route' => 'admin.refund-requests.index', 'label' => 'Refund requests', 'icon' => 'receipt-refund'],
                    ['route' => 'admin.featured-tiers.index', 'label' => 'Featured pricing', 'icon' => 'star'],
                    ['route' => 'admin.subscription-plans.index', 'label' => 'Agent plans', 'icon' => 'rectangle-stack'],
                ],
                'Audience' => [
                    ['route' => 'admin.contact-messages.index', 'label' => 'Messages', 'icon' => 'envelope'],
                    ['route' => 'admin.subscribers.index', 'label' => 'Subscribers', 'icon' => 'megaphone'],
                    ['route' => 'admin.settings.edit', 'label' => 'Site settings', 'icon' => 'cog-6-tooth'],
                ],
            ];
            $adminUser = auth()->user();
        @endphp

        <div class="min-h-screen lg:flex">
            {{-- Desktop sidebar --}}
            <aside
                x-data="{ collapsed: false }"
                class="hidden lg:block lg:shrink-0 bg-primary-900 text-white transition-[width] duration-200"
                :class="collapsed ? 'lg:w-[72px]' : 'lg:w-64'"
            >
                <div class="sticky top-0 h-screen flex flex-col">
                    <div class="flex items-center justify-between h-16 px-5 shrink-0 overflow-hidden">
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2 text-white">
                            <x-brand-logo tone="light" icon-size="h-8 w-8" text-size="text-[19px]" x-bind:class="collapsed && '[&>span]:hidden'" />
                        </a>
                        <span class="kicker !text-gray-500" x-show="!collapsed">Admin</span>
                    </div>

                    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 pt-1 pb-6 space-y-5 [scrollbar-width:thin] [scrollbar-color:#3a3733_transparent]">
                        @foreach ($adminNavGroups as $group => $links)
                            <div>
                                <p class="px-3 mb-1 kicker !text-gray-500 truncate" x-show="!collapsed">{{ $group }}</p>
                                <div class="space-y-px">
                                    @foreach ($links as $link)
                                        <x-admin.sidebar-link :href="route($link['route'])" :active="request()->routeIs($link['route'])" :icon="$link['icon']" :title="$link['label']">
                                            {{ $link['label'] }}
                                        </x-admin.sidebar-link>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>

                    <div class="border-t border-white/10 p-3 flex items-center gap-1" :class="collapsed && 'flex-col'">
                        <div class="flex-1 min-w-0 flex items-center gap-2.5 px-1" x-show="!collapsed">
                            <x-user-avatar :user="$adminUser" size="w-8 h-8" textClass="font-semibold text-xs" class="shrink-0" />
                            <div class="min-w-0">
                                <p class="text-[13px] font-medium leading-tight truncate">{{ $adminUser->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $adminUser->role->label() }}</p>
                            </div>
                        </div>
                        <a href="{{ route('home') }}" class="flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/[0.06] hover:text-white" title="View the public site">
                            <x-heroicon-o-arrow-up-right class="h-4 w-4" />
                        </a>
                        <button @click="collapsed = !collapsed" class="flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/[0.06] hover:text-white" :title="collapsed ? 'Expand' : 'Collapse'">
                            <x-heroicon-o-chevron-double-left class="h-4 w-4 transition-transform" ::class="{ 'rotate-180': collapsed }" />
                        </button>
                    </div>
                </div>
            </aside>

            {{-- Mobile / tablet drawer --}}
            <div x-show="sidebarOpen" x-cloak class="lg:hidden fixed inset-0 z-50" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-primary-900/60" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
                <div
                    class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-primary-900 text-white flex flex-col"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                >
                    <div class="flex items-center justify-between h-16 px-5 shrink-0">
                        <x-brand-logo tone="light" icon-size="h-8 w-8" text-size="text-[19px]" />
                        <button @click="sidebarOpen = false" class="p-2 -mr-2 text-gray-400 hover:text-white" aria-label="Close menu">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>
                    <nav class="flex-1 overflow-y-auto px-3 pt-2 pb-6 space-y-6">
                        @foreach ($adminNavGroups as $group => $links)
                            <div>
                                <p class="px-3 mb-1.5 kicker !text-gray-500">{{ $group }}</p>
                                <div class="space-y-0.5">
                                    @foreach ($links as $link)
                                        <x-admin.sidebar-link :href="route($link['route'])" :active="request()->routeIs($link['route'])" :icon="$link['icon']" @click="sidebarOpen = false">
                                            {{ $link['label'] }}
                                        </x-admin.sidebar-link>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>
                    <div class="border-t border-white/10 p-3">
                        <a href="{{ route('home') }}" class="flex items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-400 hover:bg-white/[0.06] hover:text-white">
                            <x-heroicon-o-arrow-up-right class="h-4 w-4" />
                            View site
                        </a>
                    </div>
                </div>
            </div>

            {{-- Main column --}}
            <div class="flex-1 min-w-0 flex flex-col">
                <header class="lg:hidden flex items-center justify-between h-14 px-4 bg-primary-900 text-white sticky top-0 z-40">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-300 hover:text-white" aria-label="Open menu">
                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                    </button>
                    <x-brand-logo tone="light" icon-size="h-7 w-7" text-size="text-base" />
                    <a href="{{ route('dashboard') }}" wire:navigate class="p-2 -mr-2 text-gray-300 hover:text-white" title="Leave admin">
                        <x-heroicon-o-arrow-left-on-rectangle class="h-5 w-5" />
                    </a>
                </header>

                <x-flash-messages />
                <x-toast-listener />

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-confirm-dialog />
    </body>
</html>
