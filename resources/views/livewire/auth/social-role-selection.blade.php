@php use Illuminate\Support\Str; @endphp

<div>
    <h1 class="font-heading text-2xl md:text-3xl font-semibold text-primary-900 mb-2">
        Welcome, {{ Str::of($pendingName)->before(' ') }}!
    </h1>
    <p class="text-sm text-gray-600 mb-8">How will you use PropNest? We just need to know this once — you can always update it later.</p>

    <form wire:submit="continue">
        <x-role-selector name="role" :value="$role" />
        <x-input-error :messages="$errors->get('role')" class="mt-2" />

        <p class="text-xs text-gray-500 mt-4">
            Signing up as <span class="font-medium text-gray-700">{{ $pendingEmail }}</span>.
            <button type="button" wire:click="cancel" class="text-primary-600 hover:text-primary-800 underline">Not you? Use a different account</button>
        </p>

        <x-button class="w-full justify-center mt-6" wire:loading.attr="disabled" wire:target="continue">
            <span wire:loading.remove wire:target="continue">Continue</span>
            <span wire:loading wire:target="continue" class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                Creating your account…
            </span>
        </x-button>
    </form>
</div>
