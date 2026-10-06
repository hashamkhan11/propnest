<div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Money"
        title="Payments"
        icon="banknotes"
        :subtitle="$payments->total() . ' ' . \Illuminate\Support\Str::plural('payment', $payments->total()) . ' for featured listings'"
    />

    @if ($payments->isEmpty())
        <x-empty-state
            title="No payments yet"
            description="Payments will appear here once agents start featuring their listings."
        >
            <x-slot name="icon">
                <x-icon.banknotes class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        {{-- Desktop / tablet table --}}
        <x-admin.data-table :headers="['Agent', 'Property', 'Amount', 'Status', 'Featured Until', 'Created', 'Actions']">
            @foreach ($payments as $payment)
                @php($featuredUntilDisplay = $payment->featured_until ?? (($payment->agentSubscription && $payment->agentSubscription->plan?->featured_credits > 0) ? $payment->agentSubscription->expires_at : null))
                <tr wire:key="payment-row-{{ $payment->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors">
                    <td class="py-3 px-4 text-sm text-gray-700">{{ $payment->agent->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium max-w-[220px] truncate">{{ $payment->property->title ?? $payment->agentSubscription?->plan?->name ?? '—' }}</td>
                    <td class="py-3 px-4 text-sm text-gray-700"><x-price :amount="$payment->amount / 100" /></td>
                    <td class="py-3 px-4">
                        <x-badge :variant="$payment->status->badgeVariant()">
                            {{ $payment->status->label() }}
                        </x-badge>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $featuredUntilDisplay?->format('M j, Y') ?? '—' }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $payment->created_at->format('M j, Y') }}</td>
                    <td class="py-3 px-4 text-right">
                        @include('livewire.admin.partials.payment-actions', ['payment' => $payment])
                    </td>
                </tr>
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($payments as $payment)
                @php($featuredUntilDisplay = $payment->featured_until ?? (($payment->agentSubscription && $payment->agentSubscription->plan?->featured_credits > 0) ? $payment->agentSubscription->expires_at : null))
                <div wire:key="payment-card-{{ $payment->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $payment->property->title ?? $payment->agentSubscription?->plan?->name ?? '—' }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $payment->agent->name }}</p>
                        </div>
                        <x-badge :variant="$payment->status->badgeVariant()" class="shrink-0">
                            {{ $payment->status->label() }}
                        </x-badge>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-y-1.5 text-sm">
                        <span class="text-gray-500">Amount</span>
                        <span class="text-gray-900 text-right font-medium"><x-price :amount="$payment->amount / 100" /></span>
                        <span class="text-gray-500">Featured until</span>
                        <span class="text-gray-700 text-right">{{ $featuredUntilDisplay?->format('M j, Y') ?? '—' }}</span>
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-700 text-right">{{ $payment->created_at->format('M j, Y') }}</span>
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-900/10 flex justify-end">
                        @include('livewire.admin.partials.payment-actions', ['payment' => $payment])
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @endif
</div>
