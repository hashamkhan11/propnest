<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-1.9C4.045 12.733 2 10.352 2 7.5 2 5.015 3.989 3 6.5 3c1.343 0 2.622.68 3.5 1.72C10.878 3.68 12.157 3 13.5 3 16.011 3 18 5.015 18 7.5c0 2.852-2.045 5.233-3.885 6.82a22.049 22.049 0 01-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 01-.69.001l-.002-.001z" />
                </svg>
            </div>
            <div>
                <h1 class="font-heading font-800 text-2xl sm:text-3xl text-gray-900">My Favorites</h1>
                <p class="text-gray-500 mt-0.5">{{ $favorites->total() }} saved {{ \Illuminate\Support\Str::plural('listing', $favorites->total()) }}</p>
            </div>
        </div>

        @if (! $favorites->isEmpty())
            <a href="{{ route('properties.index') }}" wire:navigate class="self-start sm:self-auto">
                <x-button type="button" variant="secondary">Browse more listings</x-button>
            </a>
        @endif
    </div>

    @if ($favorites->isEmpty())
        <x-empty-state
            title="You haven't favorited any listings yet"
            description="Tap the heart icon on any listing to save it here and keep track of the places you love."
        >
            <x-slot name="icon">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('properties.index') }}" wire:navigate>
                    <x-button type="button">Browse listings</x-button>
                </a>
            </x-slot>
        </x-empty-state>
    @else
        <div wire:loading.class="opacity-50 pointer-events-none" class="transition-opacity duration-150">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($favorites as $index => $favorite)
                    <div class="animate-fade-in-up" style="animation-delay: {{ min($index, 8) * 40 }}ms">
                        <x-property-card :property="$favorite->property" />
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
