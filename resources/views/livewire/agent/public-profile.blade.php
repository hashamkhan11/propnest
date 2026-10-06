<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <nav class="kicker flex items-center gap-2 mb-8" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-primary-900">Home</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('agents.index') }}" wire:navigate class="hover:text-primary-900">Agents</a>
            <span class="text-gray-300">/</span>
            <span class="text-primary-900 truncate">{{ $agent->name }}</span>
        </nav>

        @php
            $profile = $agent->agentProfile;
            $verified = $profile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
        @endphp

        <header class="grid lg:grid-cols-12 gap-8 lg:gap-12 pb-10 border-b border-gray-900/10">
            <div class="lg:col-span-7 flex items-start gap-5 sm:gap-6">
                <x-user-avatar :user="$agent" size="w-20 h-20 sm:w-24 sm:h-24" textClass="font-semibold text-2xl sm:text-3xl" class="shrink-0" />
                <div class="min-w-0">
                    <p class="kicker mb-2">{{ $profile?->agency_name ?: 'Independent Agent' }}</p>
                    <h1 class="display text-5xl sm:text-6xl">{{ $agent->name }}</h1>
                    <div class="mt-4 flex items-center gap-x-4 gap-y-2 flex-wrap text-sm text-gray-600">
                        @if ($verified)
                            <span class="inline-flex items-center gap-1.5 text-emerald-700">
                                <x-icon.check-circle class="w-4 h-4" />
                                Verified agent
                            </span>
                        @endif
                        <span><span class="figure font-medium text-primary-900">{{ $listingCount }}</span> active {{ \Illuminate\Support\Str::plural('listing', $listingCount) }}</span>
                        @if ($profile?->phone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $profile->phone) }}" class="figure hover:text-primary-900 link-underline">{{ $profile->phone }}</a>
                        @endif
                    </div>
                </div>
            </div>

            @if ($profile?->bio)
                <div class="lg:col-span-5">
                    <h2 class="kicker mb-3">About</h2>
                    <p class="text-[17px] leading-relaxed text-gray-700 whitespace-pre-line">{{ $profile->bio }}</p>
                </div>
            @endif
        </header>

        {{-- Agent listings --}}
        <div class="mt-12">
            <div class="flex items-baseline justify-between mb-6">
                <h2 class="text-lg font-semibold text-primary-900">On the market with {{ explode(' ', $agent->name)[0] }}</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                @forelse ($listings as $property)
                    <x-property-card :property="$property" />
                @empty
                    <div class="col-span-full">
                        <x-empty-state title="No published listings yet" description="{{ $agent->name }} hasn't published any listings at the moment. Check back soon.">
                            <x-slot name="icon">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" />
                                </svg>
                            </x-slot>
                        </x-empty-state>
                    </div>
                @endforelse
            </div>

            @if ($listings->hasPages())
                <div class="mt-8">
                    {{ $listings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
