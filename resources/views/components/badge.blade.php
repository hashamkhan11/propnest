@props(['variant' => 'gray'])

@php
$classes = match ($variant) {
    'success' => 'bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20',
    'warning' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
    'danger' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
    'accent' => 'bg-accent-100 text-accent-800 ring-1 ring-inset ring-accent-600/20',
    'info' => 'bg-sky-50 text-sky-700 ring-1 ring-inset ring-sky-600/20',
    'primary' => 'bg-primary-700 text-white',
    'primary-outline' => 'bg-white/90 text-primary-800',
    default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-500/10',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $classes"]) }}>
    {{ $slot }}
</span>
