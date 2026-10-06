@php
    $sortedImages = $property->images->sortBy(fn ($image) => $image->is_cover ? -1 : $image->sort_order)->values();
    $galleryUrls = $sortedImages->map(fn ($image) => \Illuminate\Support\Facades\Storage::url($image->displayPath()));
    $hasMultipleImages = $galleryUrls->count() > 1;
@endphp

<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php
            $isRent = $property->purpose === \App\Enums\Property\PropertyPurpose::ForRent;
            $agentProfile = $property->agent->agentProfile;
            $agentVerified = $agentProfile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
        @endphp

        <nav class="kicker flex items-center gap-2 mb-6 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-primary-900">Home</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('properties.index', ['purpose' => $property->purpose->value]) }}" wire:navigate class="hover:text-primary-900">{{ $isRent ? 'For rent' : 'For sale' }}</a>
            <span class="text-gray-300">/</span>
            <span class="text-primary-900">{{ $property->city }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if ($property->status !== \App\Enums\Property\PropertyStatus::Published)
                        <x-badge :variant="$property->status->badgeVariant()">
                            {{ \Illuminate\Support\Str::headline($property->status->value) }}
                        </x-badge>
                    @endif
                    @if ($property->is_featured)
                        <x-badge variant="accent">Featured</x-badge>
                    @endif
                </div>
                <h1 class="display text-4xl sm:text-5xl lg:text-6xl max-w-3xl">{{ $property->title }}</h1>
                <p class="text-gray-600 mt-3 flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    {{ $property->address }}
                </p>
            </div>

            <div class="flex items-end gap-4 shrink-0">
                <div class="lg:text-right">
                    <p class="kicker">{{ $isRent ? 'Monthly rent' : 'Asking price' }}</p>
                    <p class="figure text-3xl sm:text-4xl font-semibold tracking-tight text-primary-900 mt-1">
                        <x-price whole :amount="$property->price" />@if ($isRent)<span class="text-lg font-normal text-gray-500">/mo</span>@endif
                    </p>
                </div>
                <livewire:buyer.favorite-button :property="$property" />
            </div>
        </div>

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
                <div class="relative rounded-xl overflow-hidden bg-gray-200 aspect-[16/9] sm:aspect-[21/9] group">
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
                        <button type="button" @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Previous photo">
                            <svg class="w-5 h-5 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button type="button" @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 z-10 w-9 h-9 rounded-full bg-white/90 hover:bg-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" aria-label="Next photo">
                            <svg class="w-5 h-5 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>

                        <span class="absolute bottom-3 right-3 z-10 bg-black/55 backdrop-blur-sm text-white font-mono text-xs px-2 py-1 rounded" x-text="(active + 1) + ' / ' + images.length"></span>
                    @endif

                    <button type="button" @click="lightbox = true" class="absolute bottom-3 left-3 z-10 inline-flex items-center gap-1.5 bg-white/95 hover:bg-white text-xs font-medium text-primary-900 px-3 py-2 rounded-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 20.25v-4.5m0 4.5h-4.5m4.5 0L15 15" /></svg>
                        Show all photos
                    </button>
                </div>

                @if ($hasMultipleImages)
                    <div class="mt-2 grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-2">
                        <template x-for="(src, index) in images" :key="'thumb-'+index">
                            <button
                                type="button"
                                @click="active = index"
                                class="aspect-[4/3] rounded-md overflow-hidden transition"
                                :class="active === index ? 'ring-2 ring-primary-900 ring-offset-2 ring-offset-cream' : 'opacity-60 hover:opacity-100'"
                            >
                                <img :src="src" alt="{{ $property->title }}" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                @endif
            @else
                <div class="rounded-xl bg-gray-200 aspect-[16/9] sm:aspect-[21/9] flex items-center justify-center text-gray-400">
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
                class="fixed inset-0 z-[100] bg-primary-900/95 flex items-center justify-center"
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
                    <span class="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 bg-white/10 text-white font-mono text-sm px-3 py-1 rounded" x-text="(active + 1) + ' / ' + images.length"></span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 mt-10">
            <div class="lg:col-span-8 min-w-0">
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-px bg-gray-900/10 border-y border-gray-900/10">
                    <div class="bg-cream py-5 px-4 sm:first:pl-0">
                        <dt class="kicker">Bedrooms</dt>
                        <dd class="figure text-2xl font-semibold text-primary-900 mt-1">{{ $property->bedrooms }}</dd>
                    </div>
                    <div class="bg-cream py-5 px-4 sm:first:pl-0">
                        <dt class="kicker">Bathrooms</dt>
                        <dd class="figure text-2xl font-semibold text-primary-900 mt-1">{{ $property->bathrooms }}</dd>
                    </div>
                    <div class="bg-cream py-5 px-4 sm:first:pl-0">
                        <dt class="kicker">Area</dt>
                        <dd class="figure text-2xl font-semibold text-primary-900 mt-1">{{ number_format($property->area) }}<span class="text-sm font-normal text-gray-500 ml-1">sq ft</span></dd>
                    </div>
                    <div class="bg-cream py-5 px-4 sm:first:pl-0">
                        <dt class="kicker">Type</dt>
                        <dd class="text-2xl font-semibold text-primary-900 mt-1">{{ \Illuminate\Support\Str::headline($property->property_type?->value ?? '—') }}</dd>
                    </div>
                </dl>

                <section class="mt-10">
                    <h2 class="kicker mb-4">About this home</h2>
                    <p class="text-[17px] text-gray-700 leading-[1.75] whitespace-pre-line max-w-prose">{{ $property->description }}</p>
                </section>

                @if ($property->amenities->isNotEmpty())
                    <section class="mt-12">
                        <h2 class="kicker mb-4">What it has</h2>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 border-t border-gray-900/10">
                            @foreach ($property->amenities as $amenity)
                                <li class="flex items-center gap-3 py-3 border-b border-gray-900/10 text-[15px] text-primary-900">
                                    <svg class="w-4 h-4 text-accent-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    {{ $amenity->name }}
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($property->latitude && $property->longitude)
                    <section class="mt-12">
                        <div class="flex items-baseline justify-between gap-4 mb-4">
                            <h2 class="kicker">Where it is</h2>
                            <span class="text-sm text-gray-500">{{ $property->city }}</span>
                        </div>
                        <div class="rounded-xl overflow-hidden border border-gray-900/10">
                            <x-map-picker :latitude="$property->latitude" :longitude="$property->longitude" readonly />
                        </div>
                    </section>
                @endif

                @auth
                    @if (auth()->user()->role !== \App\Enums\User\UserRole::Agent)
                        <div class="mt-12 pt-6 border-t border-gray-900/10">
                            @if (! $showReportForm)
                                <button type="button" wire:click="toggleReportForm" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-red-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" /></svg>
                                    Something wrong with this listing? Report it
                                </button>
                            @else
                                <div class="rounded-xl border border-gray-900/10 bg-white p-5 max-w-xl">
                                    <h3 class="font-semibold text-primary-900">Report this listing</h3>
                                    <p class="text-sm text-gray-500 mt-1 mb-4">Reports go to our moderators. The agent won't see who sent it.</p>
                                    <form wire:submit="submitReport" class="space-y-4">
                                        <div>
                                            <x-input-label for="reportReason" value="What's the problem?" />
                                            <select wire:model="reportReason" id="reportReason" class="mt-1 border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900 rounded-md w-full text-sm">
                                                <option value="">Choose one</option>
                                                @foreach ($reportReasons as $reason)
                                                    <option value="{{ $reason->value }}">{{ \Illuminate\Support\Str::headline($reason->value) }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('reportReason')" class="mt-2" />
                                        </div>
                                        <div>
                                            <x-input-label for="reportDetails" value="Anything else we should know? (optional)" />
                                            <textarea wire:model="reportDetails" id="reportDetails" rows="3" class="mt-1 border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900 rounded-md w-full text-sm"></textarea>
                                            <x-input-error :messages="$errors->get('reportDetails')" class="mt-2" />
                                        </div>
                                        <div class="flex gap-2">
                                            <x-button type="submit">Send report</x-button>
                                            <x-button type="button" variant="ghost" wire:click="toggleReportForm">Cancel</x-button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>

            <aside class="lg:col-span-4">
                <div class="lg:sticky lg:top-24 space-y-4">
                    <div class="rounded-xl border border-gray-900/10 bg-white p-5">
                        <p class="kicker mb-4">Listed by</p>
                        <a href="{{ route('agents.show', $property->agent) }}" wire:navigate class="group flex items-center gap-3">
                            <x-user-avatar :user="$property->agent" size="w-12 h-12" />
                            <div class="min-w-0">
                                <p class="font-medium text-primary-900 truncate group-hover:underline underline-offset-4">{{ $property->agent->name }}</p>
                                <p class="text-sm text-gray-500 truncate flex items-center gap-1.5">
                                    @if ($agentVerified)
                                        <svg class="w-3.5 h-3.5 text-accent-600 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.751 3 3 0 00-5.305 0 3 3 0 00-3.751 3.75 3 3 0 000 5.305 3 3 0 003.75 3.751 3 3 0 005.305 0 3 3 0 003.751-3.75zm-2.546-4.46a.75.75 0 00-1.214-.883l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                        Verified agent
                                    @else
                                        {{ $agentProfile?->agency_name ?? 'Independent agent' }}
                                    @endif
                                </p>
                            </div>
                        </a>

                        @if ($agentProfile?->phone)
                            @if (auth()->check() && auth()->user()->role === \App\Enums\User\UserRole::Buyer)
                                <div class="grid grid-cols-2 gap-2 mt-5">
                                    <a href="tel:{{ $agentProfile->phone }}">
                                        <x-button class="w-full">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                            Call
                                        </x-button>
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', $agentProfile->phone) }}?text={{ urlencode("Hi, I'm interested in {$property->title} - ".route('properties.show', $property)) }}" target="_blank" rel="noopener">
                                        <x-button variant="secondary" class="w-full">
                                            <svg class="w-4 h-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.79.47 3.55 1.36 5.09L2.05 22l5.13-1.35a9.9 9.9 0 004.86 1.24h.005c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.86 9.86 0 0012.04 2zm0 1.67c2.2 0 4.26.86 5.82 2.42a8.2 8.2 0 012.42 5.82c0 4.54-3.7 8.24-8.24 8.24a8.24 8.24 0 01-4.2-1.15l-.3-.18-3.04.8.81-2.96-.2-.3a8.2 8.2 0 01-1.26-4.37c0-4.54 3.7-8.24 8.19-8.24zm-4.42 4.7c-.15 0-.4.06-.6.3-.21.24-.8.78-.8 1.9s.82 2.2.94 2.35c.11.15 1.6 2.52 3.97 3.44 1.97.76 2.37.61 2.8.57.43-.04 1.38-.56 1.58-1.11.19-.54.19-1 .13-1.1-.06-.1-.21-.16-.44-.28-.23-.11-1.38-.68-1.6-.76-.21-.08-.37-.11-.53.11-.15.23-.6.76-.74.91-.14.15-.27.17-.5.06-.23-.11-.98-.36-1.87-1.15-.69-.62-1.16-1.38-1.29-1.61-.14-.23-.01-.35.1-.47.11-.11.23-.27.35-.4.11-.13.15-.23.23-.38.08-.15.04-.29-.02-.4-.06-.11-.53-1.31-.74-1.79-.19-.46-.39-.4-.53-.4z"/></svg>
                                            WhatsApp
                                        </x-button>
                                    </a>
                                </div>
                                <p class="text-xs text-gray-500 mt-3 figure">{{ $agentProfile->phone }}</p>
                            @elseif (! auth()->check())
                                <p class="text-sm text-gray-600 mt-5 pt-4 border-t border-gray-900/10">
                                    <a href="{{ route('login') }}" @click="openAuth('login', $event)" class="text-primary-900 font-medium link-underline">Sign in</a>
                                    to see the agent's number and message them.
                                </p>
                            @endif
                        @endif
                    </div>

                    <livewire:buyer.inquiry-form :property="$property" />
                </div>
            </aside>
        </div>
    </div>
</div>
