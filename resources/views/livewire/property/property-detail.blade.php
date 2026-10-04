@php
    $sortedImages = $property->images->sortBy(fn ($image) => $image->is_cover ? -1 : $image->sort_order)->values();
    $galleryUrls = $sortedImages->map(fn ($image) => \Illuminate\Support\Facades\Storage::url($image->displayPath()));
    $hasMultipleImages = $galleryUrls->count() > 1;
@endphp

<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-4 flex-wrap">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-primary-600">Home</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <a href="{{ route('properties.index') }}" wire:navigate class="hover:text-primary-600">Properties</a>
            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            <span class="text-gray-700 font-medium truncate max-w-xs">{{ $property->title }}</span>
        </nav>

        <div
            x-data="{
                active: 0,
                images: {{ \Illuminate\Support\Js::from($galleryUrls) }},
                lightbox: false,
                prev() { this.active = (this.active - 1 + this.images.length) % this.images.length },
                next() { this.active = (this.active + 1) % this.images.length },
            }"
            @keydown.escape.window="if (lightbox) lightbox = false"
            @keydown.arrow-left.window="if (lightbox) prev()"
            @keydown.arrow-right.window="if (lightbox) next()"
        >
            @if ($galleryUrls->isNotEmpty())
                <div class="relative rounded-2xl overflow-hidden bg-gray-100 aspect-[16/9] sm:aspect-[21/9] group">
                    <template x-for="(src, index) in images" :key="index">
                        <img
                            :src="src"
                            x-show="active === index"
                            @click="lightbox = true"
                            alt="{{ $property->title }}"
                            class="w-full h-full object-cover cursor-zoom-in"
                        >
                    </template>

                    @if ($hasMultipleImages)
                        <button type="button" @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm" aria-label="Previous photo">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button type="button" @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm" aria-label="Next photo">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>

                        <span class="absolute bottom-3 right-3 z-10 bg-black/60 text-white text-xs font-medium px-2 py-1 rounded-full" x-text="(active + 1) + ' / ' + images.length"></span>
                    @endif

                    <button type="button" @click="lightbox = true" class="absolute top-3 right-3 z-10 inline-flex items-center gap-1.5 bg-white/90 hover:bg-white text-xs font-semibold text-gray-700 px-3 py-1.5 rounded-full shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 20.25v-4.5m0 4.5h-4.5m4.5 0L15 15" /></svg>
                        View full size
                    </button>
                </div>

                @if ($hasMultipleImages)
                    <div class="mt-3 grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2">
                        <template x-for="(src, index) in images" :key="'thumb-'+index">
                            <button
                                type="button"
                                @click="active = index"
                                class="aspect-[4/3] rounded-lg overflow-hidden transition"
                                :class="active === index ? 'ring-2 ring-primary-600' : 'opacity-70 hover:opacity-100'"
                            >
                                <img :src="src" alt="{{ $property->title }}" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                @endif
            @else
                <div class="rounded-2xl bg-gray-100 aspect-[16/9] sm:aspect-[21/9] flex items-center justify-center text-gray-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" />
                    </svg>
                </div>
            @endif

            <div
                x-show="lightbox"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[100] bg-gray-950/95 flex items-center justify-center"
            >
                <button type="button" @click="lightbox = false" class="absolute top-4 right-4 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center" aria-label="Close">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                @if ($hasMultipleImages)
                    <button type="button" @click="prev()" class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center" aria-label="Previous photo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    <button type="button" @click="next()" class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center" aria-label="Next photo">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </button>
                @endif

                <template x-for="(src, index) in images" :key="'lightbox-'+index">
                    <img :src="src" x-show="active === index" alt="{{ $property->title }}" class="max-w-[92vw] max-h-[85vh] object-contain" @click.stop>
                </template>

                @if ($hasMultipleImages)
                    <span class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 bg-white/10 text-white text-sm font-medium px-3 py-1 rounded-full" x-text="(active + 1) + ' / ' + images.length"></span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8 animate-fade-in-up">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <x-badge :variant="$property->status->badgeVariant()">
                                    {{ \Illuminate\Support\Str::headline($property->status->value) }}
                                </x-badge>
                                @if ($property->is_featured)
                                    <x-badge variant="accent" class="inline-flex items-center gap-1">
                                        <x-icon.star class="w-3 h-3" />
                                        Featured
                                    </x-badge>
                                @endif
                            </div>
                            <h1 class="font-heading font-800 text-2xl sm:text-3xl text-gray-900">{{ $property->title }}</h1>
                            <p class="text-gray-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                {{ $property->address }}
                            </p>
                        </div>

                        <livewire:buyer.favorite-button :property="$property" />
                    </div>

                    <p class="mt-2 text-sm text-gray-600">
                        Listed by <a href="{{ route('agents.show', $property->agent) }}" wire:navigate class="text-primary-700 font-medium hover:underline">{{ $property->agent->name }}</a>
                    </p>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-baseline gap-1">
                        <span class="font-heading font-800 text-3xl text-primary-800"><x-price :amount="$property->price" /></span>
                        @if ($property->purpose === \App\Enums\Property\PropertyPurpose::ForRent)
                            <span class="text-base font-500 text-gray-500">/mo</span>
                        @endif
                    </div>

                    <div class="mt-5 grid grid-cols-3 divide-x divide-gray-100 border-t border-gray-100 pt-5">
                        <div class="flex flex-col items-center gap-1 text-center px-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18v2M3 18h18m0 0v2M5 10V7a2 2 0 012-2h2a2 2 0 012 2v3M13 10V8a2 2 0 012-2h2a2 2 0 012 2v2" />
                            </svg>
                            <span class="font-heading font-700 text-gray-900">{{ $property->bedrooms }}</span>
                            <span class="text-xs text-gray-500">{{ \Illuminate\Support\Str::plural('Bedroom', $property->bedrooms) }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center px-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 12h16M5 12v6a1 1 0 001 1h12a1 1 0 001-1v-6M7 12V6a2 2 0 012-2h1M9 12V8" />
                                <circle cx="9.5" cy="4.5" r=".75" fill="currentColor" stroke="none" />
                            </svg>
                            <span class="font-heading font-700 text-gray-900">{{ $property->bathrooms }}</span>
                            <span class="text-xs text-gray-500">{{ \Illuminate\Support\Str::plural('Bathroom', $property->bathrooms) }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1 text-center px-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4h4M20 8V4h-4M4 16v4h4M20 16v4h-4" />
                            </svg>
                            <span class="font-heading font-700 text-gray-900">{{ number_format($property->area) }}</span>
                            <span class="text-xs text-gray-500">Sq Ft</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="font-heading font-700 text-lg text-gray-900 mb-2">About this property</h2>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $property->description }}</p>
                </div>

                @if ($property->amenities->isNotEmpty())
                    <div>
                        <h2 class="font-heading font-700 text-lg text-gray-900 mb-3">Amenities</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($property->amenities as $amenity)
                                <div class="flex items-center gap-2 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2">
                                    <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    {{ $amenity->name }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($property->latitude && $property->longitude)
                    <div>
                        <h2 class="font-heading font-700 text-lg text-gray-900 mb-3">Location</h2>
                        <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                            <x-map-picker :latitude="$property->latitude" :longitude="$property->longitude" readonly />
                        </div>
                    </div>
                @endif

                <div class="pt-2 border-t border-gray-100">
                    @auth
                        @if (auth()->user()->role !== \App\Enums\User\UserRole::Agent)
                        @if (! $showReportForm)
                            <button type="button" wire:click="toggleReportForm" class="text-sm text-gray-400 hover:text-red-600 transition-colors">
                                Report this listing
                            </button>
                        @else
                            <div class="border border-gray-200 rounded-xl p-4 mt-2 bg-gray-50">
                                <form wire:submit="submitReport" class="space-y-3">
                                    <div>
                                        <x-input-label for="reportReason" value="Reason" />
                                        <select wire:model="reportReason" id="reportReason" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full">
                                            <option value="">Select a reason</option>
                                            @foreach ($reportReasons as $reason)
                                                <option value="{{ $reason->value }}">{{ \Illuminate\Support\Str::headline($reason->value) }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('reportReason')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="reportDetails" value="Details (optional)" />
                                        <textarea wire:model="reportDetails" id="reportDetails" rows="3" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full"></textarea>
                                        <x-input-error :messages="$errors->get('reportDetails')" class="mt-2" />
                                    </div>
                                    <div class="flex gap-2">
                                        <x-button type="submit">Submit Report</x-button>
                                        <x-button type="button" variant="secondary" wire:click="toggleReportForm">Cancel</x-button>
                                    </div>
                                </form>
                            </div>
                        @endif
                        @endif
                    @endauth
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-6">
                    @if ($property->agent->agentProfile?->phone)
                        @if (auth()->check() && auth()->user()->role === \App\Enums\User\UserRole::Buyer)
                            <x-card>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-heading font-700 text-lg text-gray-900 leading-tight">Contact Agent Directly</h3>
                                        <p class="text-sm text-gray-500">{{ $property->agent->agentProfile->phone }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a
                                        href="tel:{{ $property->agent->agentProfile->phone }}"
                                        class="flex-1 min-w-[8rem] inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-transparent rounded-lg font-semibold text-xs uppercase tracking-widest bg-primary-600 hover:bg-primary-700 text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                        Call Agent
                                    </a>
                                    <a
                                        href="https://wa.me/{{ preg_replace('/\D+/', '', $property->agent->agentProfile->phone) }}?text={{ urlencode("Hi, I'm interested in {$property->title} - ".route('properties.show', $property)) }}"
                                        target="_blank"
                                        rel="noopener"
                                        class="flex-1 min-w-[8rem] inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-transparent rounded-lg font-semibold text-xs uppercase tracking-widest bg-accent-500 hover:bg-accent-600 text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-[0.98] transition ease-in-out duration-150"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.79.47 3.55 1.36 5.09L2.05 22l5.13-1.35a9.9 9.9 0 004.86 1.24h.005c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0012.04 2zm0 1.67c2.2 0 4.26.86 5.82 2.42a8.2 8.2 0 012.42 5.82c0 4.54-3.7 8.24-8.24 8.24a8.24 8.24 0 01-4.2-1.15l-.3-.18-3.04.8.81-2.96-.2-.3a8.2 8.2 0 01-1.26-4.37c0-4.54 3.7-8.24 8.19-8.24zm-4.42 4.7c-.15 0-.4.06-.6.3-.21.24-.8.78-.8 1.9s.82 2.2.94 2.35c.11.15 1.6 2.52 3.97 3.44 1.97.76 2.37.61 2.8.57.43-.04 1.38-.56 1.58-1.11.19-.54.19-1 .13-1.1-.06-.1-.21-.16-.44-.28-.23-.11-1.38-.68-1.6-.76-.21-.08-.37-.11-.53.11-.15.23-.6.76-.74.91-.14.15-.27.17-.5.06-.23-.11-.98-.36-1.87-1.15-.69-.62-1.16-1.38-1.29-1.61-.14-.23-.01-.35.1-.47.11-.11.23-.27.35-.4.11-.13.15-.23.23-.38.08-.15.04-.29-.02-.4-.06-.11-.53-1.31-.74-1.79-.19-.46-.39-.4-.53-.4z"/></svg>
                                        WhatsApp
                                    </a>
                                </div>
                            </x-card>
                        @elseif (! auth()->check())
                            <x-card title="Contact Agent Directly">
                                <p class="text-gray-600 text-sm">
                                    <a href="{{ route('login') }}" wire:navigate class="text-primary-700 font-semibold hover:underline">Log in</a>
                                    as a buyer to call or WhatsApp this agent.
                                </p>
                            </x-card>
                        @endif
                    @endif

                    <livewire:buyer.inquiry-form :property="$property" />
                </div>
            </div>
        </div>
    </div>
</div>
