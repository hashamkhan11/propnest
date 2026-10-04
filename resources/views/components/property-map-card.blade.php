@props(['property'])

<div
    data-property-id="{{ $property->id }}"
    x-data
    @mouseenter="$store.mapSync.hoveredId = {{ $property->id }}"
    @mouseleave="if ($store.mapSync.hoveredId === {{ $property->id }}) { $store.mapSync.hoveredId = null }"
    :class="$store.mapSync.hoveredId === {{ $property->id }} ? 'ring-2 ring-primary-500' : ''"
    class="flex gap-3 bg-white rounded-lg border border-gray-100 p-2 transition-shadow hover:shadow-md"
>
    <a href="{{ route('properties.show', $property) }}" wire:navigate class="shrink-0 w-20 h-16 rounded-md overflow-hidden bg-gray-100">
        @if ($property->coverImage)
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($property->coverImage->thumbnailDisplayPath()) }}"
                alt="{{ $property->title }}"
                loading="lazy"
                class="w-full h-full object-cover"
            >
        @endif
    </a>

    <a href="{{ route('properties.show', $property) }}" wire:navigate class="min-w-0 flex-1">
        <div class="font-heading font-700 text-primary-800">
            <x-price :amount="$property->price" />
            @if ($property->purpose === \App\Enums\Property\PropertyPurpose::ForRent)
                <span class="text-xs font-500 text-gray-500">/mo</span>
            @endif
        </div>
        <p class="text-sm text-gray-900 truncate">{{ $property->title }}</p>
        <p class="text-xs text-gray-500 truncate">{{ $property->city }}</p>
        <p
            x-show="$store.mapSync.distanceLabel({{ $property->latitude ?? 'null' }}, {{ $property->longitude ?? 'null' }})"
            x-text="$store.mapSync.distanceLabel({{ $property->latitude ?? 'null' }}, {{ $property->longitude ?? 'null' }})"
            class="text-xs text-gray-500"
        ></p>
    </a>
</div>
