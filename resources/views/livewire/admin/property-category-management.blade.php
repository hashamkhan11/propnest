<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Categories"
        icon="tag"
        :subtitle="$categories->count() . ' property ' . \Illuminate\Support\Str::plural('type', $categories->count())"
    />

    <x-card class="mb-6">
        <form wire:submit="save" class="flex flex-col sm:flex-row gap-3 sm:items-start">
            <div class="flex-1">
                <x-input wire:model="name" type="text" placeholder="Category name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div class="flex gap-2">
                <x-button type="submit">{{ $editingId ? 'Update' : 'Add Category' }}</x-button>
                @if ($editingId)
                    <x-button type="button" variant="secondary" wire:click="startCreate">Cancel</x-button>
                @endif
            </div>
        </form>
    </x-card>

    @if ($categories->isEmpty())
        <x-empty-state title="No categories yet" description="Add your first property category above.">
            <x-slot name="icon">
                <x-heroicon-o-tag class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Name', 'Slug', 'Listings', 'Status', 'Actions']">
            @foreach ($categories as $category)
                <tr wire:key="category-row-{{ $category->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $category->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $category->slug }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $category->properties_count }}</td>
                    <td class="py-3 px-4">
                        <x-badge :variant="$category->is_active ? 'success' : 'gray'">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex justify-end gap-2 flex-wrap">
                            <x-button variant="secondary" wire:click="startEdit({{ $category->id }})">Rename</x-button>
                            <x-button variant="secondary" wire:click="toggleActive({{ $category->id }})">
                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                            </x-button>
                            <x-button
                                variant="secondary"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Delete this category?',
                                    message: 'This permanently deletes {{ addslashes($category->name) }}.',
                                    confirmText: 'Delete',
                                    variant: 'danger',
                                    onConfirm: () => $wire.delete({{ $category->id }}),
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
            @foreach ($categories as $category)
                <div wire:key="category-card-{{ $category->id }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-heading font-700 text-gray-900">{{ $category->name }}</p>
                            <p class="text-sm text-gray-500">{{ $category->slug }} &middot; {{ $category->properties_count }} listings</p>
                        </div>
                        <x-badge :variant="$category->is_active ? 'success' : 'gray'" class="shrink-0">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex gap-2 flex-wrap">
                        <x-button variant="secondary" wire:click="startEdit({{ $category->id }})" class="flex-1 justify-center">Rename</x-button>
                        <x-button variant="secondary" wire:click="toggleActive({{ $category->id }})" class="flex-1 justify-center">
                            {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                        </x-button>
                        <x-button
                            variant="secondary"
                            class="w-full justify-center"
                            x-on:click="$store.confirmDialog.open({
                                title: 'Delete this category?',
                                message: 'This permanently deletes {{ addslashes($category->name) }}.',
                                confirmText: 'Delete',
                                variant: 'danger',
                                onConfirm: () => $wire.delete({{ $category->id }}),
                            })"
                        >
                            Delete
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
