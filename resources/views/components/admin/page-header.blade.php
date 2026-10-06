@props(['title', 'subtitle' => null, 'kicker' => null, 'icon' => null, 'color' => 'primary'])

<header class="flex flex-wrap items-end gap-4 mb-10 pb-8 border-b border-gray-900/10">
    <div class="min-w-0">
        <p class="kicker mb-3">{{ $kicker ?? 'Admin' }}</p>
        <h1 class="display text-4xl sm:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-[15px] text-gray-600 mt-3 max-w-2xl">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="ml-auto shrink-0">{{ $actions }}</div>
    @endisset
</header>
