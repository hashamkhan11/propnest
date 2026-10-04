@props([
    'iconOnly' => false,
    'iconSize' => 'h-9 w-9',
    'textSize' => 'text-xl',
])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 select-none']) }}>
    <span class="inline-flex items-center justify-center {{ $iconSize }} rounded-lg bg-gradient-to-br from-primary-600 to-primary-800 text-accent-400 shadow-sm ring-1 ring-black/5 shrink-0">
        <x-icon.house-key class="w-[58%] h-[58%]" stroke-width="2" />
    </span>

    @unless ($iconOnly)
        <span class="font-heading font-800 {{ $textSize }} leading-none whitespace-nowrap">
            <span>Estate</span><span class="text-accent-500">Hub</span>
        </span>
    @endunless
</span>
