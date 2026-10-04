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
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
        @php
            $adminNavGroups = [
                'Overview' => [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ],
                'People' => array_filter([
                    ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'users'],
                    auth()->user()->role === \App\Enums\User\UserRole::SuperAdmin
                        ? ['route' => 'admin.create-admin', 'label' => 'Create Admin', 'icon' => 'user-plus']
                        : null,
                    ['route' => 'admin.agents.index', 'label' => 'Agent Verification', 'icon' => 'identification'],
                ]),
                'Listings' => [
                    ['route' => 'admin.properties.index', 'label' => 'Properties', 'icon' => 'building-office-2'],
                    ['route' => 'admin.moderation.index', 'label' => 'Moderation Queue', 'icon' => 'shield-check'],
                    ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'tag'],
                    ['route' => 'admin.regions.index', 'label' => 'Regions & Cities', 'icon' => 'map'],
                ],
                'Revenue' => [
                    ['route' => 'admin.payments.index', 'label' => 'Payments & Refunds', 'icon' => 'banknotes'],
                    ['route' => 'admin.refund-requests.index', 'label' => 'Refund Requests', 'icon' => 'receipt-refund'],
                    ['route' => 'admin.featured-tiers.index', 'label' => 'Featured Pricing', 'icon' => 'star'],
                    ['route' => 'admin.subscription-plans.index', 'label' => 'Agent Subscriptions', 'icon' => 'rectangle-stack'],
                ],
                'Insights' => [
                    ['route' => 'admin.reports.index', 'label' => 'Listing Reports', 'icon' => 'flag'],
                    ['route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => 'chart-bar'],
                ],
                'Marketing' => [
                    ['route' => 'admin.contact-messages.index', 'label' => 'Contact Messages', 'icon' => 'envelope'],
                    ['route' => 'admin.subscribers.index', 'label' => 'Subscribers', 'icon' => 'megaphone'],
                ],
                'Settings' => [
                    ['route' => 'admin.settings.edit', 'label' => 'Site Settings', 'icon' => 'cog-6-tooth'],
                ],
            ];
        @endphp

        <div class="min-h-screen lg:flex">
            {{-- Desktop sidebar --}}
            <aside
                x-data="{ collapsed: false }"
                class="hidden lg:flex lg:flex-col lg:shrink-0 border-r border-gray-100 bg-white transition-[width] duration-200"
                :class="collapsed ? 'lg:w-20' : 'lg:w-64'"
            >
                <div class="flex items-center h-16 px-4 border-b border-gray-100 shrink-0 overflow-hidden">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <x-brand-logo icon-size="h-8 w-8" text-size="text-base" />
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 space-y-6">
                    @foreach ($adminNavGroups as $group => $links)
                        <div>
                            <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider text-gray-400 truncate" x-show="!collapsed">{{ $group }}</p>
                            <div class="space-y-1">
                                @foreach ($links as $link)
                                    <x-admin.sidebar-link :href="route($link['route'])" :active="request()->routeIs($link['route'])" :icon="$link['icon']">
                                        {{ $link['label'] }}
                                    </x-admin.sidebar-link>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>

                <div class="border-t border-gray-100 p-3">
                    <button @click="collapsed = !collapsed" class="w-full flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-xs font-medium text-gray-500 hover:bg-gray-100">
                        <x-heroicon-o-chevron-double-left class="h-4 w-4 transition-transform" ::class="{ '[transform:rotateY(180deg)]': collapsed }" />
                        <span x-show="!collapsed">Collapse</span>
                    </button>
                </div>
            </aside>

            {{-- Mobile / tablet drawer --}}
            <div x-show="sidebarOpen" x-cloak class="lg:hidden fixed inset-0 z-50" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-gray-900/50" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>
                <div
                    class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-white shadow-xl flex flex-col"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                >
                    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100 shrink-0">
                        <x-brand-logo icon-size="h-8 w-8" text-size="text-base" />
                        <button @click="sidebarOpen = false" class="p-2 text-gray-400 hover:text-gray-600">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>
                    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                        @foreach ($adminNavGroups as $group => $links)
                            <div>
                                <p class="px-3 mb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $group }}</p>
                                <div class="space-y-1">
                                    @foreach ($links as $link)
                                        <x-admin.sidebar-link :href="route($link['route'])" :active="request()->routeIs($link['route'])" :icon="$link['icon']" @click="sidebarOpen = false">
                                            {{ $link['label'] }}
                                        </x-admin.sidebar-link>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- Main column --}}
            <div class="flex-1 min-w-0 flex flex-col">
                <header class="lg:hidden flex items-center justify-between h-16 px-4 border-b border-gray-100 bg-white/95 backdrop-blur-sm sticky top-0 z-40">
                    <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 hover:text-gray-700">
                        <x-heroicon-o-bars-3 class="h-6 w-6" />
                    </button>
                    <x-brand-logo icon-size="h-7 w-7" text-size="text-sm" />
                    <a href="{{ route('dashboard') }}" wire:navigate class="p-2 -mr-2 text-gray-500 hover:text-gray-700" title="Exit Admin">
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
