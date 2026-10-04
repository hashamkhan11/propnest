@props(['variant' => 'primary'])

@php
$classes = match ($variant) {
    'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
    'danger' => 'bg-red-600 hover:bg-red-700 text-white',
    'accent' => 'bg-accent-500 hover:bg-accent-600 text-white',
    'ghost' => 'bg-transparent hover:bg-primary-50 text-primary-600 border-none',
    'ghost-danger' => 'bg-transparent hover:bg-red-50 text-red-600 border-none',
    'outline-white' => 'bg-transparent hover:bg-white/10 text-white !border-white/30',
    default => 'bg-primary-600 hover:bg-primary-700 text-white',
};
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => "inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100 transition ease-in-out duration-150 $classes"]) }}>
    {{ $slot }}
</button>
