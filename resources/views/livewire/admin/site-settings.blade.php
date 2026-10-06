<div class="max-w-3xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Audience"
        title="Site settings"
        icon="cog-6-tooth"
        subtitle="The few switches that change how the whole site behaves."
    />

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-900/10 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon.banknotes class="w-5 h-5 text-primary-900" />
                <h2 class="text-[15px] font-semibold text-primary-900">Currency</h2>
            </div>

            <x-input-label for="currency" value="Show prices in" />
            <select wire:model="currency" id="currency" class="mt-1 border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md w-full sm:max-w-xs">
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->value }}">{{ $currency->label() }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
            <p class="text-sm text-gray-500 mt-2">Every price on the site is displayed in this currency. Changing it does not convert existing prices — it only changes how they're labeled.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-900/10 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon.star class="w-5 h-5 text-accent-500" />
                <h2 class="text-[15px] font-semibold text-primary-900">Featured slots</h2>
            </div>

            <div class="flex items-center gap-4 mb-4 bg-gray-50 rounded-xl px-4 py-3">
                <div class="shrink-0">
                    <p class="font-semibold tracking-tight text-2xl text-gray-900">{{ $activeFeaturedCount }}</p>
                    <p class="text-xs text-gray-500">currently featured</p>
                </div>
                <div class="h-8 w-px bg-gray-200"></div>
                <div class="shrink-0">
                    <p class="font-semibold tracking-tight text-2xl text-gray-900">{{ $maxFeaturedListings !== '' ? $maxFeaturedListings : '∞' }}</p>
                    <p class="text-xs text-gray-500">max allowed</p>
                </div>
            </div>

            <x-input-label for="maxFeaturedListings" value="Most listings featured at once (leave blank for no limit)" />
            <x-input wire:model="maxFeaturedListings" id="maxFeaturedListings" type="number" min="1" class="mt-1 sm:max-w-xs" />
            <x-input-error :messages="$errors->get('maxFeaturedListings')" class="mt-2" />
            <p class="text-sm text-gray-500 mt-2">When this cap is reached, agents will be blocked from featuring additional listings until a slot frees up.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-900/10 p-5 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-icon.house-key class="w-5 h-5 text-primary-900" />
                <h2 class="text-[15px] font-semibold text-primary-900">Free listing limit</h2>
            </div>

            <x-input-label for="freeListingLimit" value="Live listings an agent can have without a plan (leave blank for no limit)" />
            <x-input wire:model="freeListingLimit" id="freeListingLimit" type="number" min="0" class="mt-1 sm:max-w-xs" />
            <x-input-error :messages="$errors->get('freeListingLimit')" class="mt-2" />
            <p class="text-sm text-gray-500 mt-2">Agents can raise this cap by subscribing to a plan under Agent plans.</p>
        </div>

        <div class="flex items-center gap-4">
            <x-button type="submit" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Save changes</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                    <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                    Saving&hellip;
                </span>
            </x-button>

            @if (session('success'))
                <p class="text-sm text-primary-900 font-medium inline-flex items-center gap-1.5">
                    <x-icon.check-circle class="w-4 h-4" />
                    {{ session('success') }}
                </p>
            @endif
        </div>
    </form>
</div>
