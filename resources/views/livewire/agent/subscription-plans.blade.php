<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="font-heading font-800 text-2xl text-gray-900 mb-1">Agent Subscriptions</h1>
    <p class="text-gray-500 mb-6">Subscribe to unlock more active listings and bundled featured credits.</p>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 mb-8">
        <h2 class="font-heading font-700 text-lg text-gray-900 mb-4">Your Status</h2>

        <div class="flex flex-wrap items-center gap-4 bg-gray-50 rounded-xl px-4 py-3 mb-4">
            <div class="shrink-0">
                <p class="font-heading font-800 text-2xl text-gray-900">{{ $activeListingCount }}</p>
                <p class="text-xs text-gray-500">active listings</p>
            </div>
            <div class="h-8 w-px bg-gray-200"></div>
            <div class="shrink-0">
                <p class="font-heading font-800 text-2xl text-gray-900">
                    {{ $activeSubscription->listing_limit ?? ($activeSubscription ? '∞' : ($freeListingLimit ?? '∞')) }}
                </p>
                <p class="text-xs text-gray-500">listing limit</p>
            </div>
            <div class="h-8 w-px bg-gray-200"></div>
            <div class="shrink-0">
                <p class="font-heading font-800 text-2xl text-gray-900">{{ $activeSubscription->featured_credits_remaining ?? 0 }}</p>
                <p class="text-xs text-gray-500">featured credits left</p>
            </div>
        </div>

        @if ($activeSubscription)
            <div class="flex items-center gap-2 text-sm text-primary-800 bg-primary-50 border border-primary-100 rounded-lg px-3 py-2">
                <x-icon.check-circle class="w-4 h-4 shrink-0" />
                <span>
                    Subscribed to <span class="font-semibold">{{ $activeSubscription->plan->name }}</span>
                    until {{ $activeSubscription->expires_at->format('M j, Y') }}.
                </span>
            </div>
        @else
            <p class="text-sm text-gray-500">You're on the free plan{{ $freeListingLimit !== null ? " ({$freeListingLimit} active listings, no featured credits)" : '' }}. Subscribe below for more capacity.</p>
        @endif

        @if ($pendingSubscription)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2">
                <span class="inline-flex items-center gap-2 text-amber-800 text-sm">
                    <x-icon.clock class="w-4 h-4 shrink-0" />
                    A payment for {{ $pendingSubscription->plan->name }} is in progress.
                </span>
                <x-button type="button" variant="secondary" wire:click="cancelPendingSubscription" wire:loading.attr="disabled" wire:target="cancelPendingSubscription">
                    Cancel pending payment
                </x-button>
            </div>
        @endif
    </div>

    @if ($plans->isEmpty())
        <x-empty-state
            title="No subscription plans available"
            description="Check back later — the admin team hasn't published any subscription plans yet."
        >
            <x-slot name="icon">
                <x-icon.star class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($plans as $plan)
                <div wire:key="plan-{{ $plan->id }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all p-5 sm:p-6 flex flex-col">
                    <div class="flex-1">
                        <h3 class="font-heading font-700 text-lg text-gray-900">{{ $plan->name }}</h3>

                        <div class="mt-3 font-heading font-800 text-3xl text-primary-800">
                            <x-price :amount="$plan->price_cents / 100" />
                        </div>

                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                            <x-icon.clock class="w-4 h-4 text-gray-400" />
                            {{ $plan->duration_days }} {{ \Illuminate\Support\Str::plural('day', $plan->duration_days) }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                            <x-icon.house-key class="w-4 h-4 text-gray-400" />
                            {{ $plan->listing_limit !== null ? $plan->listing_limit.' active listings' : 'Unlimited listings' }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                            <x-icon.star class="w-4 h-4 text-gray-400" />
                            {{ $plan->featured_credits }} bundled featured {{ \Illuminate\Support\Str::plural('credit', $plan->featured_credits) }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <x-button
                            type="button"
                            class="w-full justify-center"
                            wire:loading.attr="disabled"
                            wire:target="subscribe"
                            :disabled="$activeSubscription !== null || $pendingSubscription !== null"
                            x-on:click="$store.confirmDialog.open({
                                title: 'Subscribe to {{ $plan->name }}?',
                                message: 'You will be redirected to Stripe to complete payment.',
                                confirmText: 'Continue to payment',
                                onConfirm: () =&gt; $wire.subscribe({{ $plan->id }}),
                            })"
                        >
                            <span wire:loading.remove wire:target="subscribe">Subscribe</span>
                            <span wire:loading wire:target="subscribe" class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                Redirecting&hellip;
                            </span>
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
