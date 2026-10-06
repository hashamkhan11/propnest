<div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Audience"
        title="Messages"
        icon="envelope"
        :subtitle="$messages->total() . ' ' . \Illuminate\Support\Str::plural('message', $messages->total())"
    />

    @if ($messages->isEmpty())
        <x-empty-state title="No contact messages yet" description="Messages submitted through the Contact Us form will appear here.">
            <x-slot name="icon">
                <x-heroicon-o-envelope class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Sender', 'Subject', 'Date', 'Status', 'Actions']">
            @foreach ($messages as $message)
                <tr wire:key="contact-message-row-{{ $message->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors align-top">
                    <td class="py-3 px-4 text-sm">
                        <p class="text-gray-900 font-medium">{{ $message->name }}</p>
                        <p class="text-gray-500">{{ $message->email }}</p>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-700 max-w-[240px] truncate">{{ $message->subject }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $message->created_at->format('M j, Y') }}</td>
                    <td class="py-3 px-4">
                        <x-badge :variant="$message->status->badgeVariant()">{{ $message->status->label() }}</x-badge>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <x-button type="button" variant="secondary" wire:click="{{ $viewingMessageId === $message->id ? 'closeView' : 'view(' . $message->id . ')' }}">
                            {{ $viewingMessageId === $message->id ? 'Hide' : 'View' }}
                        </x-button>
                    </td>
                </tr>
                @if ($viewingMessageId === $message->id)
                    <tr wire:key="contact-message-detail-{{ $message->id }}" class="border-b border-gray-50 last:border-0 bg-gray-50/60">
                        <td colspan="5" class="py-4 px-4">
                            @include('livewire.admin.partials.contact-message-thread', ['message' => $message])
                        </td>
                    </tr>
                @endif
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($messages as $message)
                <div wire:key="contact-message-card-{{ $message->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $message->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $message->email }}</p>
                        </div>
                        <x-badge :variant="$message->status->badgeVariant()" class="shrink-0">{{ $message->status->label() }}</x-badge>
                    </div>

                    <div class="mt-3 text-sm">
                        <p class="text-gray-900 font-medium">{{ $message->subject }}</p>
                        <p class="text-gray-500 mt-1">{{ $message->created_at->format('M j, Y') }}</p>
                    </div>

                    @if ($viewingMessageId === $message->id)
                        <div class="mt-3 pt-3 border-t border-gray-900/10">
                            @include('livewire.admin.partials.contact-message-thread', ['message' => $message])
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-gray-900/10">
                        <x-button
                            type="button"
                            variant="secondary"
                            wire:click="{{ $viewingMessageId === $message->id ? 'closeView' : 'view(' . $message->id . ')' }}"
                            class="w-full justify-center"
                        >
                            {{ $viewingMessageId === $message->id ? 'Hide message' : 'View message' }}
                        </x-button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $messages->links() }}
        </div>
    @endif
</div>
