<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <x-page-header kicker="Plans" title="Room to grow" description="More active listings and featured credits each month. Cancel any time, keep what you have published." />

    <section class="mb-12">
        <h2 class="kicker mb-4">Where you stand</h2>
        <dl class="grid grid-cols-3 gap-px bg-gray-900/10 border-y border-gray-900/10 mb-4">
            <div class="bg-cream py-5 pr-4">
                <dt class="text-sm text-gray-600">Active listings</dt>
                <dd class="figure text-3xl font-semibold tracking-tight text-primary-900 mt-1">{{ $activeListingCount }}</dd>
            </div>
            <div class="bg-cream py-5 px-4">
                <dt class="text-sm text-gray-600">Listing limit</dt>
                <dd class="figure text-3xl font-semibold tracking-tight text-primary-900 mt-1">{{ $activeSubscription->listing_limit ?? ($activeSubscription ? '∞' : ($freeListingLimit ?? '∞')) }}</dd>
            </div>
            <div class="bg-cream py-5 px-4">
                <dt class="text-sm text-gray-600">Featured credits left</dt>
                <dd class="figure text-3xl font-semibold tracking-tight text-primary-900 mt-1">{{ $activeSubscription->featured_credits_remaining ?? 0 }}</dd>
            </div>
        </dl>

        @if ($activeSubscription)
            <div class="flex items-center gap-2 text-sm text-primary-900">
                <x-icon.check-circle class="w-4 h-4 shrink-0 text-emerald-700" />
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
    </section>

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
                @php($isCurrent = $activeSubscription && $activeSubscription->subscription_plan_id === $plan->id)
                <div wire:key="plan-{{ $plan->id }}" class="relative bg-white rounded-xl border {{ $isCurrent ? 'border-primary-900 ring-1 ring-primary-900' : 'border-gray-900/10' }} p-5 sm:p-6 flex flex-col">
                    @if ($isCurrent)
                        <span class="absolute -top-2.5 left-5 kicker !text-white bg-primary-900 rounded px-2 py-0.5">Your plan</span>
                    @endif
                    <div class="flex-1">
                        <h3 class="font-semibold text-lg text-primary-900">{{ $plan->name }}</h3>

                        <div class="mt-3 figure font-semibold tracking-tight text-4xl text-primary-900">
                            <x-price whole :amount="$plan->price_cents / 100" />
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

                    <div class="mt-5 pt-4 border-t border-gray-900/10">
                        <x-button
                            type="button"
                            :variant="$activeSubscription ? 'secondary' : 'primary'"
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
                            <span wire:loading.remove wire:target="subscribe">{{ $isCurrent ? 'Current plan' : ($activeSubscription ? 'Available when this one ends' : 'Choose '.$plan->name) }}</span>
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
