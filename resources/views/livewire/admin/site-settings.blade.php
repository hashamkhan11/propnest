<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Site Settings"
        icon="cog-6-tooth"
        subtitle="Global configuration for currency display and featured-listing capacity"
    />

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon.banknotes class="w-5 h-5 text-primary-600" />
                <h2 class="font-heading font-700 text-lg text-gray-900">Site Currency</h2>
            </div>

            <x-input-label for="currency" value="Display Currency" />
            <select wire:model="currency" id="currency" class="mt-1 border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full sm:max-w-xs">
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->value }}">{{ $currency->label() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
            <p class="text-sm text-gray-500 mt-2">Every price on the site is displayed in this currency. Changing it does not convert existing prices — it only changes how they're labeled.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon.star class="w-5 h-5 text-accent-500" />
                <h2 class="font-heading font-700 text-lg text-gray-900">Featured Listing Slots</h2>
            </div>

            <div class="flex items-center gap-4 mb-4 bg-gray-50 rounded-xl px-4 py-3">
                <div class="shrink-0">
                    <p class="font-heading font-800 text-2xl text-gray-900">{{ $activeFeaturedCount }}</p>
                    <p class="text-xs text-gray-500">currently featured</p>
                </div>
                <div class="h-8 w-px bg-gray-200"></div>
                <div class="shrink-0">
                    <p class="font-heading font-800 text-2xl text-gray-900">{{ $maxFeaturedListings !== '' ? $maxFeaturedListings : '∞' }}</p>
                    <p class="text-xs text-gray-500">max allowed</p>
                </div>
            </div>

            <x-input-label for="maxFeaturedListings" value="Max simultaneous featured listings (blank = unlimited)" />
            <x-input wire:model="maxFeaturedListings" id="maxFeaturedListings" type="number" min="1" class="mt-1 sm:max-w-xs" />
            <x-input-error :messages="$errors->get('maxFeaturedListings')" class="mt-2" />
            <p class="text-sm text-gray-500 mt-2">When this cap is reached, agents will be blocked from featuring additional listings until a slot frees up.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon.house-key class="w-5 h-5 text-primary-600" />
                <h2 class="font-heading font-700 text-lg text-gray-900">Free Listing Limit</h2>
            </div>

            <x-input-label for="freeListingLimit" value="Active listings an agent without a subscription may have (blank = unlimited)" />
            <x-input wire:model="freeListingLimit" id="freeListingLimit" type="number" min="0" class="mt-1 sm:max-w-xs" />
            <x-input-error :messages="$errors->get('freeListingLimit')" class="mt-2" />
            <p class="text-sm text-gray-500 mt-2">Agents can raise this cap by subscribing to a plan under Agent Subscriptions.</p>
        </div>

        <div class="flex items-center gap-4">
            <x-button type="submit" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    Saving&hellip;
                </span>
            </x-button>

            @if (session('success'))
                <p class="text-sm text-primary-700 font-medium inline-flex items-center gap-1.5">
                    <x-icon.check-circle class="w-4 h-4" />
                    {{ session('success') }}
                </p>
            @endif
        </div>
    </form>
</div>
