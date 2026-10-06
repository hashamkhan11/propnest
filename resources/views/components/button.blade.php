@props(['variant' => 'primary'])

@php
$classes = match ($variant) {
    'secondary' => 'bg-white hover:bg-gray-50 text-primary-900 border !border-gray-300 hover:!border-gray-400',
    'danger' => 'bg-red-700 hover:bg-red-800 text-white',
    'accent' => 'bg-accent-600 hover:bg-accent-700 text-white',
    'ghost' => 'bg-transparent hover:bg-gray-900/5 text-primary-900 border-none',
    'ghost-danger' => 'bg-transparent hover:bg-red-50 text-red-700 border-none',
    'outline-white' => 'bg-transparent hover:bg-white/10 text-white !border-white/40',
    default => 'bg-primary-900 hover:bg-primary-800 text-white',
};
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => "inline-flex items-center justify-center gap-1.5 px-4 py-2.5 border border-transparent rounded-md font-medium text-sm leading-none focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-500 focus-visible:ring-offset-2 active:translate-y-px disabled:opacity-50 disabled:cursor-not-allowed disabled:active:translate-y-0 transition-colors duration-150 $classes"]) }}>
    {{ $slot }}
</button>
