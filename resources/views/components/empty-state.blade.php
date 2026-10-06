@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center rounded-xl border border-dashed border-gray-300 bg-white/60 py-16 px-6']) }}>
    @isset($icon)
        <div class="w-11 h-11 rounded-lg border border-gray-900/10 bg-white flex items-center justify-center text-gray-500 mb-5">
            {{ $icon }}
        </div>
    @endisset

    <h3 class="font-semibold text-primary-900">{{ $title }}</h3>

    @if ($description)
        <p class="text-sm text-gray-500 mt-1.5 max-w-sm leading-relaxed">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
