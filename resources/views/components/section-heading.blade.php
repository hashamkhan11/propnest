@props(['eyebrow' => null, 'title', 'subtitle' => null, 'align' => 'center'])

@php
$wrapClasses = $align === 'center' ? 'text-center' : 'text-left';
$subtitleClasses = $align === 'center' ? 'mx-auto' : '';
@endphp

<div {{ $attributes->class([$wrapClasses]) }}>
    @if ($eyebrow)
        <p class="kicker mb-3">{{ $eyebrow }}</p>
    @endif
    <h2 class="display text-4xl sm:text-5xl">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-3 text-[15px] text-gray-600 max-w-lg leading-relaxed {{ $subtitleClasses }}">{{ $subtitle }}</p>
    @endif
</div>
