@props(['agent'])

@php
    $profile = $agent->agentProfile;
    $verified = $profile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
    $listingsCount = $agent->active_listings_count ?? 0;
@endphp

<a
    href="{{ route('agents.show', $agent) }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'group block bg-white rounded-2xl shadow-sm hover:shadow-xl border border-gray-100 hover:border-primary-100 p-6 text-center transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2']) }}
>
    <div class="relative mx-auto w-16 h-16">
        <x-user-avatar :user="$agent" size="w-16 h-16" textClass="font-heading font-700 text-xl" class="ring-4 ring-white shadow-sm group-hover:scale-105 transition-transform duration-300" />

        @if ($verified)
            <span class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-primary-600 text-white flex items-center justify-center ring-2 ring-white" title="Verified Agent">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </span>
        @endif
    </div>

    <h3 class="font-heading font-700 text-gray-900 mt-4 group-hover:text-primary-700 transition-colors truncate">{{ $agent->name }}</h3>

    @if ($profile?->agency_name)
        <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $profile->agency_name }}</p>
    @else
        <p class="text-sm text-gray-400 mt-0.5">Independent Agent</p>
    @endif

    <div class="mt-4 flex items-center justify-center gap-2 flex-wrap">
        @if ($verified)
            <x-badge variant="success">Verified</x-badge>
        @endif
        <x-badge>{{ $listingsCount }} active {{ \Illuminate\Support\Str::plural('listing', $listingsCount) }}</x-badge>
    </div>
</a>
