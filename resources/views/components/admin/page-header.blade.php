@props(['title', 'subtitle' => null, 'icon' => null, 'color' => 'primary'])

<div class="flex flex-wrap items-center gap-3 mb-6">
    @if ($icon)
        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-{{ $color }}-50 text-{{ $color }}-700 flex items-center justify-center shrink-0">
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6" />
        </div>
    @endif
    <div class="min-w-0">
        <h1 class="font-heading font-800 text-2xl sm:text-3xl text-gray-900">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-gray-500 mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="ml-auto shrink-0">{{ $actions }}</div>
    @endisset
</div>
