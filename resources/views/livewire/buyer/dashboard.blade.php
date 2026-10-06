<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <header class="flex items-end justify-between gap-6 flex-wrap pb-8 border-b border-gray-900/10">
        <div>
            <p class="kicker mb-3">Your dashboard</p>
            <h1 class="display text-5xl sm:text-6xl">{{ $isFirstLogin ? 'Hello' : 'Welcome back' }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-[15px] text-gray-600 mt-3">Everything you have saved, in one place. Fresh listings on the right.</p>
        </div>
        <a href="{{ route('properties.index') }}" wire:navigate>
            <x-button variant="primary">Search homes</x-button>
        </a>
    </header>

    <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 mt-10">
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24 self-start">
            <div class="grid grid-cols-2 gap-4">
                <x-stat-card label="Favorites" :value="$favoritesCount" icon="heart" />
                <x-stat-card label="Saved Searches" :value="$savedSearchesCount" icon="bell" />
            </div>

            <nav class="bg-white rounded-xl border border-gray-900/10 px-5" aria-label="Shortcuts">
                <a href="{{ route('favorites.index') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Saved homes</span>
                        <span class="block text-sm text-gray-500 mt-0.5">The places you have hearted</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="{{ route('saved-searches.index') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Saved searches</span>
                        <span class="block text-sm text-gray-500 mt-0.5">Get told when a match is listed</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="{{ route('compare.index') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Compare</span>
                        <span class="block text-sm text-gray-500 mt-0.5">Up to three homes, side by side</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="{{ route('profile') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Account settings</span>
                        <span class="block text-sm text-gray-500 mt-0.5">Name, email and password</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
            </nav>
        </div>

        <section class="lg:col-span-8">
            <div class="flex items-baseline justify-between gap-4 mb-6">
                <h2 class="text-lg font-semibold text-primary-900">Just listed</h2>
                <a href="{{ route('properties.index') }}" wire:navigate class="text-sm text-gray-600 hover:text-primary-900 link-underline">See every home</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-10">
                @forelse ($listings as $property)
                    <x-property-card :property="$property" />
                @empty
                    <div class="col-span-full">
                        <x-empty-state title="Nothing listed right now" description="New homes are reviewed and published every week. Check back soon." />
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
