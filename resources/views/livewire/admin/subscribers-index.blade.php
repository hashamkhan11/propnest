<div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Audience"
        title="Subscribers"
        icon="megaphone"
        :subtitle="$subscribers->total() . ' ' . \Illuminate\Support\Str::plural('subscriber', $subscribers->total())"
    />

    @if ($subscribers->isEmpty())
        <x-empty-state title="No subscribers yet" description="Emails submitted through the newsletter signup form will appear here.">
            <x-slot name="icon">
                <x-heroicon-o-megaphone class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Email', 'Subscribed', 'Status']">
            @foreach ($subscribers as $subscriber)
                <tr wire:key="subscriber-row-{{ $subscriber->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors align-top">
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $subscriber->email }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $subscriber->created_at->format('M j, Y') }}</td>
                    <td class="py-3 px-4 text-right">
                        <x-badge :variant="$subscriber->status->badgeVariant()">{{ $subscriber->status->label() }}</x-badge>
                    </td>
                </tr>
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($subscribers as $subscriber)
                <div wire:key="subscriber-card-{{ $subscriber->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate">{{ $subscriber->email }}</p>
                        <p class="text-sm text-gray-500">Subscribed {{ $subscriber->created_at->format('M j, Y') }}</p>
                    </div>
                    <x-badge :variant="$subscriber->status->badgeVariant()" class="shrink-0">{{ $subscriber->status->label() }}</x-badge>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $subscribers->links() }}
        </div>
    @endif
</div>
