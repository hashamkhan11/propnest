@props(['property'])

@php use Illuminate\Support\Str; @endphp

@php
    $isPublished = $property->status === \App\Enums\Property\PropertyStatus::Published;
    $purposeBadge = $property->purpose === \App\Enums\Property\PropertyPurpose::ForRent ? 'For Rent' : 'For Sale';
    $cardImages = $property->images->map(fn ($image) => \Illuminate\Support\Facades\Storage::url($image->thumbnailDisplayPath()));
    $hasMultipleImages = $cardImages->count() > 1;
@endphp

<div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-200 hover:-translate-y-1 overflow-hidden transition-all duration-300">
    <div
        class="relative aspect-[4/3] overflow-hidden bg-gray-100"
        @if ($hasMultipleImages) x-data="{ active: 0, images: {{ \Illuminate\Support\Js::from($cardImages) }} }" @endif
    >
        @if ($property->coverImage)
            @if ($hasMultipleImages)
                <template x-for="(src, index) in images" :key="index">
                    <img
                        :src="src"
                        x-show="active === index"
                        alt="{{ $property->title }}"
                        loading="lazy"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    >
                </template>

                <button
                    type="button"
                    @click.stop.prevent="active = (active - 1 + images.length) % images.length"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-white/80 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    aria-label="Previous photo"
                >
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button
                    type="button"
                    @click.stop.prevent="active = (active + 1) % images.length"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-7 h-7 rounded-full bg-white/80 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    aria-label="Next photo"
                >
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <template x-for="(src, index) in images" :key="index">
                        <span class="w-1.5 h-1.5 rounded-full" :class="active === index ? 'bg-white' : 'bg-white/50'"></span>
                    </template>
                </div>
            @else
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url($property->coverImage->thumbnailDisplayPath()) }}"
                    alt="{{ $property->title }}"
                    loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                >
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" />
                </svg>
            </div>
        @endif

        <div class="absolute top-3 left-3 flex flex-col items-start gap-1.5">
            @if ($property->is_featured)
                <x-badge variant="accent" class="shadow-sm font-semibold inline-flex items-center gap-1">
                    <x-icon.star class="w-3 h-3" />
                    Featured
                </x-badge>
            @endif
            <x-badge variant="primary-outline" class="shadow-sm font-semibold backdrop-blur-sm">{{ $purposeBadge }}</x-badge>
        </div>

        <div class="absolute top-3 right-3 flex flex-col items-end gap-2">
            <livewire:buyer.favorite-button :property="$property" :key="'favorite-'.$property->id" />
            <livewire:buyer.compare-toggle :property="$property" :key="'compare-'.$property->id" />
        </div>

        @unless ($isPublished)
            <div class="absolute inset-0 bg-white/80 flex items-center justify-center">
                <x-badge variant="gray">No longer available</x-badge>
            </div>
        @endunless
    </div>

    @if ($isPublished)
        <a href="{{ route('properties.show', $property) }}" wire:navigate class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-inset rounded-b-2xl">
    @endif

        <div class="p-4">
            <div class="font-heading font-700 text-xl text-primary-800">
                <x-price :amount="$property->price" />
                @if ($property->purpose === \App\Enums\Property\PropertyPurpose::ForRent)
                    <span class="text-sm font-500 text-gray-500">/mo</span>
                @endif
            </div>

            <h3 class="mt-1 font-heading font-600 text-gray-900 truncate">{{ $property->title }}</h3>

            <div class="mt-1 flex items-center gap-1 text-sm text-gray-500 truncate">
                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <span class="truncate">{{ $property->city }}</span>
            </div>

            <div class="mt-3 pt-3 border-t border-gray-100 flex items-center gap-4 text-sm text-gray-500">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18v2M3 18h18m0 0v2M5 10V7a2 2 0 012-2h2a2 2 0 012 2v3M13 10V8a2 2 0 012-2h2a2 2 0 012 2v2" />
                    </svg>
                    {{ $property->bedrooms }} {{ Str::plural('Bed', $property->bedrooms) }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 12h16M5 12v6a1 1 0 001 1h12a1 1 0 001-1v-6M7 12V6a2 2 0 012-2h1M9 12V8" />
                        <circle cx="9.5" cy="4.5" r=".75" fill="currentColor" stroke="none" />
                    </svg>
                    {{ $property->bathrooms }} {{ Str::plural('Bath', $property->bathrooms) }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                    </svg>
                    {{ number_format($property->area) }} sqft
                </span>
            </div>
        </div>

    @if ($isPublished)
        </a>
    @endif
</div>
