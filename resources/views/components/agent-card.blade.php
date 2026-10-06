@props(['agent'])

@php
    $profile = $agent->agentProfile;
    $verified = $profile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
    $listingsCount = $agent->active_listings_count ?? 0;
@endphp

<a
    href="{{ route('agents.show', $agent) }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'group flex flex-col bg-white rounded-xl border border-gray-900/10 hover:border-gray-900/25 p-5 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2']) }}
>
    <div class="flex items-center gap-4">
        <x-user-avatar :user="$agent" size="w-14 h-14" textClass="font-semibold text-lg" class="shrink-0" />
        <div class="min-w-0">
            <h3 class="text-[15px] font-semibold text-primary-900 truncate">{{ $agent->name }}</h3>
            <p class="text-sm text-gray-500 truncate">{{ $profile?->agency_name ?: 'Independent Agent' }}</p>
        </div>
    </div>

    <div class="mt-5 pt-4 border-t border-gray-900/10 flex items-center justify-between gap-3 text-sm">
        <span class="figure text-gray-600">{{ $listingsCount }} active {{ \Illuminate\Support\Str::plural('listing', $listingsCount) }}</span>
        @if ($verified)
            <span class="inline-flex items-center gap-1 text-emerald-700 text-[13px]">
                <x-icon.check-circle class="w-4 h-4" />
                Verified
            </span>
        @else
            <span class="text-[13px] text-gray-400">Not yet verified</span>
        @endif
    </div>
</a>
