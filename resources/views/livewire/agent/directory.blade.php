<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <x-page-header kicker="Agents" title="People who know the street">
            <x-slot:description>{{ $agents->total() }} {{ \Illuminate\Support\Str::plural('agent', $agents->total()) }}. Each one checked by our team before their first listing goes live.</x-slot:description>
        </x-page-header>

        <div class="bg-white rounded-xl border border-gray-900/10 p-4 sm:p-5 mb-8">
            <x-input-label for="keyword" value="Search by name or agency" class="mb-1.5" />
            <div class="relative max-w-md">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M18 10.5a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" />
                </svg>
                <x-input
                    wire:model.live.debounce.400ms="keyword"
                    id="keyword"
                    type="text"
                    class="pl-9"
                    placeholder="e.g. Jane Smith or Ace Realty"
                />
            </div>
        </div>

        <div wire:loading.class="opacity-50" wire:target="keyword" class="transition-opacity duration-150">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($agents as $agent)
                    <x-agent-card :agent="$agent" class="animate-fade-in-up" />
                @empty
                    <div class="col-span-full">
                        <x-empty-state title="No agents found" description="Try a different name or agency, or clear your search to browse all agents.">
                            <x-slot name="icon">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2.13a4 4 0 100-8" />
                                </svg>
                            </x-slot>
                            <x-slot name="actions">
                                @if ($keyword !== '')
                                    <x-button variant="secondary" wire:click="$set('keyword', '')">Clear search</x-button>
                                @endif
                            </x-slot>
                        </x-empty-state>
                    </div>
                @endforelse
            </div>
        </div>

        @if ($agents->hasPages())
            <div class="mt-8">
                {{ $agents->links() }}
            </div>
        @endif
    </div>
</div>
