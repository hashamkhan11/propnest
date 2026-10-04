<div class="max-w-xl mx-auto px-4 sm:px-6 py-10 sm:py-16">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-10 text-center animate-fade-in-up">

        @if ($paymentConfirmed)
            <div class="mx-auto w-16 h-16 rounded-full bg-primary-50 flex items-center justify-center text-primary-600 animate-heart-pop">
                <x-icon.check-circle class="w-9 h-9" />
            </div>

            <h1 class="font-heading font-800 text-2xl text-gray-900 mt-5">Subscription active!</h1>

            <p class="text-gray-600 mt-2 leading-relaxed">
                Your subscription is active until <span class="font-semibold text-accent-700">{{ $subscription->expires_at->format('M j, Y') }}</span>,
                with {{ $subscription->listing_limit !== null ? $subscription->listing_limit.' active listings' : 'unlimited listings' }}
                and {{ $subscription->featured_credits_remaining }} bundled featured {{ \Illuminate\Support\Str::plural('credit', $subscription->featured_credits_remaining) }}.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button class="w-full justify-center">View Subscription</x-button>
                </a>
                <a href="{{ route('agent.properties.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="secondary" class="w-full justify-center">Back to My Listings</x-button>
                </a>
            </div>

        @elseif ($subscription->status === \App\Enums\Subscription\AgentSubscriptionStatus::Failed)
            <div class="mx-auto w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                <x-icon.x-circle class="w-9 h-9" />
            </div>

            <h1 class="font-heading font-800 text-2xl text-gray-900 mt-5">Payment failed</h1>

            <p class="text-gray-600 mt-2 leading-relaxed">Your payment couldn't be completed. Your card has not been charged.</p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="accent" class="w-full justify-center">Try again</x-button>
                </a>
            </div>

        @elseif ($subscription->status === \App\Enums\Subscription\AgentSubscriptionStatus::Cancelled)
            <div class="mx-auto w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <x-icon.alert-circle class="w-9 h-9" />
            </div>

            <h1 class="font-heading font-800 text-2xl text-gray-900 mt-5">Checkout cancelled</h1>

            <p class="text-gray-600 mt-2 leading-relaxed">Checkout was cancelled &mdash; no subscription was activated. No charge was made.</p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="accent" class="w-full justify-center">Try again</x-button>
                </a>
            </div>

        @elseif ($subscription->status === \App\Enums\Subscription\AgentSubscriptionStatus::Expired)
            <div class="mx-auto w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <x-icon.clock class="w-9 h-9" />
            </div>

            <h1 class="font-heading font-800 text-2xl text-gray-900 mt-5">Checkout session expired</h1>

            <p class="text-gray-600 mt-2 leading-relaxed">This checkout session expired before payment was completed. Nothing was charged.</p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="accent" class="w-full justify-center">Try again</x-button>
                </a>
            </div>

        @else
            <div class="mx-auto w-16 h-16 rounded-full bg-sky-50 flex items-center justify-center text-sky-600">
                <svg class="animate-spin w-8 h-8" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
            </div>

            <h1 class="font-heading font-800 text-2xl text-gray-900 mt-5">Confirming your payment&hellip;</h1>

            <p class="text-gray-600 mt-2 leading-relaxed">
                We couldn't confirm your payment yet. If you completed checkout, please wait a moment and refresh this page.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-button type="button" onclick="window.location.reload()" class="w-full sm:w-auto justify-center">Refresh Status</x-button>
                <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="secondary" class="w-full justify-center">Back to Subscriptions</x-button>
                </a>
            </div>
        @endif

        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-center gap-2 text-xs text-gray-400">
            <x-icon.banknotes class="w-4 h-4" />
            <span>Payment reference &middot; <x-price :amount="$subscription->amount_cents / 100" class="font-medium text-gray-500" /></span>
        </div>
    </div>
</div>
