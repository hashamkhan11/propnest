@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $heroProperty = $featuredProperties->first(fn ($p) => $p->coverImage) ?? $latestProperties->first(fn ($p) => $p->coverImage);
@endphp

<div>
    {{-- Hero: the headline and search on the left, a real listing on the right --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-14 lg:pt-16 pb-16">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-14 items-end">
            <div class="lg:col-span-7">
                <p class="kicker flex items-center gap-2">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                    {{ number_format($propertyCount) }} {{ Str::plural('home', $propertyCount) }} live in {{ $cityCount }} {{ Str::plural('city', $cityCount) }}
                </p>

                <h1 class="display text-[3.4rem] sm:text-7xl xl:text-[5.6rem] mt-6">
                    Find a place<br>
                    that feels like <em class="text-accent-600">yours.</em>
                </h1>

                <p class="mt-6 text-[17px] text-gray-600 max-w-lg leading-relaxed">
                    Homes for sale and rent from agents we have verified. Real photos, the real price, and a direct line to the person selling.
                </p>

                <form wire:submit="search" class="mt-9 max-w-xl" x-data>
                    <div class="inline-flex p-1 rounded-md bg-gray-900/5 text-sm mb-3" role="radiogroup" aria-label="Buy or rent">
                        @foreach (['' => 'Any', 'for_sale' => 'Buy', 'for_rent' => 'Rent'] as $value => $label)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="purpose" value="{{ $value }}" class="peer sr-only">
                                <span class="block px-4 py-1.5 rounded text-gray-600 peer-checked:bg-white peer-checked:text-primary-900 peer-checked:shadow-sm peer-focus-visible:ring-2 peer-focus-visible:ring-accent-500 transition">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-stretch bg-white rounded-md ring-1 ring-gray-900/10 shadow-lg focus-within:ring-primary-900 transition">
                        <label for="hero-location" class="flex-1 flex items-center gap-3 pl-4">
                            <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span class="sr-only">City, neighborhood or keyword</span>
                            <input id="hero-location" wire:model="location" type="text" placeholder="City, neighborhood or street" class="w-full border-0 focus:ring-0 text-[15px] px-0 py-4 placeholder:text-gray-400 bg-transparent">
                        </label>
                        <button type="submit" class="m-1.5 px-5 sm:px-6 rounded bg-primary-900 hover:bg-primary-800 text-white text-sm font-medium inline-flex items-center gap-2 transition-colors">
                            Search
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                        </button>
                    </div>

                    @error('location')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror

                    @if ($cityCounts->isNotEmpty())
                        <p class="mt-4 text-sm text-gray-500">
                            Popular:
                            @foreach ($cityCounts->keys()->take(4) as $city)
                                <a href="{{ route('properties.index', ['location' => $city]) }}" wire:navigate class="text-primary-900 link-underline">{{ $city }}</a>@if (! $loop->last)<span class="text-gray-300 mx-1.5">/</span>@endif
                            @endforeach
                        </p>
                    @endif
                </form>
            </div>

            <div class="lg:col-span-5">
                @if ($heroProperty)
                    <a href="{{ route('properties.show', $heroProperty) }}" wire:navigate class="group block relative">
                        <div class="aspect-[4/5] rounded-lg overflow-hidden bg-gray-200">
                            <img src="{{ Storage::url($heroProperty->coverImage->displayPath()) }}" alt="{{ $heroProperty->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                        </div>
                        <div class="absolute left-4 right-4 bottom-4 sm:left-auto sm:right-[-1rem] sm:bottom-8 sm:w-72 bg-white rounded-md p-4 shadow-xl">
                            <p class="kicker">{{ $heroProperty->is_featured ? 'Featured this week' : 'Just listed' }}</p>
                            <p class="mt-2 font-medium text-primary-900 truncate">{{ $heroProperty->title }}</p>
                            <div class="mt-1 flex items-baseline justify-between gap-3">
                                <span class="text-sm text-gray-500 truncate">{{ $heroProperty->city }}</span>
                                <span class="figure font-semibold text-primary-900 whitespace-nowrap"><x-price whole :amount="$heroProperty->price" />@if ($heroProperty->purpose === \App\Enums\Property\PropertyPurpose::ForRent)<span class="text-gray-500 font-normal text-sm">/mo</span>@endif</span>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>

        {{-- Facts strip --}}
        <dl class="mt-16 grid grid-cols-2 lg:grid-cols-4 border-y border-gray-900/10">
            @foreach ([
                [number_format($propertyCount), Str::plural('Home', $propertyCount).' on the market'],
                [number_format($agentCount), 'Verified '.Str::plural('agent', $agentCount)],
                [number_format($cityCount), Str::plural('City', $cityCount).' covered'],
                ['$0', 'Cost to search and message agents'],
            ] as [$value, $label])
                <div class="flex flex-col-reverse gap-1 py-6 px-4 sm:px-6 border-gray-900/10 [&:nth-child(even)]:border-l [&:nth-child(n+3)]:border-t lg:[&:nth-child(n+3)]:border-t-0 lg:[&:nth-child(3)]:border-l">
                    <dt class="text-sm text-gray-500">{{ $label }}</dt>
                    <dd class="display text-4xl sm:text-5xl figure">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Featured --}}
        @if ($featuredProperties->isNotEmpty())
            <section class="py-14">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                    <x-section-heading eyebrow="Featured" title="Worth a closer look" subtitle="Homes agents are putting forward this week, picked for light, location and price." align="left" />
                    <a href="{{ route('properties.index', ['featured' => 1]) }}" wire:navigate class="shrink-0 text-sm font-medium text-primary-900 inline-flex items-center gap-1.5 group">
                        <span class="link-underline">See all featured homes</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                    @foreach ($featuredProperties->take(3) as $property)
                        <x-property-card :property="$property" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Cities: a typographic index instead of image tiles --}}
        @if ($cityCounts->isNotEmpty())
            <section class="py-14">
                <div class="grid lg:grid-cols-12 gap-10">
                    <div class="lg:col-span-4">
                        <x-section-heading eyebrow="By city" title="Where people are looking" subtitle="Pick a city to see every home listed there, on a map or as a list." align="left" />
                    </div>
                    <ul class="lg:col-span-8 grid sm:grid-cols-2 gap-x-10 border-t border-gray-900/10">
                        @foreach ($cityCounts as $city => $total)
                            <li class="border-b border-gray-900/10">
                                <a href="{{ route('properties.index', ['location' => $city]) }}" wire:navigate class="group flex items-baseline justify-between gap-4 py-5">
                                    <span class="display text-3xl group-hover:text-accent-600 transition-colors">{{ $city }}</span>
                                    <span class="font-mono text-xs text-gray-500 whitespace-nowrap flex items-center gap-2">
                                        {{ $total }} {{ Str::plural('home', $total) }}
                                        <svg class="w-3.5 h-3.5 -translate-x-1 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        {{-- Property types --}}
        @if (count($propertyTypes) > 0)
            <section class="py-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="kicker mr-2">Browse by type</span>
                    @foreach ($propertyTypes as $type)
                        <a href="{{ route('properties.index', ['propertyType' => $type->value]) }}" wire:navigate class="inline-flex items-center gap-2 rounded-full border border-gray-900/15 bg-white px-4 py-2 text-sm text-primary-900 hover:border-primary-900 transition-colors">
                            {{ Str::headline($type->value) }}
                            <span class="font-mono text-xs text-gray-500">{{ $propertyTypeCounts[$type->value] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest --}}
        <section class="py-14">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                <x-section-heading eyebrow="New on PropNest" title="Fresh on the market" align="left" />
                <a href="{{ route('properties.index') }}" wire:navigate class="shrink-0 text-sm font-medium text-primary-900 inline-flex items-center gap-1.5 group">
                    <span class="link-underline">Browse all {{ number_format($propertyCount) }} homes</span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-10">
                @forelse ($latestProperties as $property)
                    <x-property-card :property="$property" />
                @empty
                    <div class="col-span-full text-center py-16 border border-dashed border-gray-300 rounded-lg">
                        <p class="text-gray-500">Nothing listed yet. New homes appear here the moment an agent publishes them.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- How it works --}}
        <section id="about" class="py-14 scroll-mt-24">
            <div class="grid lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <x-section-heading eyebrow="How it works" title="No middlemen. No guesswork." align="left" />
                    <p class="mt-4 text-[15px] text-gray-600 leading-relaxed max-w-sm">
                        PropNest connects people looking for a home directly with the agent who listed it. We check every agent before their first listing goes live.
                    </p>
                </div>
                <ol class="lg:col-span-8 grid sm:grid-cols-2 gap-px bg-gray-900/10 border border-gray-900/10 rounded-lg overflow-hidden">
                    @foreach ([
                        ['Search the way you think', 'Filter by price, size and type, or draw the exact area you want on the map.'],
                        ['Shortlist and compare', 'Save homes as you go and put up to three side by side, down to the square foot.'],
                        ['Talk to the agent', 'Send a message from the listing. Your email and number stay private until you choose to share them.'],
                        ['Hear about new homes first', 'Save a search and we email you when a new listing matches it.'],
                    ] as $i => [$title, $desc])
                        <li class="bg-cream p-6 sm:p-8">
                            <span class="font-mono text-xs text-accent-600">{{ sprintf('%02d', $i + 1) }}</span>
                            <h3 class="mt-4 text-lg font-semibold text-primary-900">{{ $title }}</h3>
                            <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $desc }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- Agents --}}
        @if ($featuredAgents->isNotEmpty())
            <section class="py-14">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-10">
                    <x-section-heading eyebrow="The people behind the listings" title="Agents you can check" align="left" />
                    <a href="{{ route('agents.index') }}" wire:navigate class="shrink-0 text-sm font-medium text-primary-900 inline-flex items-center gap-1.5 group">
                        <span class="link-underline">Meet every agent</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($featuredAgents as $profile)
                        <a href="{{ route('agents.show', $profile->user) }}" wire:navigate class="group flex items-center gap-4 rounded-lg bg-white ring-1 ring-gray-900/10 hover:ring-primary-900 p-4 transition">
                            <x-user-avatar :user="$profile->user" size="w-12 h-12" textClass="font-semibold text-sm" />
                            <div class="min-w-0">
                                <div class="font-medium text-primary-900 truncate flex items-center gap-1.5">
                                    {{ $profile->user->name }}
                                    <svg class="w-4 h-4 text-accent-600 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-label="Verified"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /></svg>
                                </div>
                                <div class="text-sm text-gray-500 truncate">{{ $profile->agency_name ?: 'Independent agent' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- House rules: what we actually check, instead of invented testimonials --}}
        <section class="py-14">
            <div class="grid lg:grid-cols-12 gap-10 items-start">
                <div class="lg:col-span-5">
                    <p class="kicker">House rules</p>
                    <p class="display text-3xl sm:text-[2.6rem] leading-[1.08] mt-4">
                        A listing site is only as good as what it <em class="text-accent-600">refuses</em> to show.
                    </p>
                </div>
                <ol class="lg:col-span-7 divide-y divide-gray-900/10 border-y border-gray-900/10">
                    @php $rules = [
                        ['title' => 'Every listing is reviewed before it goes live', 'body' => 'A person checks the photos, the price and the details. Listings that do not hold up are sent back to the agent with a reason.'],
                        ['title' => 'Agents are checked, not just signed up', 'body' => 'Agents send their details for review. Only approved agents get the verified mark next to their name.'],
                        ['title' => 'Anyone can flag a listing', 'body' => 'If something looks wrong, report it from the listing page. Reports go straight to our moderators.'],
                    ]; @endphp
                    @foreach ($rules as $rule)
                        <li class="grid grid-cols-[2.5rem_1fr] gap-4 py-6">
                            <span class="font-mono text-xs text-accent-600 pt-1">0{{ $loop->iteration }}</span>
                            <div>
                                <h3 class="font-semibold text-primary-900">{{ $rule['title'] }}</h3>
                                <p class="text-[15px] text-gray-600 mt-1.5 leading-relaxed">{{ $rule['body'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        {{-- For agents --}}
        <section class="py-14">
            <div class="relative overflow-hidden rounded-lg bg-primary-900 text-white">
                <div class="grid lg:grid-cols-12 gap-10 px-6 py-12 sm:px-12 sm:py-16">
                    <div class="lg:col-span-7">
                        <p class="kicker !text-gray-400">For agents</p>
                        <h2 class="display !text-white text-4xl sm:text-6xl mt-4">Selling or letting?<br><em class="text-accent-300">List it here.</em></h2>
                    </div>
                    <div class="lg:col-span-5 flex flex-col justify-end">
                        <p class="text-gray-300 leading-relaxed">Get verified once, then publish as many homes as your plan allows. Buyers reach you directly, and you can feature a listing whenever you need more eyes on it.</p>
                        <div class="mt-7 flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" @click="openAuth('register', $event)">
                                <x-button variant="accent" class="px-5 py-3">Become an agent</x-button>
                            </a>
                            <a href="{{ route('properties.index') }}" wire:navigate>
                                <x-button variant="outline-white" class="px-5 py-3">Browse homes first</x-button>
                            </a>
                        </div>
                    </div>
                </div>
                {{-- Large roofline from the logo, as a quiet graphic --}}
                <svg class="absolute -right-10 -bottom-24 w-[28rem] text-white/[0.04] pointer-events-none hidden lg:block" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <path d="M7 17.5 16 9l9 8.5" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11.5 23 16 18.75 20.5 23" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="py-14" x-data="{ open: 0 }">
            <div class="grid lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <x-section-heading eyebrow="Questions" title="Good to know" align="left" />
                </div>
                <div class="lg:col-span-8 border-t border-gray-900/10">
                    @foreach ([
                        ['q' => 'Is PropNest free for buyers and renters?', 'a' => 'Yes. Searching, saving homes, comparing them and messaging agents costs nothing. Agents pay for their plan and for featured placement.'],
                        ['q' => 'How do you verify agents?', 'a' => 'Agents submit their licence and agency details. Our team checks them before the agent can publish, and reported listings are reviewed by hand.'],
                        ['q' => 'How do I contact an agent?', 'a' => 'Open a listing or an agent profile and send a message. The agent gets it by email and in their inbox on PropNest.'],
                        ['q' => 'Can I get alerts for new homes?', 'a' => 'Sign in, run a search with the filters you want, and choose "Save this search". We email you when a new listing matches.'],
                        ['q' => 'How do I list a property?', 'a' => 'Create an agent account, finish your profile and pick a plan. New listings start as drafts and go live once you publish them.'],
                    ] as $index => $faq)
                        <div class="border-b border-gray-900/10">
                            <button type="button" class="w-full flex items-center justify-between gap-6 text-left py-5 text-[17px] font-medium text-primary-900" @click="open = open === {{ $index }} ? null : {{ $index }}" :aria-expanded="open === {{ $index }}">
                                <span>{{ $faq['q'] }}</span>
                                <span class="relative w-4 h-4 shrink-0 text-gray-500" aria-hidden="true">
                                    <span class="absolute inset-x-0 top-1/2 h-px bg-current"></span>
                                    <span class="absolute inset-y-0 left-1/2 w-px bg-current transition-transform duration-200" :class="{ 'scale-y-0': open === {{ $index }} }"></span>
                                </span>
                            </button>
                            <p x-show="open === {{ $index }}" x-collapse x-cloak class="text-[15px] text-gray-600 leading-relaxed pb-6 pr-10 max-w-2xl">{{ $faq['a'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Contact --}}
        <section id="contact" class="py-14 scroll-mt-24">
            <div class="grid lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <x-section-heading eyebrow="Contact" title="Talk to a person" align="left" />
                    <p class="mt-4 text-[15px] text-gray-600 leading-relaxed max-w-sm">A question about a listing, an agent or your account? Write to us. Someone from the team replies within one working day.</p>
                    <p class="mt-6 text-sm text-gray-500 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        Only our team sees what you send.
                    </p>
                </div>
                <div class="lg:col-span-8 bg-white rounded-lg ring-1 ring-gray-900/10 p-6 sm:p-8">
                    <livewire:public.contact-form />
                </div>
            </div>
        </section>
    </div>
</div>
