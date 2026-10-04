<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-primary-600">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <a href="{{ route('agents.index') }}" wire:navigate class="hover:text-primary-600">Find an Agent</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <span class="text-gray-700 font-medium truncate">{{ $agent->name }}</span>
        </nav>

        @php
            $profile = $agent->agentProfile;
            $verified = $profile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
        @endphp

        {{-- Profile header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in-up">
            <div class="h-24 sm:h-28 bg-gradient-to-r from-primary-800 via-primary-700 to-primary-800 relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, white 0, transparent 40%);"></div>
            </div>

            <div class="px-5 sm:px-8 pt-4 sm:pt-5 pb-6 sm:pb-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                        <div class="relative shrink-0 -mt-14 sm:-mt-16">
                            <x-user-avatar :user="$agent" size="w-20 h-20 sm:w-24 sm:h-24" textClass="font-heading font-800 text-2xl sm:text-3xl" class="ring-4 ring-white shadow-md" />
                            @if ($verified)
                                <span class="absolute bottom-0.5 right-0.5 w-7 h-7 rounded-full bg-primary-600 text-white flex items-center justify-center ring-4 ring-white" title="Verified Agent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </span>
                            @endif
                        </div>

                        <div class="pb-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h1 class="font-heading font-800 text-2xl text-gray-900">{{ $agent->name }}</h1>
                                @if ($verified)
                                    <x-badge variant="success">Verified</x-badge>
                                @endif
                            </div>

                            @if ($profile?->agency_name)
                                <p class="text-gray-600 font-medium mt-0.5">{{ $profile->agency_name }}</p>
                            @else
                                <p class="text-gray-400 mt-0.5">Independent Agent</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pb-1">
                        <div class="bg-primary-50 rounded-xl px-4 py-2.5 text-center">
                            <div class="font-heading font-800 text-xl text-primary-800">{{ $listingCount }}</div>
                            <div class="text-xs text-primary-700 font-medium uppercase tracking-wide">Active {{ \Illuminate\Support\Str::plural('listing', $listingCount) }}</div>
                        </div>
                    </div>
                </div>

                @if ($profile?->phone || $profile?->bio)
                    <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="sm:col-span-1 space-y-3">
                            @if ($profile?->phone)
                                <div class="flex items-center gap-2.5 text-sm text-gray-600">
                                    <span class="w-8 h-8 rounded-full bg-primary-50 text-primary-700 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106a1.125 1.125 0 00-1.161.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97a1.125 1.125 0 00.417-1.16L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                        </svg>
                                    </span>
                                    <span>{{ $profile->phone }}</span>
                                </div>
                            @endif
                        </div>

                        @if ($profile?->bio)
                            <div class="sm:col-span-2">
                                <h2 class="font-heading font-700 text-sm text-gray-900 uppercase tracking-wide mb-2">About</h2>
                                <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $profile->bio }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Agent listings --}}
        <div class="mt-10">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <p class="font-heading font-700 text-accent-600 text-xs uppercase tracking-[0.15em] mb-1">Portfolio</p>
                    <h2 class="font-heading font-800 text-xl sm:text-2xl text-gray-900">Listings by {{ $agent->name }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
