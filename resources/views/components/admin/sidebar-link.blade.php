@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->class([
        'group relative flex items-center gap-3 rounded-md px-3 py-[7px] text-[13.5px] transition-colors',
        'bg-white/[0.08] text-white font-medium before:absolute before:-left-3 before:top-1.5 before:bottom-1.5 before:w-[2px] before:rounded-full before:bg-accent-500' => $active,
        'text-gray-400 hover:bg-white/[0.05] hover:text-white' => ! $active,
    ]) }}
>
    @if ($icon)
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="h-[18px] w-[18px] shrink-0 {{ $active ? 'text-accent-400' : 'text-gray-500 group-hover:text-gray-300' }}" />
    @endif
    <span class="truncate" x-show="typeof collapsed === 'undefined' || !collapsed">{{ $slot }}</span>
</a>
