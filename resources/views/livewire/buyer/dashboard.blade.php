<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="font-heading font-800 text-2xl text-gray-900 mb-1">{{ $isFirstLogin ? 'Hello' : 'Welcome back' }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
    <p class="text-gray-500 mb-6">Here's a quick overview of your property search.</p>

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $favoritesCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Favorites</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <x-icon.house-search class="w-5 h-5" />
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $savedSearchesCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Saved Searches</p>
        </div>
    </div>

    <x-card title="Quick Links">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <a href="{{ route('properties.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <x-icon.house-search class="w-5 h-5 text-primary-600" />
                <span class="text-xs font-semibold text-gray-700">Browse Properties</span>
            </a>

            <a href="{{ route('favorites.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                <span class="text-xs font-semibold text-gray-700">Favorites</span>
            </a>

            <a href="{{ route('saved-searches.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
                <span class="text-xs font-semibold text-gray-700">Saved Searches</span>
            </a>

            <a href="{{ route('compare.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v1.5M3 21v-6M21 21v-1.5M21 3v6M3 4.5h18M3 19.5h18M8 8v8M16 8v8" /></svg>
                <span class="text-xs font-semibold text-gray-700">Compare</span>
            </a>

            <a href="{{ route('profile') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <x-icon.user class="w-5 h-5 text-primary-600" />
                <span class="text-xs font-semibold text-gray-700">My Profile</span>
            </a>
        </div>
    </x-card>

    <x-card title="All Listings" class="mt-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($listings as $property)
                <x-property-card :property="$property" />
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500">No listings available right now — check back soon.</p>
                </div>
            @endforelse
        </div>
    </x-card>
</div>
