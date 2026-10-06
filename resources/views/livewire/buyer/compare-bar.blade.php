@php use Illuminate\Support\Str; @endphp

<div>
    @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer && $properties->isNotEmpty() && !$onComparePage)
        <div class="fixed bottom-4 inset-x-4 z-30 flex justify-center pointer-events-none">
            <div class="pointer-events-auto max-w-4xl w-full bg-primary-900 text-white rounded-lg shadow-2xl px-4 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 overflow-x-auto">
                    <span class="font-mono text-xs uppercase tracking-[0.14em] text-gray-400 whitespace-nowrap">Compare ({{ $properties->count() }}/3)</span>
                    @foreach ($properties as $property)
                        <span class="flex items-center gap-1 bg-white/10 rounded pl-3 pr-1 py-1 text-sm whitespace-nowrap">
                            {{ Str::limit($property->title, 22) }}
                            <button type="button" wire:click="remove({{ $property->id }})" class="w-5 h-5 flex items-center justify-center rounded hover:bg-white/15" aria-label="Remove {{ $property->title }} from comparison">
                                &times;
                            </button>
                        </span>
                    @endforeach
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" wire:click="clearAll" class="text-sm text-gray-400 hover:text-white">Clear</button>
                    @if ($properties->count() >= 2)
                        <a href="{{ route('compare.index') }}" wire:navigate>
                            <x-button variant="accent">Compare side by side</x-button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
