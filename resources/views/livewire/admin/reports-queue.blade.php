<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Listing Reports"
        icon="flag"
        :subtitle="$reports->total() . ' pending ' . \Illuminate\Support\Str::plural('report', $reports->total())"
    />

    @if ($reports->isEmpty())
        <x-empty-state title="No pending reports" description="Reported listings will appear here for review.">
            <x-slot name="icon">
                <x-heroicon-o-flag class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Listing', 'Reported By', 'Reason', 'Details', 'Actions']">
            @foreach ($reports as $report)
                <tr wire:key="report-row-{{ $report->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors align-top">
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium max-w-[200px] truncate">{{ $report->property->title }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $report->reportedBy->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ \Illuminate\Support\Str::headline($report->reason->value) }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500 max-w-[220px]">{{ $report->details ?? '—' }}</td>
                    <td class="py-3 px-4">
                        @if ($resolvingReportId === $report->id)
                            <div class="space-y-2 text-left">
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" wire:model.live="alsoReject" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                    Also reject this listing
                                </label>
                                @if ($alsoReject)
                                    <textarea wire:model="rejectionReason" rows="2" placeholder="Rejection reason"
                                              class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full text-sm"></textarea>
                                    <x-input-error :messages="$errors->get('rejectionReason')" />
                                @endif
                                <div class="flex gap-2">
                                    <x-button wire:click="resolve({{ $report->id }})">Confirm Resolve</x-button>
                                    <x-button variant="secondary" wire:click="cancelResolve">Cancel</x-button>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <x-button variant="secondary" wire:click="dismiss({{ $report->id }})">Dismiss</x-button>
                                <x-button wire:click="startResolve({{ $report->id }})">Resolve</x-button>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($reports as $report)
                <div wire:key="report-card-{{ $report->id }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <p class="font-heading font-700 text-gray-900 truncate">{{ $report->property->title }}</p>
                    <p class="text-sm text-gray-500">Reported by {{ $report->reportedBy->name }}</p>

                    <div class="mt-3 text-sm">
                        <p class="text-gray-500">Reason: <span class="text-gray-900">{{ \Illuminate\Support\Str::headline($report->reason->value) }}</span></p>
                        @if ($report->details)
                            <p class="text-gray-500 mt-1">{{ $report->details }}</p>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100">
                        @if ($resolvingReportId === $report->id)
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" wire:model.live="alsoReject" class="rounded border-gray-300 text-primary-600 focus:ring-primary-600">
                                    Also reject this listing
                                </label>
                                @if ($alsoReject)
                                    <textarea wire:model="rejectionReason" rows="2" placeholder="Rejection reason"
                                              class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full text-sm"></textarea>
                                    <x-input-error :messages="$errors->get('rejectionReason')" />
                                @endif
                                <div class="flex gap-2">
                                    <x-button wire:click="resolve({{ $report->id }})" class="flex-1 justify-center">Confirm</x-button>
                                    <x-button variant="secondary" wire:click="cancelResolve" class="flex-1 justify-center">Cancel</x-button>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <x-button variant="secondary" wire:click="dismiss({{ $report->id }})" class="flex-1 justify-center">Dismiss</x-button>
                                <x-button wire:click="startResolve({{ $report->id }})" class="flex-1 justify-center">Resolve</x-button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    @endif
</div>
