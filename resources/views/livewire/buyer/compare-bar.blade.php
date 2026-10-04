@php use Illuminate\Support\Str; @endphp

<div>
    @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer && $properties->isNotEmpty() && !$onComparePage)
        <div class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-gray-200 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 overflow-x-auto">
                    <span class="text-sm font-600 text-gray-700 whitespace-nowrap">Compare ({{ $properties->count() }}/3)</span>
                    @foreach ($properties as $property)
                        <span class="flex items-center gap-1 bg-gray-100 rounded-full pl-3 pr-1 py-1 text-sm text-gray-700 whitespace-nowrap">
                            {{ Str::limit($property->title, 20) }}
                            <button type="button" wire:click="remove({{ $property->id }})" class="w-5 h-5 flex items-center justify-center rounded-full hover:bg-gray-200" aria-label="Remove {{ $property->title }} from comparison">
                                &times;
                            </button>
                        </span>
                    @endforeach
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" wire:click="clearAll" class="text-sm text-gray-500 hover:text-gray-700">Clear</button>
                    @if ($properties->count() >= 2)
                        <a href="{{ route('compare.index') }}" wire:navigate>
                            <x-button>Compare Now</x-button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
