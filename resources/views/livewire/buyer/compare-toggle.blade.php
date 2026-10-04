<div>
    @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer)
        <button
            type="button"
            wire:click.stop.prevent="toggle"
            @disabled(!$isSelected && $isFull)
            class="w-8 h-8 flex items-center justify-center rounded-full shadow transition {{ $isSelected ? 'bg-primary-600 text-white' : 'bg-white/90 text-gray-400 hover:bg-white' }} {{ !$isSelected && $isFull ? 'opacity-40 cursor-not-allowed' : '' }}"
            aria-label="{{ $isSelected ? 'Remove from comparison' : 'Add to comparison' }}"
            title="{{ !$isSelected && $isFull ? 'You can compare up to 3 properties' : ($isSelected ? 'Remove from comparison' : 'Add to comparison') }}"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v18M15 3v18M4 8h5M4 16h5M15 8h5M15 16h5" />
            </svg>
        </button>
    @endif
</div>
