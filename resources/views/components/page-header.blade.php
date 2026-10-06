@props(['title', 'kicker' => null, 'description' => null])

<header {{ $attributes->class(['flex items-end justify-between gap-6 flex-wrap pb-8 mb-10 border-b border-gray-900/10']) }}>
    <div class="min-w-0">
        @if ($kicker)
            <p class="kicker mb-3">{{ $kicker }}</p>
        @endif
        <h1 class="display text-5xl sm:text-6xl">{{ $title }}</h1>
        @if ($description)
            <p class="text-[15px] text-gray-600 mt-3 max-w-2xl">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex items-center gap-3 shrink-0">{{ $actions }}</div>
    @endisset
</header>
