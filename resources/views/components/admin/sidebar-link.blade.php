@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->class([
        'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
        'bg-primary-50 text-primary-700' => $active,
        'text-gray-600 hover:bg-gray-100 hover:text-gray-900' => ! $active,
    ]) }}
>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-5 w-5 shrink-0 {{ $active ? 'text-primary-600' : 'text-gray-400 group-hover:text-gray-500' }}" />
    @endif
    <span class="truncate" x-show="typeof collapsed === 'undefined' || !collapsed">{{ $slot }}</span>
</a>
