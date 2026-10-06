<div x-data="{ filtersOpen: false }" @keydown.escape.window="filtersOpen = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @php
            $pageTitle = match ($purpose) {
                'for_sale' => 'Homes for sale',
                'for_rent' => 'Homes for rent',
                default => 'Every home, one search',
            };
        @endphp

        <div class="flex items-end justify-between gap-6 flex-wrap pb-6 mb-6 border-b border-gray-900/10">
            <div>
                <nav class="kicker flex items-center gap-2 mb-4" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-primary-900">Home</a>
                    <span class="text-gray-300">/</span>
                    <span class="text-primary-900">Search</span>
                </nav>
                <h1 class="display text-5xl sm:text-6xl">{{ $pageTitle }}</h1>
                <p class="text-[15px] text-gray-600 mt-3"><span class="figure font-medium text-primary-900">{{ number_format($properties->total()) }}</span> {{ \Illuminate\Support\Str::plural('home', $properties->total()) }} match. Narrow it down with the filters, or draw an area on the map.</p>
            </div>

            <button
                type="button"
                @click="filtersOpen = true"
                class="lg:hidden inline-flex items-center gap-2 bg-white border border-gray-300 rounded-md px-4 py-2.5 text-sm font-medium leading-none text-primary-900 hover:bg-gray-50"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4.5h18M6 9.75h12M9.75 15h4.5" /></svg>
                Filters
            </button>
        </div>

        @php
            $activeFilters = collect([
                ['key' => 'keyword', 'label' => 'Keyword: "'.$keyword.'"', 'active' => $keyword !== ''],
                ['key' => 'location', 'label' => 'Location: '.$location, 'active' => $location !== ''],
                ['key' => 'minPrice', 'label' => 'Min '.$minPrice, 'active' => $minPrice !== ''],
                ['key' => 'maxPrice', 'label' => 'Max '.$maxPrice, 'active' => $maxPrice !== ''],
                ['key' => 'purpose', 'label' => collect($purposes)->first(fn ($p) => $p->value === $purpose)?->label() ?? '', 'active' => $purpose !== ''],
                ['key' => 'propertyType', 'label' => ucfirst($propertyType), 'active' => $propertyType !== ''],
                ['key' => 'bedrooms', 'label' => $bedrooms.'+ Beds', 'active' => $bedrooms !== ''],
                ['key' => 'bathrooms', 'label' => $bathrooms.'+ Baths', 'active' => $bathrooms !== ''],
                ['key' => 'minArea', 'label' => $minArea.'+ sqft', 'active' => $minArea !== ''],
            ])->filter(fn ($f) => $f['active'])->values();

            $hasGeoFilter = $mapBounds || $mapRadius || $mapPolygon;
            $hasAnyActiveFilter = $activeFilters->isNotEmpty() || count($amenityIds) > 0 || $featured || $hasGeoFilter;
        @endphp

        @if ($hasAnyActiveFilter)
            <div class="flex flex-wrap items-center gap-2 mb-6">
                @if ($featured)
                    <span class="inline-flex items-center gap-1.5 bg-accent-50 text-accent-800 border border-accent-500/30 text-[13px] pl-2.5 pr-1 py-1 rounded-md">
                        Featured only
                        <button type="button" wire:click="$set('featured', false)" class="w-5 h-5 rounded flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-primary-900" aria-label="Clear featured filter">&times;</button>
                    </span>
                @endif

                @if ($hasGeoFilter)
                    <span class="inline-flex items-center gap-1.5 bg-accent-50 text-accent-800 border border-accent-500/30 text-[13px] pl-2.5 pr-1 py-1 rounded-md">
                        Map area search
                        <button type="button" wire:click="clearGeoSearch" class="w-5 h-5 rounded flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-primary-900" aria-label="Clear map filter">&times;</button>
                    </span>
                @endif

                @foreach ($activeFilters as $filter)
                    <span class="inline-flex items-center gap-1.5 bg-white text-primary-900 border border-gray-300 text-[13px] pl-2.5 pr-1 py-1 rounded-md">
                        {{ $filter['label'] }}
                        <button type="button" wire:click="$set('{{ $filter['key'] }}', '')" class="w-5 h-5 rounded flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-primary-900" aria-label="Clear {{ $filter['key'] }} filter">&times;</button>
                    </span>
                @endforeach

                @if (count($amenityIds) > 0)
                    <span class="inline-flex items-center gap-1.5 bg-white text-primary-900 border border-gray-300 text-[13px] pl-2.5 pr-1 py-1 rounded-md">
                        {{ count($amenityIds) }} {{ \Illuminate\Support\Str::plural('amenity', count($amenityIds)) }}
                        <button type="button" wire:click="$set('amenityIds', [])" class="w-5 h-5 rounded flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-primary-900" aria-label="Clear amenity filters">&times;</button>
                    </span>
                @endif

                <a href="{{ route('properties.index') }}" wire:navigate class="text-[13px] text-gray-600 hover:text-primary-900 link-underline ml-2">
                    Clear all
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div
                x-show="filtersOpen"
                x-cloak
                @click="filtersOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-primary-900/40 z-40 lg:hidden"
            ></div>

            <aside
                :class="filtersOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white overflow-y-auto lg:overflow-visible transition-transform duration-300 ease-in-out lg:static lg:z-auto lg:w-auto lg:max-w-none lg:translate-x-0 lg:transition-none lg:col-span-1 lg:bg-transparent"
            >
                <div class="p-5 lg:p-0 lg:sticky lg:top-24">
                    <div class="flex items-center justify-between mb-4 lg:hidden">
                        <h2 class="font-semibold text-lg text-primary-900">Filters</h2>
                        <button type="button" @click="filtersOpen = false" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-100" aria-label="Close filters">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <x-card>
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="kicker !text-primary-900">Filters</h3>
                            @if ($hasAnyActiveFilter)
                                <a href="{{ route('properties.index') }}" wire:navigate class="text-xs text-gray-600 hover:text-primary-900 link-underline">Reset</a>
                            @endif
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-3">
                                <div>
                                    <x-input-label for="keyword" value="Keyword" class="mb-1.5" />
                                    <div class="relative">
                                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M18 10.5a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z" /></svg>
                                        <x-input wire:model.live.debounce.400ms="keyword" id="keyword" type="text" class="pl-9" placeholder="Pool, garden, loft…" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="location" value="Location" class="mb-1.5" />
                                    <div class="relative">
                                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        <x-input wire:model.live.debounce.400ms="location" id="location" type="text" class="pl-9" placeholder="City or street" />
                                    </div>
                                </div>
                            </div>

                            <div class="pt-5 border-t border-gray-900/10">
                                <x-input-label value="Listing type" class="mb-2" />
                                <div class="inline-flex rounded-md border border-gray-300 overflow-hidden w-full">
                                    <button type="button" wire:click="$set('purpose', '')" class="flex-1 px-2 py-2 text-xs font-medium {{ $purpose === '' ? 'bg-primary-900 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">Any</button>
                                    @foreach ($purposes as $purposeOption)
                                        <button type="button" wire:click="$set('purpose', '{{ $purposeOption->value }}')" class="flex-1 px-2 py-2 text-xs font-medium border-l border-gray-300 {{ $purpose === $purposeOption->value ? 'bg-primary-900 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                                            {{ $purposeOption->label() }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="pt-5 border-t border-gray-900/10">
                                <x-input-label value="Price range" class="mb-1.5" />
                                <div class="grid grid-cols-2 gap-2">
                                    <x-input wire:model.live.debounce.400ms="minPrice" id="minPrice" type="number" placeholder="Min" />
                                    <x-input wire:model.live.debounce.400ms="maxPrice" id="maxPrice" type="number" placeholder="Max" />
                                </div>
                            </div>

                            <div class="pt-5 border-t border-gray-900/10">
                                <x-input-label for="propertyType" value="Property type" class="mb-1.5" />
                                <select wire:model.live="propertyType" id="propertyType" class="border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900 rounded-md w-full">
                                    <option value="">Any type</option>
                                    @foreach ($propertyTypes as $type)
                                        <option value="{{ $type->value }}">{{ ucfirst($type->value) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-5 border-t border-gray-900/10">
                                <x-input-label value="Rooms & area" class="mb-1.5" />
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs text-gray-500" for="bedrooms">Beds, at least</label>
                                        <x-input wire:model.live="bedrooms" id="bedrooms" type="number" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500" for="bathrooms">Baths, at least</label>
                                        <x-input wire:model.live="bathrooms" id="bathrooms" type="number" />
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="text-xs text-gray-500" for="minArea">Area, at least (sq ft)</label>
                                    <x-input wire:model.live.debounce.400ms="minArea" id="minArea" type="number" />
                                </div>
                            </div>

                            <div class="pt-5 border-t border-gray-900/10">
                                <x-input-label value="Amenities" class="mb-2" />
                                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                    @foreach ($amenities as $amenity)
                                        <label class="flex items-center gap-2.5 text-sm text-gray-700 cursor-pointer">
                                            <input type="checkbox" wire:model.live="amenityIds" value="{{ $amenity->id }}" class="rounded-sm border-gray-300 text-primary-900 focus:ring-accent-500">
                                            {{ $amenity->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
            </aside>

            <div class="lg:col-span-3">
                <div class="flex justify-between items-center gap-3 flex-wrap mb-4">
                    <p class="kicker">Showing {{ $properties->firstItem() ?? 0 }}–{{ $properties->lastItem() ?? 0 }} of {{ $properties->total() }}</p>

                    <div class="flex items-center gap-3 flex-wrap">
                        @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer)
                            <x-button
                                type="button"
                                variant="secondary"
                                wire:click="saveSearch"
                                wire:loading.attr="disabled"
                                wire:target="saveSearch"
                                class="inline-flex items-center gap-1.5"
                            >
                                <svg wire:loading wire:target="saveSearch" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <svg wire:loading.remove wire:target="saveSearch" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                </svg>
                                Save this search
                            </x-button>
                        @endif

                        <div class="inline-flex rounded-md border border-gray-300 overflow-hidden bg-white">
                            <button type="button" wire:click="setViewMode('grid')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium transition-colors {{ $viewMode === 'grid' ? 'bg-primary-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                                List
                            </button>
                            <button type="button" wire:click="setViewMode('map')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium border-l border-gray-300 transition-colors {{ $viewMode === 'map' ? 'bg-primary-900 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 6.75L14.25 4.5l6 3v9.75l-6-3-5.25 2.25L3 13.5V3.75l6 3z" /></svg>
                                Map
                            </button>
                        </div>

                        <select wire:model.live="sort" class="border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900 rounded-md text-sm">
                            <option value="featured">Featured first</option>
                            <option value="newest">Newest first</option>
                            <option value="oldest">Oldest first</option>
                            <option value="price_asc">Lowest price</option>
                            <option value="price_desc">Highest price</option>
                        </select>
                    </div>
                </div>

                <div
                    wire:loading.class="opacity-40 pointer-events-none"
                    wire:target="keyword,location,minPrice,maxPrice,propertyType,purpose,bedrooms,bathrooms,minArea,amenityIds,sort,setViewMode,searchThisArea,applyRadiusSearch,applyPolygonSearch,clearGeoSearch,previousPage,nextPage,gotoPage"
                    class="transition-opacity duration-200"
                >
                    @if ($viewMode === 'map')
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div
                                class="space-y-3 max-h-[600px] overflow-y-auto pr-1"
                                x-data="{
                                    init() {
                                        this.$watch('$store.mapSync.selectedId', (id) => {
                                            if (id === null) return;
                                            const el = this.$root.querySelector('[data-property-id=\'' + id + '\']');
                                            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                        });
                                    },
                                }"
                            >
                                @forelse ($properties as $property)
                                    <x-property-map-card :property="$property" />
                                @empty
                                    <p class="text-sm text-gray-500 py-8 text-center">Nothing in this area yet. Try zooming out or clearing a filter.</p>
                                @endforelse

                                <div class="mt-2">
                                    {{ $properties->links() }}
                                </div>
                            </div>

                            <div class="rounded-xl overflow-hidden border border-gray-900/10">
                                <x-property-map-search :pins="$pins" />
                            </div>
                        </div>
                    @else
                        @if ($properties->isEmpty())
                            <x-empty-state
                                title="Nothing matches, yet"
                                description="Widen the price range, drop a filter or two, or try a nearby city. New homes go live every week."
                            >
                                <x-slot name="icon">
                                    <x-icon.house-search class="w-7 h-7" />
                                </x-slot>
                                @if ($hasAnyActiveFilter)
                                    <x-slot name="actions">
                                        <a href="{{ route('properties.index') }}" wire:navigate>
                                            <x-button type="button" variant="secondary">Clear every filter</x-button>
                                        </a>
                                    </x-slot>
                                @endif
                            </x-empty-state>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-10">
                                @foreach ($properties as $property)
                                    <x-property-card :property="$property" />
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $properties->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
