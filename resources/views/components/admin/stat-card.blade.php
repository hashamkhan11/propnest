@props(['label', 'value', 'icon' => null, 'color' => 'primary', 'trend' => null])

<div {{ $attributes->class(['bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 animate-fade-in-up']) }}>
    <div class="flex items-center justify-between">
        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        @if ($icon)
            <div class="w-9 h-9 rounded-lg bg-{{ $color }}-50 text-{{ $color }}-700 flex items-center justify-center shrink-0">
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5" />
            </div>
        @endif
    </div>
    <p class="mt-2 font-heading font-800 text-xl sm:text-2xl text-gray-900 break-words">{{ $value }}</p>
    @if ($trend)
        <p class="mt-1 text-xs font-medium {{ str_starts_with($trend, '-') ? 'text-red-600' : 'text-primary-700' }}">{{ $trend }}</p>
    @endif
</div>
