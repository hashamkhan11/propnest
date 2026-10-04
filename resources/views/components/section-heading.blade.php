@props(['eyebrow' => null, 'title', 'subtitle' => null, 'align' => 'center'])

@php
$wrapClasses = $align === 'center' ? 'text-center' : 'text-left';
$subtitleClasses = $align === 'center' ? 'mx-auto' : '';
@endphp

<div {{ $attributes->class([$wrapClasses]) }}>
    @if ($eyebrow)
        <p class="font-heading font-700 text-accent-600 text-xs uppercase tracking-[0.15em] mb-1">{{ $eyebrow }}</p>
    @endif
    <h2 class="font-heading font-700 text-2xl sm:text-3xl text-primary-900">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-2 text-sm text-gray-600 max-w-md {{ $subtitleClasses }}">{{ $subtitle }}</p>
    @endif
</div>
