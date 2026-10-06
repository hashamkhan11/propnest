<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <x-page-header kicker="Saved searches" title="Searches on watch">
        <x-slot:description>{{ $savedSearches->count() }} saved {{ \Illuminate\Support\Str::plural('search', $savedSearches->count()) }}. Turn on alerts and we email you when a new home matches.</x-slot:description>
    </x-page-header>

    <div class="space-y-4">
        @forelse ($savedSearches as $index => $savedSearch)
            <div
                class="animate-fade-in-up bg-white rounded-xl border border-gray-900/10 hover:border-gray-900/25 transition-all p-4 sm:p-5"
                style="animation-delay: {{ min($index, 8) * 40 }}ms"
                wire:loading.class="opacity-40 pointer-events-none"
                wire:target="delete({{ $savedSearch->id }})"
            >
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="hidden sm:flex w-10 h-10 rounded-lg bg-primary-50 text-primary-900 items-center justify-center shrink-0">
                            <x-icon.house-search class="w-5 h-5" />
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5">
                                @foreach (explode(' · ', $savedSearch->label()) as $part)
                                    <x-badge variant="gray">{{ $part }}</x-badge>
                                @endforeach
                            </div>
                            <div class="text-sm text-gray-500 mt-1.5">Saved {{ $savedSearch->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 flex-wrap sm:flex-nowrap sm:justify-end sm:shrink-0">
                        <div
                            class="flex items-center gap-2.5"
                            wire:loading.class="opacity-50 pointer-events-none"
                            wire:target="toggleAlerts({{ $savedSearch->id }})"
                        >
                            <label class="inline-flex items-center gap-2.5 text-sm text-gray-600 cursor-pointer select-none">
                                <span class="relative inline-flex h-5 w-9 items-center shrink-0">
                                    <input type="checkbox" wire:click="toggleAlerts({{ $savedSearch->id }})" @checked($savedSearch->alerts_enabled) class="peer sr-only">
                                    <span class="absolute inset-0 rounded-full bg-gray-200 peer-checked:bg-primary-600 transition-colors duration-200"></span>
                                    <span class="absolute left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-4"></span>
                                </span>
                                Email alerts
                            </label>
                            @if ($savedSearch->alerts_enabled)
                                <x-badge variant="success">Active</x-badge>
                            @else
                                <x-badge variant="gray">Paused</x-badge>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 ml-auto sm:ml-0">
                            <a href="{{ $savedSearch->resultsUrl() }}" wire:navigate>
                                <x-button type="button" variant="secondary">View results</x-button>
                            </a>

                            <x-button
                                type="button"
                                variant="ghost-danger"
                                wire:loading.attr="disabled"
                                wire:target="delete({{ $savedSearch->id }})"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Delete this saved search?',
                                    message: 'You will stop receiving match alerts for this search.',
                                    variant: 'danger',
                                    confirmText: 'Delete',
                                    onConfirm: () =&gt; $wire.delete({{ $savedSearch->id }}),
                                })"
                            >
                                Delete
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <x-empty-state
                title="You haven't saved any searches yet"
                description="Save a search from the listings page to get notified about new matches automatically."
            >
                <x-slot name="icon">
                    <x-icon.house-search class="w-7 h-7" />
                </x-slot>
                <x-slot name="actions">
                    <a href="{{ route('properties.index') }}" wire:navigate>
                        <x-button type="button">Browse listings</x-button>
                    </a>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>
</div>
