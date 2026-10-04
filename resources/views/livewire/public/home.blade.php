@php use Illuminate\Support\Str; @endphp

<div>
    {{-- Hero --}}
    <section class="relative bg-primary-900">
        <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(9,38,39,.75), rgba(9,38,39,.88)), url('https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=1600&h=700&fit=crop') center/cover;"></div>

        <div class="relative max-w-4xl mx-auto px-4 pt-24 sm:pt-28 pb-14 text-center">
            <p class="font-heading font-700 text-accent-400 text-sm uppercase tracking-[0.2em] mb-3">Premium Real Estate Marketplace</p>
            <h1 class="font-heading font-800 text-4xl sm:text-5xl text-white leading-tight">Find your next home,<br class="hidden sm:block"> for sale or for rent</h1>
            <p class="mt-4 text-primary-100 text-base sm:text-lg max-w-xl mx-auto">Search verified listings from trusted agents across every city on PropNest.</p>
        </div>

        <form wire:submit="search" class="relative max-w-3xl mx-auto px-4 z-10">
            <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-black/5 p-2.5 sm:p-3 flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="flex-1 flex items-center gap-2.5 px-3 py-2.5 sm:py-1">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                    </svg>
                    <input wire:model="location" type="text" placeholder="Search by city, neighborhood, or keyword..." class="w-full border-0 focus:ring-0 text-sm px-0 placeholder:text-gray-400">
                </div>

                <div class="hidden sm:block w-px h-8 bg-gray-100"></div>

                <div class="relative flex items-center gap-2 px-3 sm:px-2">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" />
                    </svg>
                    <select wire:model="purpose" class="border-0 focus:ring-0 text-sm pl-0 pr-7 py-2.5 sm:py-1 rounded-none bg-transparent">
                        <option value="">For Sale or Rent</option>
                        @foreach ($purposes as $option)
                            <option value="{{ $option->value }}">{{ $option->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <x-button type="submit" variant="accent" class="justify-center sm:w-auto w-full py-3 sm:py-2.5 shrink-0">
                    <svg class="w-4 h-4 mr-1.5 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                    </svg>
                    Search
                </x-button>
            </div>

            @error('location')
                <p class="mt-2 text-sm text-accent-300 text-center">{{ $message }}</p>
            @enderror
        </form>

        <div class="relative flex flex-wrap justify-center gap-x-10 gap-y-2 pt-6 pb-4 text-white text-sm">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Verified Listings
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2.13a4 4 0 100-8" />
                </svg>
                Trusted Agents
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9.5L12 3l9 6.5V21a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z" />
                </svg>
                Buy &amp; Rent
            </div>
        </div>

        <div class="relative text-center pb-10">
            <p class="inline-flex items-center gap-2 text-primary-200 text-sm">
                <span class="font-heading font-700 text-white text-base">{{ number_format($propertyCount) }}+</span>
                verified {{ Str::plural('property', $propertyCount) }} &middot;
                <span class="font-heading font-700 text-white text-base">{{ number_format($agentCount) }}+</span>
                trusted {{ Str::plural('agent', $agentCount) }}
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Featured Properties --}}
        @if ($featuredProperties->isNotEmpty())
            <section class="py-12 sm:py-14">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-50 via-white to-white border border-primary-100 px-6 py-8 sm:px-10 sm:py-10">
                    <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-accent-400/10"></div>

                    <div class="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                        <x-section-heading eyebrow="Hand-picked" title="Featured Properties" subtitle="Premium listings selected for their quality, location, and value." align="left" />
                        <a href="{{ route('properties.index', ['featured' => 1]) }}" wire:navigate class="shrink-0">
                            <x-button variant="accent" class="w-full sm:w-auto justify-center">
                                View All Featured Properties
                                <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </x-button>
                        </a>
                    </div>

                    <div class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($featuredProperties as $property)
                            <x-property-card :property="$property" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Browse by Category --}}
        @if (count($propertyTypes) > 0)
            <section class="py-12 sm:py-14">
                <x-section-heading eyebrow="Explore" title="Browse by Category" class="mb-8" />
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
                    @foreach ($propertyTypes as $type)
                        <a href="{{ route('properties.index', ['propertyType' => $type->value]) }}" wire:navigate class="group relative flex items-end h-36 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-700 to-primary-900 group-hover:scale-105 transition-transform duration-500"></div>
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="relative p-4 text-white">
                                <div class="font-heading font-700 truncate">{{ Str::headline($type->value) }}</div>
                                <div class="text-sm text-primary-200">{{ $propertyTypeCounts[$type->value] ?? 0 }} {{ Str::plural('listing', $propertyTypeCounts[$type->value] ?? 0) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Browse by City --}}
        @if ($cityCounts->isNotEmpty())
            <section class="py-12 sm:py-14">
                <x-section-heading eyebrow="Nationwide" title="Browse by City" class="mb-8" />
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach ($cityCounts as $city => $total)
                        <a href="{{ route('properties.index', ['location' => $city]) }}" wire:navigate class="group relative flex items-end h-36 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-700 to-primary-900 group-hover:scale-105 transition-transform duration-500"></div>
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="relative p-4 text-white">
                                <div class="font-heading font-700 truncate">{{ $city }}</div>
                                <div class="text-sm text-primary-200">{{ $total }} {{ Str::plural('listing', $total) }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Why Choose PropNest --}}
        <section class="py-12 sm:py-14">
            <x-section-heading eyebrow="Benefits" title="Why Choose PropNest" class="mb-8" />
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ([
                    ['title' => 'Verified Property Listings', 'desc' => 'Every published listing goes through our agent verification process before it goes live.', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Trusted Agents', 'desc' => 'Connect directly with verified real estate professionals across every listing.', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-2.13a4 4 0 100-8 4 4 0 000 8zm6 2.13a4 4 0 100-8'],
                    ['title' => 'Easy Property Search', 'desc' => 'Powerful location, price, and purpose filters help you find exactly what you need.', 'icon' => 'M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z'],
                    ['title' => 'Secure Inquiry Process', 'desc' => 'Message agents directly through PropNest — your contact details stay protected.', 'icon' => 'M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z'],
                ] as $benefit)
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:border-primary-200 hover:-translate-y-1 transition-all duration-300">
                        <div class="w-11 h-11 rounded-xl bg-primary-50 text-primary-700 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $benefit['icon'] }}" />
                            </svg>
                        </div>
                        <div class="font-heading font-700 text-primary-900">{{ $benefit['title'] }}</div>
                        <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $benefit['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Featured Agents --}}
        @if ($featuredAgents->isNotEmpty())
            <section class="py-12 sm:py-14">
                <x-section-heading eyebrow="Meet the network" title="Featured Agents" class="mb-8" />
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($featuredAgents as $profile)
                        <a href="{{ route('agents.show', $profile->user) }}" wire:navigate class="group block bg-white rounded-2xl border border-gray-100 p-6 text-center shadow-sm hover:shadow-lg hover:border-primary-200 hover:-translate-y-1 transition-all duration-300">
                            <x-user-avatar :user="$profile->user" size="w-14 h-14" textClass="font-heading font-700 text-lg" class="mx-auto ring-4 ring-white shadow-sm" />
                            <div class="font-heading font-600 text-primary-900 mt-3 group-hover:text-primary-700 transition-colors">{{ $profile->user->name }}</div>
                            <x-badge variant="primary" class="mt-2">Verified</x-badge>
                            @if ($profile->agency_name)
                                <div class="text-sm text-gray-500 mt-2">{{ $profile->agency_name }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Testimonials --}}
        <section class="py-12 sm:py-14">
            <x-section-heading eyebrow="Client stories" title="What Our Users Say" class="mb-8" />
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ([
                    ['quote' => 'PropNest made finding our first rental so simple — every listing felt trustworthy, and the agent responded within the hour.', 'name' => 'Amara Chen', 'role' => 'Renter'],
                    ['quote' => 'As an agent, the verification process gave my listings instant credibility. Buyer inquiries come in ready to move forward.', 'name' => 'Daniel Osei', 'role' => 'Verified Agent'],
                    ['quote' => 'The comparison and saved-search tools helped us narrow down three cities to the one perfect neighborhood.', 'name' => 'Priya Nair', 'role' => 'Buyer'],
                ] as $testimonial)
                    <div class="h-full bg-white rounded-2xl border border-gray-100 p-6 flex flex-col shadow-sm hover:shadow-lg hover:border-primary-200 hover:-translate-y-1 transition-all duration-300">
                        <div class="flex gap-0.5 text-accent-500 mb-3">
                            @for ($i = 0; $i < 5; $i++)
                                <x-icon.star class="w-4 h-4 fill-current" />
                            @endfor
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed flex-1">&ldquo;{{ $testimonial['quote'] }}&rdquo;</p>
                        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 text-primary-800 font-heading font-700 text-sm flex items-center justify-center shrink-0">
                                {{ Str::of($testimonial['name'])->explode(' ')->map(fn ($n) => $n[0] ?? '')->take(2)->implode('') }}
                            </div>
                            <div>
                                <div class="font-heading font-600 text-primary-900 text-sm">{{ $testimonial['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ $testimonial['role'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Latest Listings --}}
        <section class="py-12 sm:py-14">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-50 via-white to-white border border-primary-100 px-6 py-8 sm:px-10 sm:py-10">
                <div class="absolute -top-10 -right-10 w-56 h-56 rounded-full bg-accent-400/10"></div>

                <div class="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                    <x-section-heading eyebrow="Just Listed" title="Latest Listings" align="left" />
                    <a href="{{ route('properties.index') }}" wire:navigate class="shrink-0">
                        <x-button variant="accent" class="w-full sm:w-auto justify-center">
                            View All Listings
                            <svg class="w-4 h-4 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </x-button>
                    </a>
                </div>

                <div class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($latestProperties as $property)
                        <x-property-card :property="$property" />
                    @empty
                        <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-100">
                            <p class="text-gray-500">No listings yet — check back soon.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Call to Action --}}
        <section class="py-12 sm:py-14">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-800 to-primary-900 px-6 py-14 sm:px-14 sm:py-16 text-center">
                <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-accent-500/10"></div>
                <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-primary-600/20"></div>
                <div class="relative">
                    <h2 class="font-heading font-800 text-2xl sm:text-3xl text-white">Ready to find your next home?</h2>
                    <p class="mt-3 text-primary-200 max-w-xl mx-auto">Create a free account to save favorites, message agents, and get notified the moment a matching listing goes live.</p>
                    <div class="mt-7 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('register') }}" @click="openAuth('register', $event)">
                            <x-button variant="accent" class="px-6 py-3">Get Started Free</x-button>
                        </a>
                        <a href="{{ route('properties.index') }}" wire:navigate>
                            <x-button variant="outline-white" class="px-6 py-3">Browse Properties</x-button>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- About Us --}}
        <section id="about" class="py-12 sm:py-14 scroll-mt-24">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-800 to-primary-900 px-6 py-14 sm:px-14 sm:py-16 text-center">
                <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-accent-500/10"></div>
                <div class="absolute -bottom-20 -left-10 w-72 h-72 rounded-full bg-primary-600/20"></div>
                <div class="relative">
                    <h2 class="font-heading font-700 text-2xl sm:text-3xl text-white mb-4">About PropNest</h2>
                    <p class="text-primary-100 max-w-2xl mx-auto leading-relaxed">
                        PropNest is a real-estate marketplace that connects buyers and renters directly with verified agents.
                        Search published listings for sale or for rent, filter by location, price, and property type, save
                        the properties and searches you care about, and message the listing agent straight from the site —
                        no middlemen, no guesswork. Agents publish and manage their own listings, respond to buyer inquiries,
                        and build a public profile buyers can trust.
                    </p>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="py-12 sm:py-14" x-data="{ open: null }">
            <x-section-heading eyebrow="Support" title="Frequently Asked Questions" class="mb-8" />
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-w-5xl mx-auto">
                @foreach ([
                    ['q' => 'How do I search for a property?', 'a' => 'Use the search bar above to enter a city, neighborhood, or keyword, choose For Sale or For Rent, and browse the matching listings.'],
                    ['q' => 'How do I contact an agent?', 'a' => 'Open any listing or agent profile and use the inquiry form to send a message — the agent is notified directly.'],
                    ['q' => 'How do I list a property?', 'a' => 'Register as an Agent, then create a listing from your dashboard. New listings start as a draft until you publish them.'],
                    ['q' => 'What is the difference between For Sale and For Rent?', 'a' => 'For Sale listings are properties available to purchase; For Rent listings are available to lease. Use the search toggle to filter by either.'],
                    ['q' => 'How do I save a property?', 'a' => 'Create a free buyer account, then use the heart icon on any listing to add it to your favorites.'],
                    ['q' => 'How do I save a search?', 'a' => 'As a signed-in buyer, run a search on the Browse Listings page and click "Save this search" to get notified when new matches are published.'],
                ] as $index => $faq)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-primary-200 hover:shadow-md transition-all duration-300 self-start">
                        <button type="button" class="w-full flex items-center justify-between gap-4 text-left font-heading font-600 text-primary-900 px-5 py-4" @click="open = open === {{ $index }} ? null : {{ $index }}">
                            <span>{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 shrink-0 text-primary-500 transition-transform duration-300" :class="{ 'rotate-180': open === {{ $index }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <p x-show="open === {{ $index }}" x-cloak class="text-sm text-gray-600 px-5 pb-4">{{ $faq['a'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Contact --}}
        <section id="contact" class="py-12 sm:py-14 scroll-mt-24">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-5 gap-10">
                <div class="lg:col-span-2">
                    <h2 class="font-heading font-700 text-2xl sm:text-3xl text-primary-900 mb-2">Contact Us</h2>
                    <p class="text-gray-600">Questions about PropNest? Send us a message using the form and we'll get back to you as soon as we can.</p>

                    <div class="mt-6 space-y-4 text-sm text-gray-600">
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-xl bg-primary-50 text-primary-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </span>
                            Your details stay private — only our team sees your message.
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-xl bg-primary-50 text-primary-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </span>
                            We typically reply within one business day.
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <livewire:public.contact-form />
                </div>
            </div>
        </section>
    </div>
</div>
