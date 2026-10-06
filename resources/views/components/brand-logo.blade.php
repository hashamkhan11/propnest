@props([
    'iconOnly' => false,
    'iconSize' => 'h-9 w-9',
    'textSize' => 'text-xl',
    // "light" for dark backgrounds: the tile turns white and the roof turns ink.
    'tone' => 'dark',
])

@php
    $light = $tone === 'light';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 select-none']) }} aria-label="PropNest">
    {{-- Two nested rooflines: a home inside a home, the "nest". --}}
    <svg class="{{ $iconSize }} shrink-0" viewBox="0 0 32 32" fill="none" aria-hidden="true">
        <rect width="32" height="32" rx="7" class="{{ $light ? 'fill-white' : 'fill-primary-900' }}" />
        <path d="M7 17.5 16 9l9 8.5" class="{{ $light ? 'stroke-primary-900' : 'stroke-white' }}" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M11.5 23 16 18.75 20.5 23" class="stroke-accent-500" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

    @unless ($iconOnly)
        <span class="font-semibold {{ $textSize }} leading-none tracking-tightest whitespace-nowrap" aria-hidden="true">prop<span class="text-accent-500">nest</span></span>
    @endunless
</span>
