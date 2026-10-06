@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-3 border-l-2 border-accent-500 text-start text-base font-medium text-primary-900 bg-white/60 focus:outline-none'
            : 'block w-full px-4 py-3 border-l-2 border-transparent text-start text-base text-gray-600 hover:text-primary-900 hover:bg-white/60 focus:outline-none transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
