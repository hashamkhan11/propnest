<div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Money"
        title="Refund requests"
        icon="receipt-refund"
        :subtitle="$refundRequests->total() . ' pending ' . \Illuminate\Support\Str::plural('request', $refundRequests->total())"
    />

    @if ($refundRequests->isEmpty())
        <x-empty-state title="No pending refund requests" description="Refund requests submitted by agents will appear here for review.">
            <x-slot name="icon">
                <x-heroicon-o-receipt-refund class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Agent', 'Property', 'Amount', 'Reason', 'Requested', 'Actions']">
            @foreach ($refundRequests as $refundRequest)
                <tr wire:key="refund-request-row-{{ $refundRequest->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors align-top">
                    <td class="py-3 px-4 text-sm text-gray-700">{{ $refundRequest->agent->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium max-w-[200px] truncate">{{ $refundRequest->property->title }}</td>
                    <td class="py-3 px-4 text-sm text-gray-700">
                        <p class="text-gray-900 font-medium"><x-price :amount="$refundRequest->refund_amount_cents / 100" /></p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            of <x-price :amount="$refundRequest->payment->amount / 100" /> &middot;
                            {{ $refundRequest->payment->featuredDaysUsed($refundRequest->created_at) }}/{{ $refundRequest->payment->featuredTotalDays() }} days used
                        </p>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-500 max-w-[220px]">{{ $refundRequest->reason ?? '—' }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $refundRequest->created_at->format('M j, Y') }}</td>
                    <td class="py-3 px-4 text-right">
                        @if ($rejectingRequestId === $refundRequest->id)
                            <div class="space-y-2 text-left">
                                <textarea wire:model="rejectionNote" rows="2" placeholder="Reason for rejection (optional)"
                                          class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md w-full text-sm"></textarea>
                                <x-input-error :messages="$errors->get('rejectionNote')" />
                                <div class="flex gap-2">
                                    <x-button wire:click="reject({{ $refundRequest->id }})" wire:loading.attr="disabled" wire:target="reject">Confirm Reject</x-button>
                                    <x-button variant="secondary" wire:click="cancelReject">Cancel</x-button>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2 justify-end">
                                <x-button variant="ghost-danger" wire:click="startReject({{ $refundRequest->id }})">Reject</x-button>
                                <x-button
                                    type="button"
                                    wire:loading.attr="disabled"
                                    wire:target="approve"
                                    x-on:click="$store.confirmDialog.open({
                                        title: 'Approve this refund?',
                                        message: 'This refunds {{ \App\Support\Settings::currency()->format($refundRequest->refund_amount_cents / 100) }} (prorated for unused days) via Stripe, un-features the listing immediately, and notifies the agent. This cannot be undone.',
                                        variant: 'danger',
                                        confirmText: 'Approve &amp; Refund',
                                        onConfirm: () =&gt; $wire.approve({{ $refundRequest->id }}),
                                    })"
                                >
                                    <span wire:loading.remove wire:target="approve">Approve</span>
                                    <span wire:loading wire:target="approve" class="inline-flex items-center gap-1.5">
                                        <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                        Processing&hellip;
                                    </span>
                                </x-button>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($refundRequests as $refundRequest)
                <div wire:key="refund-request-card-{{ $refundRequest->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4">
                    <p class="font-semibold text-gray-900 truncate">{{ $refundRequest->property->title }}</p>
                    <p class="text-sm text-gray-500">Requested by {{ $refundRequest->agent->name }}</p>

                    <div class="mt-3 text-sm">
                        <p class="text-gray-500">Refund: <span class="text-gray-900 font-medium"><x-price :amount="$refundRequest->refund_amount_cents / 100" /></span></p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            of <x-price :amount="$refundRequest->payment->amount / 100" /> &middot;
                            {{ $refundRequest->payment->featuredDaysUsed($refundRequest->created_at) }}/{{ $refundRequest->payment->featuredTotalDays() }} days used
                        </p>
                        @if ($refundRequest->reason)
                            <p class="text-gray-500 mt-1">{{ $refundRequest->reason }}</p>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-900/10">
                        @if ($rejectingRequestId === $refundRequest->id)
                            <div class="space-y-2">
                                <textarea wire:model="rejectionNote" rows="2" placeholder="Reason for rejection (optional)"
                                          class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md w-full text-sm"></textarea>
                                <div class="flex gap-2">
                                    <x-button wire:click="reject({{ $refundRequest->id }})" class="flex-1 justify-center">Confirm</x-button>
                                    <x-button variant="secondary" wire:click="cancelReject" class="flex-1 justify-center">Cancel</x-button>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <x-button variant="ghost-danger" wire:click="startReject({{ $refundRequest->id }})" class="flex-1 justify-center">Reject</x-button>
                                <x-button
                                    type="button"
                                    class="flex-1 justify-center"
                                    x-on:click="$store.confirmDialog.open({
                                        title: 'Approve this refund?',
                                        message: 'This refunds {{ \App\Support\Settings::currency()->format($refundRequest->refund_amount_cents / 100) }} (prorated for unused days) via Stripe, un-features the listing immediately, and notifies the agent. This cannot be undone.',
                                        variant: 'danger',
                                        confirmText: 'Approve &amp; Refund',
                                        onConfirm: () =&gt; $wire.approve({{ $refundRequest->id }}),
                                    })"
                                >
                                    Approve
                                </x-button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $refundRequests->links() }}
        </div>
    @endif
</div>
