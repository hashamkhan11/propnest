<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <x-page-header kicker="Compare" title="Side by side" description="Up to three homes, every detail in one table. The best value in each row is easy to spot." />

    @if ($properties->isEmpty())
        <x-empty-state
            title="You haven't selected any properties to compare yet"
            description="Use the compare button on any listing to add it here."
        >
            <x-slot name="icon">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v1.5M3 21v-6M21 21v-1.5M21 3v6M3 4.5h18M3 19.5h18M8 8v8M16 8v8" /></svg>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ route('properties.index') }}" wire:navigate>
                    <x-button type="button">Browse homes</x-button>
                </a>
            </x-slot>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            @foreach ($properties as $property)
                <div class="flex flex-col bg-white rounded-xl border border-gray-900/10 transition-all overflow-hidden">
                    <div class="aspect-[4/3] bg-gray-100">
                        @if ($property->coverImage)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($property->coverImage->thumbnailDisplayPath()) }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" /></svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col flex-1 p-4">
                        <div class="font-semibold text-xl text-primary-800">
                            <x-price whole :amount="$property->price" />
                            @if ($property->purpose === \App\Enums\Property\PropertyPurpose::ForRent)
                                <span class="text-sm font-medium text-gray-500">/mo</span>
                            @endif
                        </div>

                        <a href="{{ route('properties.show', $property) }}" wire:navigate class="mt-1 font-semibold text-gray-900 hover:text-accent-700 truncate">
                            {{ $property->title }}
                        </a>

                        <div class="mt-1 flex items-center gap-1 text-sm text-gray-500 truncate">
                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="truncate">{{ $property->address }}, {{ $property->city }}</span>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-900/10 flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18v2M3 18h18m0 0v2M5 10V7a2 2 0 012-2h2a2 2 0 012 2v3M13 10V8a2 2 0 012-2h2a2 2 0 012 2v2" />
                                </svg>
                                {{ $property->bedrooms }} {{ \Illuminate\Support\Str::plural('Bed', $property->bedrooms) }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 12h16M5 12v6a1 1 0 001 1h12a1 1 0 001-1v-6M7 12V6a2 2 0 012-2h1M9 12V8" />
                                    <circle cx="9.5" cy="4.5" r=".75" fill="currentColor" stroke="none" />
                                </svg>
                                {{ $property->bathrooms }} {{ \Illuminate\Support\Str::plural('Bath', $property->bathrooms) }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                                </svg>
                                {{ number_format($property->area) }} sqft
                            </span>
                        </div>

                        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                {{ $property->views_count }} {{ \Illuminate\Support\Str::plural('view', $property->views_count) }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
                                {{ $property->inquiries_count }} {{ \Illuminate\Support\Str::plural('inquiry', $property->inquiries_count) }}
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-gray-400">Listed {{ $property->created_at->format('M j, Y') }}</p>

                        <div class="mt-4 pt-4 border-t border-gray-900/10 flex flex-col items-stretch gap-2 mt-auto">
                            <a href="{{ route('properties.show', $property) }}" wire:navigate>
                                <x-button type="button" variant="secondary" class="w-full justify-center">View Details</x-button>
                            </a>
                            <button type="button" wire:click="remove({{ $property->id }})" class="text-sm text-red-500 hover:text-red-700 transition-colors">
                                Remove
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div x-data="{ open: false }" class="mt-8">
            <div class="flex justify-center">
                <button
                    type="button"
                    x-on:click="open = !open"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-200 bg-white text-gray-700 font-semibold text-sm hover:bg-gray-50 hover:border-gray-900/25 transition-colors"
                >
                    Compare Features
                    <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="mt-6 overflow-x-auto bg-white rounded-xl border border-gray-900/10"
            >
                <table class="w-full border-collapse">
                    <tbody class="divide-y divide-gray-900/10 [&>tr:hover]:bg-gray-50/60">
                        <tr>
                            <td class="p-4 font-semibold text-gray-500 w-40">Type</td>
                            @foreach ($properties as $property)
                                <td class="p-4 text-gray-700">
                                    {{ $property->property_type->name }} &middot;
                                    {{ $property->purpose === \App\Enums\Property\PropertyPurpose::ForRent ? 'For Rent' : 'For Sale' }}
                                </td>
                            @endforeach
                        </tr>
                        @foreach ($amenityNames as $amenityName)
                            <tr>
                                <td class="p-4 font-semibold text-gray-500">{{ $amenityName }}</td>
                                @foreach ($properties as $property)
                                    <td class="p-4">
                                        @if ($property->amenities->contains('name', $amenityName))
                                            <svg class="w-5 h-5 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
