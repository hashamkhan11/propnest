<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <x-page-header kicker="Saved homes" title="Your shortlist">
        <x-slot:description>{{ $favorites->total() }} {{ \Illuminate\Support\Str::plural('home', $favorites->total()) }} saved. Tap the heart on any listing to add or remove it.</x-slot:description>
        @if (! $favorites->isEmpty())
            <x-slot:actions>
                <a href="{{ route('properties.index') }}" wire:navigate><x-button type="button" variant="secondary">Keep looking</x-button></a>
            </x-slot:actions>
        @endif
    </x-page-header>

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
