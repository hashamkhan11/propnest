@props(['property'])

@php use Illuminate\Support\Str; @endphp

@php
    $isPublished = $property->status === \App\Enums\Property\PropertyStatus::Published;
    $isRent = $property->purpose === \App\Enums\Property\PropertyPurpose::ForRent;
    $cardImages = $property->images->map(fn ($image) => \Illuminate\Support\Facades\Storage::url($image->thumbnailDisplayPath()));
    $hasMultipleImages = $cardImages->count() > 1;
@endphp

<article class="group relative">
    <div
        class="relative aspect-[4/3] overflow-hidden rounded-lg bg-gray-200"
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
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                    >
                </template>

                <button
                    type="button"
                    @click.stop.prevent="active = (active - 1 + images.length) % images.length"
                    class="absolute left-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/90 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 focus-visible:opacity-100 transition-opacity"
                    aria-label="Previous photo"
                >
                    <svg class="w-4 h-4 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <button
                    type="button"
                    @click.stop.prevent="active = (active + 1) % images.length"
                    class="absolute right-2 top-1/2 -translate-y-1/2 z-10 w-8 h-8 rounded-full bg-white/90 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 focus-visible:opacity-100 transition-opacity"
                    aria-label="Next photo"
                >
                    <svg class="w-4 h-4 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>

                <div class="absolute bottom-3 right-3 z-10 font-mono text-[11px] text-white bg-black/50 backdrop-blur-sm rounded px-1.5 py-0.5">
                    <span x-text="active + 1">1</span>/<span x-text="images.length">{{ $cardImages->count() }}</span>
                </div>
            @else
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url($property->coverImage->thumbnailDisplayPath()) }}"
                    alt="{{ $property->title }}"
                    loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                >
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400">
                <svg class="w-10 h-10" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <path d="M7 17.5 16 9l9 8.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11.5 23 16 18.75 20.5 23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        @endif

        <div class="absolute top-3 left-3 flex items-center gap-1.5">
            <x-badge variant="primary-outline" class="shadow-sm">{{ $isRent ? 'For rent' : 'For sale' }}</x-badge>
            @if ($property->is_featured)
                <x-badge variant="accent" class="shadow-sm">Featured</x-badge>
            @endif
        </div>

        <div class="absolute top-3 right-3 z-10 flex flex-col items-end gap-2">
            <livewire:buyer.favorite-button :property="$property" :key="'favorite-'.$property->id" />
            <livewire:buyer.compare-toggle :property="$property" :key="'compare-'.$property->id" />
        </div>

        @unless ($isPublished)
            <div class="absolute inset-0 bg-cream/85 flex items-center justify-center">
                <x-badge variant="gray">No longer available</x-badge>
            </div>
        @endunless
    </div>

    <div class="pt-4">
        <div class="flex items-baseline justify-between gap-3">
            <p class="figure text-xl font-semibold text-primary-900">
                <x-price whole :amount="$property->price" />@if ($isRent)<span class="text-sm font-normal text-gray-500">/mo</span>@endif
            </p>
            <span class="font-mono text-[11px] uppercase tracking-[0.12em] text-gray-500 shrink-0">{{ Str::headline($property->property_type?->value ?? '') }}</span>
        </div>

        <h3 class="mt-1 text-[15px] text-primary-900 truncate">
            @if ($isPublished)
                <a href="{{ route('properties.show', $property) }}" wire:navigate class="after:absolute after:inset-0 focus:outline-none focus-visible:underline">{{ $property->title }}</a>
            @else
                {{ $property->title }}
            @endif
        </h3>
        <p class="text-sm text-gray-500 truncate">{{ $property->city }}</p>

        <p class="mt-3 flex items-center gap-x-3 text-sm text-gray-600">
            <span><span class="figure font-medium text-primary-900">{{ $property->bedrooms }}</span> {{ Str::plural('bed', $property->bedrooms) }}</span>
            <span class="w-px h-3 bg-gray-300"></span>
            <span><span class="figure font-medium text-primary-900">{{ $property->bathrooms }}</span> {{ Str::plural('bath', $property->bathrooms) }}</span>
            <span class="w-px h-3 bg-gray-300"></span>
            <span><span class="figure font-medium text-primary-900">{{ number_format($property->area) }}</span> sqft</span>
        </p>
    </div>
</article>
