<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Moderation Queue"
        icon="shield-check"
        :subtitle="$pendingReview->count() . ' ' . \Illuminate\Support\Str::plural('listing', $pendingReview->count()) . ' pending review'"
    />

    <x-card title="Pending Review" class="mb-6">
        <div class="space-y-3">
            @forelse ($pendingReview as $property)
                <div wire:key="pending-{{ $property->id }}" class="border border-gray-100 rounded-xl p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 sm:gap-4 sm:items-center">
                        <div class="min-w-0">
                            <div class="font-heading font-700 text-gray-900 flex items-center gap-2 flex-wrap">
                                {{ $property->title }}
                                <x-badge :variant="$property->has_been_published ? 'warning' : 'gray'">
                                    {{ $property->has_been_published ? 'Edited — re-approval needed' : 'New listing' }}
                                </x-badge>
                            </div>
                            <div class="text-sm text-gray-500">{{ $property->agent->name }}</div>
                        </div>

                        <div class="flex items-center gap-2 sm:justify-end">
                            <x-button
                                variant="secondary"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Approve this listing?',
                                    message: 'This republishes {{ addslashes($property->title) }} immediately.',
                                    confirmText: 'Approve',
                                    onConfirm: () => $wire.approve({{ $property->id }}),
                                })"
                            >
                                Approve
                            </x-button>

                            @if ($rejectingPropertyId !== $property->id)
                                <x-button variant="danger" wire:click="startReject({{ $property->id }})">
                                    Reject
                                </x-button>
                            @endif
                        </div>
                    </div>

                    @if ($rejectingPropertyId === $property->id)
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <x-input-label for="rejectionReason" value="Reason for rejection" />
                            <textarea wire:model="rejectionReason" id="rejectionReason" rows="3" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full"></textarea>
                            <x-input-error :messages="$errors->get('rejectionReason')" class="mt-2" />

                            <div class="mt-3 flex gap-2">
                                <x-button variant="danger" wire:click="reject({{ $property->id }})">Confirm Reject</x-button>
                                <x-button variant="secondary" wire:click="cancelReject">Cancel</x-button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <x-empty-state title="Queue is clear" description="No listings awaiting moderation.">
                    <x-slot name="icon">
                        <x-heroicon-o-shield-check class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @endforelse
        </div>
    </x-card>

    <x-card title="Published Listings">
        <div class="mb-4">
            <x-input-label for="keyword" value="Search" />
            <x-input wire:model.live.debounce.400ms="keyword" id="keyword" type="text" placeholder="Search title or agent name" />
        </div>

        <div class="space-y-3">
            @forelse ($published as $property)
                <div wire:key="published-{{ $property->id }}" class="border border-gray-100 rounded-xl p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 sm:gap-4 sm:items-center">
                        <div class="min-w-0">
                            <div class="font-heading font-700 text-gray-900">{{ $property->title }}</div>
                            <div class="text-sm text-gray-500">{{ $property->agent->name }} &middot; <x-price :amount="$property->price" /></div>
                        </div>

                        <div class="flex items-center gap-2 sm:justify-end">
                            <x-button
                                variant="secondary"
                                x-on:click="$store.confirmDialog.open({
                                    title: 'Pull for review?',
                                    message: 'Pull {{ addslashes($property->title) }} for review. The agent will be notified.',
                                    confirmText: 'Pull for Review',
                                    onConfirm: () => $wire.pullForReview({{ $property->id }}),
                                })"
                            >
                                Pull for Review
                            </x-button>

                            @if ($rejectingPropertyId !== $property->id)
                                <x-button variant="danger" wire:click="startReject({{ $property->id }})">
                                    Reject
                                </x-button>
                            @endif
                        </div>
                    </div>

                    @if ($rejectingPropertyId === $property->id)
                        <div class="mt-4 border-t border-gray-100 pt-4">
                            <x-input-label for="rejectionReason" value="Reason for rejection" />
                            <textarea wire:model="rejectionReason" id="rejectionReason" rows="3" class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full"></textarea>
                            <x-input-error :messages="$errors->get('rejectionReason')" class="mt-2" />

                            <div class="mt-3 flex gap-2">
                                <x-button variant="danger" wire:click="reject({{ $property->id }})">Confirm Reject</x-button>
                                <x-button variant="secondary" wire:click="cancelReject">Cancel</x-button>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <x-empty-state title="No matches" description="No published listings match this search.">
                    <x-slot name="icon">
                        <x-heroicon-o-building-office-2 class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $published->links() }}
        </div>
    </x-card>
</div>
