<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="font-heading font-800 text-2xl sm:text-3xl text-gray-900">My Listings</h1>
            <p class="text-gray-500 mt-1">{{ $properties->total() }} {{ \Illuminate\Support\Str::plural('listing', $properties->total()) }}</p>
        </div>

        <a href="{{ route('agent.properties.create') }}" wire:navigate>
            <x-button>+ New Listing</x-button>
        </a>
    </div>

    @if ($statuses !== [])
        <div class="flex items-center gap-2 mb-6">
            <span class="inline-flex items-center gap-1.5 bg-primary-50 text-primary-800 border border-primary-100 text-sm font-medium pl-3 pr-1.5 py-1.5 rounded-full">
                {{ $this->filterLabel() }}
                <button type="button" wire:click="clearFilter" class="w-4 h-4 rounded-full flex items-center justify-center hover:bg-primary-100 transition-colors" aria-label="Clear filter">&times;</button>
            </span>
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($properties as $property)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-primary-100 transition-all p-4 sm:p-5">
                <div class="flex items-start gap-3 sm:gap-4">
                    <div class="shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-gray-100 ring-1 ring-gray-100">
                        @if ($property->coverImage)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($property->coverImage->thumbnailDisplayPath()) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" /></svg>
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-heading font-700 text-gray-900 truncate">{{ $property->title }}</span>
                            <x-badge :variant="$property->status->badgeVariant()">
                                {{ \Illuminate\Support\Str::headline($property->status->value) }}
                            </x-badge>
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5">{{ ucfirst($property->property_type->value) }}</p>
                        <p class="font-heading font-700 text-lg text-gray-900 mt-1">
                            <x-price :amount="$property->price" />
                        </p>

                        <div class="flex items-center gap-4 text-xs text-gray-500 mt-1.5">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $property->views_count }} {{ \Illuminate\Support\Str::plural('view', $property->views_count) }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                                {{ $property->inquiries_count }} {{ \Illuminate\Support\Str::plural('inquiry', $property->inquiries_count) }}
                            </span>
                        </div>

                        @if ($property->status === \App\Enums\Property\PropertyStatus::Rejected && $property->rejection_reason)
                            <p class="text-sm text-red-700 mt-1.5">Reason: {{ $property->rejection_reason }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('agent.properties.edit', $property) }}" wire:navigate>
                            <x-button type="button" variant="secondary">Edit</x-button>
                        </a>

                        <x-dropdown align="right" width="w-56">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-colors" aria-label="More actions">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if (in_array($property->status, [\App\Enums\Property\PropertyStatus::Sold, \App\Enums\Property\PropertyStatus::Rented, \App\Enums\Property\PropertyStatus::Archived], true))
                                    <button
                                        type="button"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                        x-on:click="$store.confirmDialog.open({
                                            title: 'Re-list this property?',
                                            message: 'It will be submitted for review again before going live.',
                                            confirmText: 'Re-list',
                                            onConfirm: () =&gt; $wire.relist({{ $property->id }}),
                                        })"
                                    >
                                        Re-list
                                    </button>
                                @endif

                                @foreach ($property->status->validTransitions() as $target)
                                    <button
                                        type="button"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                                        x-on:click="$store.confirmDialog.open({
                                            title: 'Move to {{ \Illuminate\Support\Str::headline($target->value) }}?',
                                            message: 'This updates the listing status immediately.',
                                            confirmText: 'Move to {{ \Illuminate\Support\Str::headline($target->value) }}',
                                            onConfirm: () =&gt; $wire.transition({{ $property->id }}, '{{ $target->value }}'),
                                        })"
                                    >
                                        {{ \Illuminate\Support\Str::headline($target->value) }}
                                    </button>
                                @endforeach

                                <div class="border-t border-gray-100 my-1"></div>

                                <button
                                    type="button"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                                    x-on:click="$store.confirmDialog.open({
                                        title: 'Delete this listing?',
                                        message: 'This permanently deletes the listing and its photos. This cannot be undone.',
                                        variant: 'danger',
                                        confirmText: 'Delete permanently',
                                        onConfirm: () =&gt; $wire.delete({{ $property->id }}),
                                    })"
                                >
                                    Delete
                                </button>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>

                @if ($property->status === \App\Enums\Property\PropertyStatus::Published)
                    @php($latestFeaturedPayment = $property->latestFeaturedPayment)

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        @if ($property->is_featured && $property->featured_until && $property->featured_until->isFuture())
                            {{-- Currently featured: status banner + any refund sub-state --}}
                            <div class="rounded-xl border border-accent-100 bg-accent-50/70 px-3 sm:px-4 py-3 flex items-center justify-between gap-3 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 text-accent-800 text-sm font-semibold">
                                    <x-icon.star class="w-4 h-4 text-accent-500" />
                                    Featured
                                    <span class="text-accent-600/70 font-normal">&middot; until {{ $property->featured_until->format('M j, Y') }}</span>
                                    <span class="text-accent-600/70 font-normal hidden sm:inline">({{ now()->diffInDays($property->featured_until) }}d left)</span>
                                </span>

                                @php($featuredByPaidPayment = $latestFeaturedPayment && $latestFeaturedPayment->status === \App\Enums\Payment\PaymentStatus::Completed && $latestFeaturedPayment->featured_until?->equalTo($property->featured_until))
                                @if ($featuredByPaidPayment)
                                    @php($latestRefundRequest = $latestFeaturedPayment->latestRefundRequest)
                                    @if ($latestRefundRequest && $latestRefundRequest->status === \App\Enums\RefundRequest\RefundRequestStatus::Pending)
                                        <x-badge variant="info">Refund Requested</x-badge>
                                    @elseif ($requestingRefundPropertyId === $property->id)
                                        <div class="w-full space-y-2 text-left">
                                            <textarea wire:model="refundReason" rows="2" placeholder="Why are you requesting a refund?"
                                                      class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full text-sm"></textarea>
                                            <x-input-error :messages="$errors->get('refundReason')" />
                                            <div class="flex gap-2">
                                                <x-button wire:click="submitRefundRequest({{ $property->id }})" wire:loading.attr="disabled" wire:target="submitRefundRequest">Submit Request</x-button>
                                                <x-button variant="secondary" wire:click="cancelRefundRequest">Cancel</x-button>
                                            </div>
                                        </div>
                                    @elseif ($latestRefundRequest && $latestRefundRequest->status === \App\Enums\RefundRequest\RefundRequestStatus::Rejected)
                                        <x-badge variant="danger">Refund Request Rejected</x-badge>
                                    @else
                                        <x-button type="button" variant="ghost-danger" wire:click="startRefundRequest({{ $property->id }})">
                                            Request Refund
                                        </x-button>
                                    @endif
                                @endif
                            </div>
                        @elseif ($property->pendingPayment)
                            {{-- Payment in progress --}}
                            <div class="rounded-xl border border-amber-100 bg-amber-50/70 px-3 sm:px-4 py-3 flex items-center justify-between gap-3 flex-wrap">
                                <span class="inline-flex items-center gap-1.5 text-amber-700 text-sm font-semibold">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    Payment processing
                                </span>
                                <x-button
                                    type="button"
                                    variant="secondary"
                                    wire:loading.attr="disabled"
                                    wire:target="cancelPendingPayment"
                                    x-on:click="$store.confirmDialog.open({
                                        title: 'Cancel pending payment?',
                                        message: 'If you already paid, we will check with Stripe first and feature the listing instead of cancelling.',
                                        confirmText: 'Cancel payment',
                                        cancelText: 'Keep waiting',
                                        onConfirm: () =&gt; $wire.cancelPendingPayment({{ $property->id }}),
                                    })"
                                >
                                    <span wire:loading.remove wire:target="cancelPendingPayment">Cancel pending payment</span>
                                    <span wire:loading wire:target="cancelPendingPayment" class="inline-flex items-center gap-1.5">
                                        <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                        Checking&hellip;
                                    </span>
                                </x-button>
                            </div>
                        @elseif ($activeTiers->isEmpty())
                            <span class="text-xs text-gray-400 italic">Featuring is currently unavailable.</span>
                        @else
                            {{-- Not featured: expandable Featured section (credits + plans) --}}
                            @php($canUseCredit = ! $property->pendingPayment && $activeSubscription && $activeSubscription->featured_credits_remaining > 0)

                            @if ($latestFeaturedPayment && $latestFeaturedPayment->status === \App\Enums\Payment\PaymentStatus::Refunded)
                                <x-badge variant="gray" class="mb-2">Refunded</x-badge>
                            @endif

                            <div x-data="{ open: {{ $canUseCredit ? 'true' : 'false' }}, tierId: {{ $activeTiers->first()->id }} }" class="rounded-xl border border-gray-200 overflow-hidden">
                                @if ($canUseCredit)
                                    <div class="flex items-center justify-between gap-3 px-3 sm:px-4 py-3 bg-primary-50/60">
                                        <span class="inline-flex items-center gap-2 min-w-0">
                                            <span class="shrink-0 w-6 h-6 rounded-full bg-primary-600 text-white flex items-center justify-center">
                                                <x-icon.star class="w-3.5 h-3.5" />
                                            </span>
                                            <span class="text-sm font-semibold text-gray-800 truncate">Use Featured Credit ({{ $activeSubscription->featured_credits_remaining }} left)</span>
                                        </span>
                                        <span class="inline-flex items-center gap-2 shrink-0">
                                            <button
                                                type="button"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="opacity-60"
                                                wire:target="featureWithCredit"
                                                x-on:click="$store.confirmDialog.open({
                                                    title: 'Feature this listing with a credit?',
                                                    message: 'This will use one of your {{ $activeSubscription->featured_credits_remaining }} remaining subscription featured credits. No payment required.',
                                                    confirmText: 'Use credit',
                                                    onConfirm: () =&gt; $wire.featureWithCredit({{ $property->id }}),
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 rounded-full border border-primary-600 text-primary-700 text-[11px] font-bold tracking-wide uppercase hover:bg-primary-600 hover:text-white transition-colors"
                                            >
                                                Use Credit
                                            </button>
                                            <button type="button" @click="open = ! open" class="w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-700" aria-label="Toggle featured plans">
                                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            </button>
                                        </span>
                                    </div>
                                @endif

                                <div @if ($canUseCredit) x-show="open" x-transition class="border-t border-gray-200" @endif>
                                    <div class="p-3 sm:p-4 space-y-3">
                                        <div class="flex items-stretch gap-2">
                                            @foreach ($activeTiers as $tier)
                                                <button
                                                    type="button"
                                                    @click="tierId = {{ $tier->id }}"
                                                    :class="tierId === {{ $tier->id }} ? 'border-primary-600 bg-primary-50' : 'border-gray-200 hover:border-primary-300'"
                                                    class="relative flex-1 min-w-0 border rounded-lg px-3 py-2 text-left transition-colors"
                                                >
                                                    <span class="block text-xs font-semibold text-gray-800 truncate pr-4">{{ $tier->name }} &middot; {{ $tier->duration_days }}d &middot; <x-price :amount="$tier->price_cents / 100" /></span>
                                                    <svg x-show="tierId === {{ $tier->id }}" x-cloak class="absolute top-2 right-2 w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75l2.25 2.25 4.5-4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </button>
                                            @endforeach
                                        </div>

                                        <x-button
                                            type="button"
                                            variant="accent"
                                            class="w-full justify-center"
                                            wire:loading.attr="disabled"
                                            wire:target="feature"
                                            x-on:click="$store.confirmDialog.open({
                                                title: 'Feature this listing?',
                                                message: 'You will be redirected to Stripe to complete payment for the selected plan.',
                                                confirmText: 'Continue to payment',
                                                onConfirm: () =&gt; $wire.feature({{ $property->id }}, tierId),
                                            })"
                                        >
                                            <span wire:loading.remove wire:target="feature" class="inline-flex items-center gap-1.5">
                                                <x-icon.star class="w-3.5 h-3.5" />
                                                Feature Listing
                                            </span>
                                            <span wire:loading wire:target="feature" class="inline-flex items-center gap-1.5">
                                                <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                                Redirecting to checkout&hellip;
                                            </span>
                                        </x-button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <x-empty-state
                :title="$statuses !== [] ? 'No listings match this filter' : 'You haven\'t created any listings yet'"
                :description="$statuses !== [] ? 'Try clearing the filter to see all of your listings.' : 'Create your first listing to start reaching buyers.'"
            >
                <x-slot name="icon">
                    <x-icon.house-search class="w-7 h-7" />
                </x-slot>
                <x-slot name="actions">
                    @if ($statuses !== [])
                        <x-button type="button" variant="secondary" wire:click="clearFilter">Clear filter</x-button>
                    @else
                        <a href="{{ route('agent.properties.create') }}" wire:navigate>
                            <x-button type="button">+ New Listing</x-button>
                        </a>
                    @endif
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $properties->links() }}
    </div>
</div>
