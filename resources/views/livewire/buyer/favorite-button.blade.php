<div>
    @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer)
        <button
            type="button"
            wire:click.stop.prevent="toggle"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-60 cursor-wait scale-95"
            wire:target="toggle"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-white/90 shadow hover:bg-white hover:scale-110 active:scale-95 transition-transform disabled:hover:scale-100"
            aria-label="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}"
            aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
        >
            @if ($isFavorited)
                <svg class="w-5 h-5 text-red-500 animate-heart-pop" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 01-1.162-.682 22.045 22.045 0 01-2.582-1.9C4.045 12.733 2 10.352 2 7.5 2 5.015 3.989 3 6.5 3c1.343 0 2.622.68 3.5 1.72C10.878 3.68 12.157 3 13.5 3 16.011 3 18 5.015 18 7.5c0 2.852-2.045 5.233-3.885 6.82a22.049 22.049 0 01-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 01-.69.001l-.002-.001z" />
                </svg>
            @else
                <svg class="w-5 h-5 text-gray-400 animate-toggle-pop" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            @endif
        </button>
    @endif
</div>
