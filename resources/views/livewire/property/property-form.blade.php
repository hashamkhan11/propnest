<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="font-heading font-800 text-2xl text-gray-900">{{ $property ? 'Edit Listing' : 'New Listing' }}</h1>
        <p class="text-gray-500 mt-1">{{ $property ? 'Update the details below.' : 'Fill in the details below — it will be saved as a Draft.' }}</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <x-card>
            <div class="flex items-center gap-3 mb-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 font-heading font-700 text-sm">1</span>
                <h2 class="font-heading font-700 text-lg text-gray-900">Basic Info</h2>
            </div>
            <p class="text-sm text-gray-500 mb-5 ml-11">The headline and description buyers will see first.</p>

            <div class="space-y-5">
                <div>
                    <x-input-label for="title" value="Title" />
                    <x-input wire:model="title" id="title" type="text" placeholder="e.g. Sunny 3-bed family home near downtown" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Description" />
                    <textarea wire:model="description" id="description" rows="5" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full" placeholder="Describe the property, neighborhood, and what makes it stand out."></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3 mb-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 font-heading font-700 text-sm">2</span>
                <h2 class="font-heading font-700 text-lg text-gray-900">Details</h2>
            </div>
            <p class="text-sm text-gray-500 mb-5 ml-11">Category, price, and specifications.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5" x-data="{ categoryId: @entangle('categoryId'), categorySlugs: {{ \Illuminate\Support\Js::from($categories->pluck('slug', 'id')) }} }">
                <div>
                    <x-input-label for="categoryId" value="Property Category" />
                    <select wire:model="categoryId" id="categoryId" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full">
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('categoryId')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="purpose" value="Listing Purpose" />
                    <select wire:model="purpose" id="purpose" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full">
                        @foreach ($purposes as $purposeOption)
                            <option value="{{ $purposeOption->value }}">{{ $purposeOption->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="price" value="Price" />
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm whitespace-nowrap">{{ \App\Support\Settings::currency()->prefix() }}</span>
                        <x-input wire:model="price" id="price" type="number" step="0.01" class="pl-12" />
                    </div>
                    <x-input-error :messages="$errors->get('price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="area" value="Area (sq ft)" />
                    <x-input wire:model="area" id="area" type="number" step="0.01" />
                    <x-input-error :messages="$errors->get('area')" class="mt-2" />
                </div>

                <div x-show="!['land', 'commercial'].includes(categorySlugs[categoryId])">
                    <x-input-label for="bedrooms" value="Bedrooms" />
                    <x-input wire:model="bedrooms" id="bedrooms" type="number" />
                    <x-input-error :messages="$errors->get('bedrooms')" class="mt-2" />
                </div>

                <div x-show="!['land', 'commercial'].includes(categorySlugs[categoryId])">
                    <x-input-label for="bathrooms" value="Bathrooms" />
                    <x-input wire:model="bathrooms" id="bathrooms" type="number" />
                    <x-input-error :messages="$errors->get('bathrooms')" class="mt-2" />
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3 mb-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 font-heading font-700 text-sm">3</span>
                <h2 class="font-heading font-700 text-lg text-gray-900">Location</h2>
            </div>
            <p class="text-sm text-gray-500 mb-5 ml-11">Click the map or drag the pin to set the exact location, or type coordinates directly.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <x-input-label for="address" value="Address" />
                    <x-input wire:model="address" id="address" type="text" />
                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="cityId" value="City" />
                    <select wire:model="cityId" id="cityId" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full">
                        <option value="">Select a city</option>
                        @foreach ($citiesByRegion as $regionName => $citiesInRegion)
                            <optgroup label="{{ $regionName }}">
                                @foreach ($citiesInRegion as $cityOption)
                                    <option value="{{ $cityOption->id }}">{{ $cityOption->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cityId')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="latitude" value="Latitude (optional)" />
                    <x-input wire:model="latitude" id="latitude" type="number" step="0.0000001" />
                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="longitude" value="Longitude (optional)" />
                    <x-input wire:model="longitude" id="longitude" type="number" step="0.0000001" />
                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                </div>
            </div>

            <div class="rounded-xl overflow-hidden border border-gray-200">
                <x-map-picker :latitude="$latitude" :longitude="$longitude" />
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3 mb-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 font-heading font-700 text-sm">4</span>
                <h2 class="font-heading font-700 text-lg text-gray-900">Amenities</h2>
            </div>
            <p class="text-sm text-gray-500 mb-5 ml-11">Select everything that applies.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($amenities as $amenity)
                    <label class="flex items-center gap-2.5 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-2.5 cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" wire:model="selectedAmenities" value="{{ $amenity->id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                        {{ $amenity->name }}
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('selectedAmenities')" class="mt-2" />
        </x-card>

        <x-card>
            <div class="flex items-center gap-3 mb-1">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 font-heading font-700 text-sm">5</span>
                <h2 class="font-heading font-700 text-lg text-gray-900">Photos</h2>
            </div>
            <p class="text-sm text-gray-500 mb-5 ml-11">A cover photo plus a gallery of 4-5 more is ideal.</p>

            <div class="space-y-6">
                <div
                    x-data="{
                        previewUrl: {{ $property?->coverImage ? \Illuminate\Support\Js::from(\Illuminate\Support\Facades\Storage::url($property->coverImage->thumbnailDisplayPath())) : 'null' }},
                        onSelect(event) {
                            const file = event.target.files[0];
                            if (file) this.previewUrl = URL.createObjectURL(file);
                        },
                    }"
                >
                    <x-input-label for="coverImage" value="Cover Photo" class="mb-2" />
                    <label for="coverImage" class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 hover:border-primary-400 rounded-xl py-8 px-4 text-center cursor-pointer transition-colors bg-gray-50/50" x-show="!previewUrl">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" /></svg>
                        <span class="text-sm font-medium text-gray-600">Click to choose a cover photo</span>
                        <span class="text-xs text-gray-400">PNG or JPG</span>
                    </label>

                    <div x-show="previewUrl" x-cloak class="relative rounded-xl overflow-hidden border border-gray-200 aspect-video max-w-sm group">
                        <img :src="previewUrl" class="w-full h-full object-cover">
                        <label for="coverImage" class="absolute inset-0 bg-gray-900/0 group-hover:bg-gray-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                            <span class="text-white text-sm font-semibold">Change photo</span>
                        </label>
                    </div>

                    <input type="file" wire:model="coverImage" id="coverImage" accept="image/*" class="hidden" x-on:change="onSelect($event)">
                    <x-input-error :messages="$errors->get('coverImage')" class="mt-2" />
                    @if ($coverImage)
                        <p class="text-sm text-primary-700 mt-2">Selected: {{ $coverImage->getClientOriginalName() }}</p>
                    @elseif ($property?->coverImage)
                        <p class="text-sm text-gray-500 mt-2">Current cover photo will be replaced only if you choose a new file.</p>
                    @endif
                </div>

                <div
                    x-data="{
                        files: [],
                        syncHiddenInput() {
                            const dt = new DataTransfer();
                            this.files.forEach(f => dt.items.add(f));
                            this.$refs.galleryImagesInput.files = dt.files;
                            this.$refs.galleryImagesInput.dispatchEvent(new Event('change', { bubbles: true }));
                        },
                        addFiles(event) {
                            this.files = this.files.concat(Array.from(event.target.files));
                            event.target.value = '';
                            this.syncHiddenInput();
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                            this.syncHiddenInput();
                        }
                    }"
                >
                    <x-input-label for="galleryImagesPicker" value="Gallery Photos" class="mb-2" />

                    <label for="galleryImagesPicker" class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 hover:border-primary-400 rounded-xl py-8 px-4 text-center cursor-pointer transition-colors bg-gray-50/50">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M18 22.5H6a2.25 2.25 0 01-2.25-2.25V3.75A2.25 2.25 0 016 1.5h9.879a1.5 1.5 0 011.06.44l3.622 3.621a1.5 1.5 0 01.439 1.061V20.25A2.25 2.25 0 0118 22.5zM10.5 8.25a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
                        <span class="text-sm font-medium text-gray-600">Click to add gallery photos</span>
                        <span class="text-xs text-gray-400">Can be used multiple times — each selection adds to the list</span>
                    </label>
                    <input type="file" id="galleryImagesPicker" accept="image/*" multiple @change="addFiles($event)" class="hidden">
                    <input type="file" x-ref="galleryImagesInput" wire:model="galleryImages" id="galleryImages" accept="image/*" multiple class="hidden">

                    <ul x-show="files.length > 0" class="mt-3 space-y-1.5">
                        <template x-for="(file, index) in files" :key="file.name + '-' + file.size + '-' + index">
                            <li class="flex items-center gap-3 text-sm text-gray-700 bg-gray-50 rounded-lg px-3 py-1.5">
                                <img :src="URL.createObjectURL(file)" class="w-10 h-8 object-cover rounded shrink-0">
                                <span x-text="file.name" class="truncate flex-1"></span>
                                <button type="button" class="text-red-600 hover:text-red-800 font-medium shrink-0 ml-2" @click="removeFile(index)">Remove</button>
                            </li>
                        </template>
                    </ul>
                    <p class="text-sm text-gray-500 mt-1.5" x-show="files.length > 0" x-text="files.length + ' photo(s) selected'"></p>

                    <x-input-error :messages="$errors->get('galleryImages')" class="mt-2" />
                    <x-input-error :messages="$errors->get('galleryImages.*')" class="mt-2" />
                </div>
            </div>
        </x-card>

        <div class="sticky bottom-4 flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-lg shadow-gray-900/5 px-6 py-4">
            <x-button type="submit" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">{{ $property ? 'Save Changes' : 'Create Draft Listing' }}</span>
                <span wire:loading wire:target="save">Saving&hellip;</span>
            </x-button>
            <a href="{{ route('agent.properties.index') }}" wire:navigate class="text-sm font-semibold text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>
</div>
