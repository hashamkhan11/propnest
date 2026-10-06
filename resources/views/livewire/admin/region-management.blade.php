<div class="max-w-6xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Listings"
        title="Regions and cities"
        icon="map"
        :subtitle="$regions->count() . ' regions, ' . $cities->count() . ' cities'"
    />

    <x-card title="Regions" class="mb-6">
        <form wire:submit="addRegion" class="flex flex-col sm:flex-row gap-3 mb-2">
            <x-input wire:model="newRegionName" type="text" placeholder="Region name" class="sm:max-w-xs" />
            <x-button type="submit">Add region</x-button>
        </form>
        <x-input-error :messages="$errors->get('newRegionName')" class="mb-4" />

        @if ($regions->isEmpty())
            <x-empty-state title="No regions yet" description="Add your first region above.">
                <x-slot name="icon">
                    <x-heroicon-o-map class="w-7 h-7" />
                </x-slot>
            </x-empty-state>
        @else
            <x-admin.data-table :headers="['Name', 'Cities', 'Actions']">
                @foreach ($regions as $region)
                    <tr wire:key="region-row-{{ $region->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
                        <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $region->name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $region->cities_count }}</td>
                        <td class="py-3 px-4 text-right">
                            <x-button
                                variant="ghost-danger"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Delete this region?',
                                    message: 'This permanently deletes {{ addslashes($region->name) }}.',
                                    confirmText: 'Delete',
                                    variant: 'danger',
                                    onConfirm: () => $wire.deleteRegion({{ $region->id }}),
                                })"
                            >
                                Delete
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-admin.data-table>

            <div class="md:hidden space-y-3">
                @foreach ($regions as $region)
                    <div wire:key="region-card-{{ $region->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $region->name }}</p>
                            <p class="text-sm text-gray-500">{{ $region->cities_count }} cities</p>
                        </div>
                        <x-button
                            variant="ghost-danger"
                            x-on:click="$store.confirmDialog.open({
                                title: 'Delete this region?',
                                message: 'This permanently deletes {{ addslashes($region->name) }}.',
                                confirmText: 'Delete',
                                variant: 'danger',
                                onConfirm: () => $wire.deleteRegion({{ $region->id }}),
                            })"
                        >
                            Delete
                        </x-button>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <x-card title="Cities">
        <form wire:submit="addCity" class="flex flex-col sm:flex-row gap-3 mb-2">
            <select wire:model="newCityRegionId" class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md">
                <option value="">Select region</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->name }}</option>
                @endforeach
            </select>
            <x-input wire:model="newCityName" type="text" placeholder="City name" class="sm:max-w-xs" />
            <x-button type="submit">Add city</x-button>
        </form>
        <x-input-error :messages="$errors->get('newCityRegionId')" class="mb-2" />
        <x-input-error :messages="$errors->get('newCityName')" class="mb-4" />

        @if ($cities->isEmpty())
            <x-empty-state title="No cities yet" description="Add your first city above.">
                <x-slot name="icon">
                    <x-heroicon-o-map class="w-7 h-7" />
                </x-slot>
            </x-empty-state>
        @else
            <x-admin.data-table :headers="['City', 'Region', 'Listings', 'Actions']">
                @foreach ($cities as $city)
                    <tr wire:key="city-row-{{ $city->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
                        <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $city->name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $city->region->name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-500">{{ $city->properties_count }}</td>
                        <td class="py-3 px-4 text-right">
                            <x-button
                                variant="ghost-danger"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Delete this city?',
                                    message: 'This permanently deletes {{ addslashes($city->name) }}.',
                                    confirmText: 'Delete',
                                    variant: 'danger',
                                    onConfirm: () => $wire.deleteCity({{ $city->id }}),
                                })"
                            >
                                Delete
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-admin.data-table>

            <div class="md:hidden space-y-3">
                @foreach ($cities as $city)
                    <div wire:key="city-card-{{ $city->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4 flex items-center justify-between gap-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $city->name }}</p>
                            <p class="text-sm text-gray-500">{{ $city->region->name }} &middot; {{ $city->properties_count }} listings</p>
                        </div>
                        <x-button
                            variant="ghost-danger"
                            x-on:click="$store.confirmDialog.open({
                                title: 'Delete this city?',
                                message: 'This permanently deletes {{ addslashes($city->name) }}.',
                                confirmText: 'Delete',
                                variant: 'danger',
                                onConfirm: () => $wire.deleteCity({{ $city->id }}),
                            })"
                        >
                            Delete
                        </x-button>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>
</div>
