@props(['label', 'value', 'icon' => null, 'color' => 'primary', 'trend' => null])

<div {{ $attributes->class(['bg-white rounded-xl border border-gray-900/10 p-5']) }}>
    <div class="flex items-center justify-between gap-3">
        <p class="kicker">{{ $label }}</p>
        @if ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-4 h-4 text-gray-400 shrink-0" />
        @endif
    </div>
    <p class="figure mt-3 text-2xl sm:text-[28px] font-semibold tracking-tight text-primary-900 break-words">{{ $value }}</p>
    @if ($trend)
        <p class="mt-1 text-xs font-medium {{ str_starts_with($trend, '-') ? 'text-red-700' : 'text-emerald-700' }}">{{ $trend }}</p>
    @endif
</div>
