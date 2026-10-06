<div class="max-w-xl mx-auto px-4 sm:px-6 py-10 sm:py-16">
    <div class="bg-white rounded-xl border border-gray-900/10 p-6 sm:p-10 text-center animate-fade-in-up">

        @if ($paymentConfirmed)
            <div class="mx-auto w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-700 animate-heart-pop">
                <x-icon.check-circle class="w-9 h-9" />
            </div>

            <h1 class="display text-4xl mt-6">You are featured</h1>

            <p class="text-[15px] text-gray-600 mt-3 leading-relaxed">
                Your listing <span class="font-semibold text-gray-900">&ldquo;{{ $property->title }}&rdquo;</span>
                is now featured until <span class="font-medium text-primary-900">{{ $property->featured_until->format('M j, Y') }}</span>.
            </p>

            <div class="mt-5 inline-flex items-center gap-1.5 bg-accent-50 text-accent-800 border border-accent-100 rounded-full pl-2 pr-3 py-1 text-xs font-medium">
                <x-icon.star class="w-3.5 h-3.5 text-accent-500" />
                @php($daysLeft = max(0, (int) ceil(now()->diffInDays($property->featured_until))))
                Featured &middot; {{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} left
            </div>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('properties.show', $property) }}" wire:navigate class="w-full sm:w-auto">
                    <x-button class="w-full justify-center">See the listing</x-button>
                </a>
                <a href="{{ route('agent.properties.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="secondary" class="w-full justify-center">Back to your listings</x-button>
                </a>
            </div>

        @elseif ($payment->status === \App\Enums\Payment\PaymentStatus::Failed)
            <div class="mx-auto w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                <x-icon.x-circle class="w-9 h-9" />
            </div>

            <h1 class="display text-4xl mt-6">Payment failed</h1>

            <p class="text-[15px] text-gray-600 mt-3 leading-relaxed">
                Your payment couldn't be completed{{ $payment->failure_reason ? ": {$payment->failure_reason}" : '.' }}
            </p>

            <p class="text-sm text-gray-500 mt-1">Your card was not charged. You can try again with the same card or another one.</p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.properties.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="accent" class="w-full justify-center">Try again from My Listings</x-button>
                </a>
            </div>

        @elseif ($payment->status === \App\Enums\Payment\PaymentStatus::Cancelled)
            <div class="mx-auto w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <x-icon.alert-circle class="w-9 h-9" />
            </div>

            <h1 class="display text-4xl mt-6">Checkout cancelled</h1>

            <p class="text-[15px] text-gray-600 mt-3 leading-relaxed">
                Checkout was cancelled, so your listing was not featured and nothing was charged.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.properties.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="accent" class="w-full justify-center">Try again from My Listings</x-button>
                </a>
            </div>

        @elseif ($payment->status === \App\Enums\Payment\PaymentStatus::Expired)
            <div class="mx-auto w-16 h-16 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                <x-icon.clock class="w-9 h-9" />
            </div>

            <h1 class="display text-4xl mt-6">Checkout timed out</h1>

            <p class="text-[15px] text-gray-600 mt-3 leading-relaxed">
                This checkout session expired before payment was completed. Nothing was charged.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('agent.properties.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="accent" class="w-full justify-center">Try again from My Listings</x-button>
                </a>
            </div>

        @else
            <div class="mx-auto w-16 h-16 rounded-full bg-sky-50 flex items-center justify-center text-sky-600">
                <svg class="animate-spin w-8 h-8" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
            </div>

            <h1 class="display text-4xl mt-6">Confirming your payment&hellip;</h1>

            <p class="text-[15px] text-gray-600 mt-3 leading-relaxed">
                Stripe has not told us about this payment yet. If you finished checkout, give it a few seconds and check again.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-button type="button" onclick="window.location.reload()" class="w-full sm:w-auto justify-center">Check again</x-button>
                <a href="{{ route('agent.properties.index') }}" wire:navigate class="w-full sm:w-auto">
                    <x-button variant="secondary" class="w-full justify-center">Back to your listings</x-button>
                </a>
            </div>
        @endif

        <div class="mt-8 pt-6 border-t border-gray-900/10 flex items-center justify-center gap-2 text-xs text-gray-400">
            <x-icon.banknotes class="w-4 h-4" />
            <span>Amount paid &middot; <x-price :amount="$payment->amount / 100" class="font-medium text-gray-500" /></span>
        </div>
    </div>
</div>
