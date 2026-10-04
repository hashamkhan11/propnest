<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Property Management"
        icon="building-office-2"
        :subtitle="$properties->total() . ' ' . \Illuminate\Support\Str::plural('listing', $properties->total())"
    />

    <x-card class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <x-input wire:model.live.debounce.400ms="keyword" type="text" placeholder="Search title…" class="sm:max-w-xs" />

            <select wire:model.live="statusFilter" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="categoryFilter" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </x-card>

    @if ($properties->isEmpty())
        <x-empty-state title="No listings found" description="Try adjusting your search or filters.">
            <x-slot name="icon">
                <x-heroicon-o-building-office-2 class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Title', 'Agent', 'Category', 'City', 'Status', 'Actions']">
            @foreach ($properties as $property)
                <tr wire:key="property-row-{{ $property->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium max-w-[220px] truncate">{{ $property->title }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $property->agent->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $property->category->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $property->cityRecord->name }}</td>
                    <td class="py-3 px-4">
                        <x-badge :variant="$property->status->badgeVariant()">{{ $property->status->label() }}</x-badge>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.properties.edit', $property) }}" wire:navigate>
                                <x-button variant="secondary">Edit</x-button>
                            </a>
                            <x-button
                                variant="secondary"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Delete this listing?',
                                    message: 'This permanently deletes {{ addslashes($property->title) }} and its images. This cannot be undone.',
                                    confirmText: 'Delete',
                                    variant: 'danger',
                                    onConfirm: () => $wire.delete({{ $property->id }}),
                                })"
                            >
                                Delete
                            </x-button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($properties as $property)
                <div wire:key="property-card-{{ $property->id }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-heading font-700 text-gray-900 truncate">{{ $property->title }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $property->agent->name }} &middot; {{ $property->cityRecord->name }}</p>
                        </div>
                        <x-badge :variant="$property->status->badgeVariant()" class="shrink-0">{{ $property->status->label() }}</x-badge>
                    </div>

                    <p class="mt-2 text-sm text-gray-500">{{ $property->category->name }}</p>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex gap-2">
                        <a href="{{ route('admin.properties.edit', $property) }}" wire:navigate class="flex-1">
                            <x-button variant="secondary" class="w-full justify-center">Edit</x-button>
                        </a>
                        <x-button
                            variant="secondary"
                            class="flex-1 justify-center"
                            x-on:click="$store.confirmDialog.open({
                                title: 'Delete this listing?',
                                message: 'This permanently deletes {{ addslashes($property->title) }} and its images. This cannot be undone.',
                                confirmText: 'Delete',
                                variant: 'danger',
                                onConfirm: () => $wire.delete({{ $property->id }}),
                            })"
                        >
                            Delete
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $properties->links() }}
        </div>
    @endif
</div>
