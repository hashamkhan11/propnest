@props(['active'])

@php
$classes = ($active ?? false)
            ? 'relative inline-flex items-center text-[14px] font-medium text-primary-900 after:absolute after:left-0 after:right-0 after:-bottom-[21px] after:h-[2px] after:bg-accent-500 focus:outline-none'
            : 'relative inline-flex items-center text-[14px] text-gray-600 hover:text-primary-900 focus:outline-none focus-visible:text-primary-900 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
