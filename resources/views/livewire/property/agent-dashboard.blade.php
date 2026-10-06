<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <header class="flex items-end justify-between gap-6 flex-wrap pb-8 border-b border-gray-900/10">
        <div>
            <p class="kicker mb-3">Agent workspace</p>
            <h1 class="display text-5xl sm:text-6xl">Good to see you, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-[15px] text-gray-600 mt-3">How your listings are doing, and what needs you today.</p>
        </div>
        <a href="{{ route('agent.properties.create') }}" wire:navigate>
            <x-button variant="accent">New listing</x-button>
        </a>
    </header>

    @if ($profileIncomplete)
        <div class="flex items-center justify-between gap-4 flex-wrap bg-white border border-gray-900/10 border-l-2 border-l-accent-500 rounded-lg px-5 py-4 mt-8">
            <div class="flex items-start gap-3">
                <x-icon.alert-circle class="w-5 h-5 shrink-0 text-accent-600 mt-0.5" />
                <div>
                    <p class="text-[15px] font-medium text-primary-900">Complete your agent profile</p>
                    <p class="text-sm text-gray-600">Buyers trust a face and a name. Add a photo, your agency and a short bio.</p>
                </div>
            </div>
            <a href="{{ route('profile') }}" wire:navigate>
                <x-button variant="secondary">Finish profile</x-button>
            </a>
        </div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-10">
        <x-stat-card label="Live listings" :value="$publishedCount" icon="check-circle" />
        <x-stat-card label="Unread inquiries" :value="$unreadInquiriesCount" icon="inbox" />
        <x-stat-card label="Listing views" :value="number_format($totalViewsCount)" icon="eye" />
        <x-stat-card label="Inquiries, all time" :value="$totalInquiriesCount" icon="chat-bubble-left" />
    </div>

    @php
        $rows = [
            ['All listings', $totalCount, route('agent.properties.index')],
            ['Live', $publishedCount, route('agent.properties.index', ['statuses' => ['published']])],
            ['Drafts', $draftCount, route('agent.properties.index', ['statuses' => ['draft']])],
            ['Sold or rented', $soldRentedCount, route('agent.properties.index', ['statuses' => ['sold', 'rented']])],
        ];
    @endphp

    <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 mt-12">
        <section class="lg:col-span-5">
            <h2 class="text-lg font-semibold text-primary-900 mb-4">Your listings</h2>
            <div class="bg-white rounded-xl border border-gray-900/10 divide-y divide-gray-900/10 overflow-hidden">
                @foreach ($rows as [$label, $count, $href])
                    <a href="{{ $href }}" wire:navigate class="group flex items-center justify-between px-5 py-4 hover:bg-cream/60 transition-colors">
                        <span class="text-[15px] text-gray-700 group-hover:text-primary-900">{{ $label }}</span>
                        <span class="figure text-[15px] font-semibold text-primary-900">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="lg:col-span-7">
            <h2 class="text-lg font-semibold text-primary-900 mb-4">Shortcuts</h2>
            <nav class="bg-white rounded-xl border border-gray-900/10 px-5" aria-label="Shortcuts">
                <a href="{{ route('agent.properties.create') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">List a new home</span>
                        <span class="block text-sm text-gray-500 mt-0.5">Photos, price and details. Reviewed within a day.</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="{{ route('agent.inquiries.index') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Answer inquiries</span>
                        <span class="block text-sm text-gray-500 mt-0.5">Buyers waiting on a reply</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Plans and featuring</span>
                        <span class="block text-sm text-gray-500 mt-0.5">Put a listing at the top of search</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
                <a href="{{ route('profile') }}" wire:navigate class="group flex items-center justify-between gap-4 py-4 border-b border-gray-900/10 last:border-b-0">
                    <span>
                        <span class="block text-[15px] font-medium text-primary-900">Public profile</span>
                        <span class="block text-sm text-gray-500 mt-0.5">What buyers see next to your listings</span>
                    </span>
                    <svg class="w-4 h-4 shrink-0 text-gray-400 group-hover:text-accent-600 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" /></svg>
                </a>
            </nav>
        </section>
    </div>
</div>
