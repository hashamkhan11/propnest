@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center bg-white rounded-2xl border border-gray-100 py-16 px-6']) }}>
    @isset($icon)
        <div class="w-14 h-14 rounded-full bg-primary-50 flex items-center justify-center text-primary-600 mb-4">
            {{ $icon }}
        </div>
    @endisset

    <h3 class="font-heading font-700 text-lg text-gray-900">{{ $title }}</h3>

    @if ($description)
        <p class="text-gray-500 mt-1 max-w-sm">{{ $description }}</p>
    @endif

    @isset($actions)
        <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
