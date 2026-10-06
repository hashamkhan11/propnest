@props(['variant' => 'gray'])

@php
$classes = match ($variant) {
    'success' => 'bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-700/20',
    'warning' => 'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-700/20',
    'danger' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-700/20',
    'accent' => 'bg-accent-600 text-white',
    'info' => 'bg-sky-50 text-sky-800 ring-1 ring-inset ring-sky-700/20',
    'primary' => 'bg-primary-900 text-white',
    'primary-outline' => 'bg-white text-primary-900',
    default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-900/5',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium $classes"]) }}>
    {{ $slot }}
</span>
