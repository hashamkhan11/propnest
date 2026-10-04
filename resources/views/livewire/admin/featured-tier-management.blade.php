<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Featured Pricing"
        icon="star"
        color="accent"
        :subtitle="$tiers->count() . ' ' . \Illuminate\Support\Str::plural('tier', $tiers->count()) . ' · shown to agents when they feature a listing'"
    />

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-8">
        <h2 class="font-heading font-700 text-lg text-gray-900 mb-4">{{ $editingId ? 'Edit Tier' : 'Add a New Tier' }}</h2>

        <form wire:submit="save" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-start">
            <div class="flex flex-col">
                <x-input-label for="name" value="Name" class="mb-1 min-h-[2rem] flex items-end text-xs" />
                <x-input wire:model="name" id="name" type="text" placeholder="e.g. Standard" class="w-full" />
            </div>
            <div class="flex flex-col">
                <x-input-label for="durationDays" value="Duration (days)" class="mb-1 min-h-[2rem] flex items-end text-xs" />
                <x-input wire:model="durationDays" id="durationDays" type="number" placeholder="e.g. 14" class="w-full" />
            </div>
            <div class="flex flex-col">
                <x-input-label for="priceDollars" value="Price" class="mb-1 min-h-[2rem] flex items-end text-xs" />
                <x-input wire:model="priceDollars" id="priceDollars" type="number" step="0.01" placeholder="e.g. 49.99" class="w-full" />
            </div>
            <div class="flex flex-col min-w-0">
                <div class="mb-1 min-h-[2rem]" aria-hidden="true"></div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-button type="submit" class="flex-1 justify-center" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Tier' : 'Add Tier' }}</span>
                        <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                            Saving&hellip;
                        </span>
                    </x-button>
                    @if ($editingId)
                        <x-button type="button" variant="secondary" wire:click="startCreate">Cancel</x-button>
                    @endif
                </div>
            </div>
        </form>

        <x-input-error :messages="$errors->get('name')" class="mt-2" />
        <x-input-error :messages="$errors->get('durationDays')" class="mt-2" />
        <x-input-error :messages="$errors->get('priceDollars')" class="mt-2" />
    </div>

    @if ($tiers->isEmpty())
        <x-empty-state
            title="No pricing tiers yet"
            description="Add your first tier above so agents can start featuring their listings."
        >
            <x-slot name="icon">
                <x-icon.star class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($tiers as $index => $tier)
                <div
                    wire:key="tier-{{ $tier->id }}"
                    class="animate-fade-in-up relative bg-white rounded-2xl border shadow-sm hover:shadow-md transition-all p-5 sm:p-6 flex flex-col {{ $tier->is_active ? 'border-primary-100' : 'border-gray-100 opacity-70' }}"
                    style="animation-delay: {{ min($index, 8) * 40 }}ms"
                >
                    @if ($editingId === $tier->id)
                        <span class="absolute -top-2.5 -right-2.5">
                            <x-badge variant="info" class="shadow-sm">Editing</x-badge>
                        </span>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-heading font-700 text-lg text-gray-900">{{ $tier->name }}</h3>
                            <x-badge :variant="$tier->is_active ? 'success' : 'gray'" class="shrink-0">{{ $tier->is_active ? 'Active' : 'Inactive' }}</x-badge>
                        </div>

                        <div class="mt-3 font-heading font-800 text-3xl text-primary-800">
                            <x-price :amount="$tier->price_cents / 100" />
                        </div>

                        <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                            <x-icon.clock class="w-4 h-4 text-gray-400" />
                            Featured for {{ $tier->duration_days }} {{ \Illuminate\Support\Str::plural('day', $tier->duration_days) }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center gap-2">
                        <x-button type="button" variant="secondary" class="flex-1 justify-center" wire:click="startEdit({{ $tier->id }})">
                            Edit
                        </x-button>
                        @if ($tier->is_active)
                            <x-button
                                type="button"
                                variant="ghost-danger"
                                class="flex-1 justify-center"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Deactivate this tier?',
                                    message: 'Agents will no longer be able to select this tier when featuring a listing.',
                                    variant: 'danger',
                                    confirmText: 'Deactivate',
                                    onConfirm: () =&gt; $wire.toggleActive({{ $tier->id }}),
                                })"
                            >
                                Deactivate
                            </x-button>
                        @else
                            <x-button
                                type="button"
                                variant="secondary"
                                class="flex-1 justify-center"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Activate this tier?',
                                    message: 'This tier will become available for agents to select again.',
                                    confirmText: 'Activate',
                                    onConfirm: () =&gt; $wire.toggleActive({{ $tier->id }}),
                                })"
                            >
                                Activate
                            </x-button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
