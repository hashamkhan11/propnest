@props(['message'])

<div>
    <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-1.5">Message</p>
    <div class="bg-white rounded-xl rounded-tl-sm border border-gray-100 px-4 py-3 max-w-2xl">
        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $message->message }}</p>
    </div>
    <p class="mt-2 text-xs text-gray-400 flex items-center gap-1">
        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
        <a href="mailto:{{ $message->email }}" class="hover:text-primary-600 hover:underline">{{ $message->email }}</a>
    </p>

    @if ($message->replied_at)
        <div class="mt-3 flex justify-end">
            <div class="max-w-2xl w-full sm:w-auto bg-primary-600 text-white rounded-xl rounded-tr-sm px-4 py-3">
                <p class="text-xs text-primary-100 mb-1">
                    Your reply
                    @if ($message->repliedBy)
                        &middot; {{ $message->repliedBy->name }}
                    @endif
                    &middot; {{ $message->replied_at->diffForHumans() }}
                </p>
                <p class="text-sm whitespace-pre-line">{{ $message->reply }}</p>
            </div>
        </div>
    @elseif ($replyingId === $message->id)
        <div class="mt-3 border border-gray-100 rounded-xl p-3 bg-white max-w-2xl">
            <textarea
                wire:model="replyMessage"
                rows="4"
                maxlength="5000"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-600 focus:ring-primary-600 text-sm resize-none"
                placeholder="Write your reply&hellip;"
                wire:loading.attr="disabled"
                wire:target="sendReply({{ $message->id }})"
            ></textarea>
            <x-input-error :messages="$errors->get('replyMessage')" class="mt-1" />
            <div class="mt-2 flex gap-2">
                <x-button wire:click="sendReply({{ $message->id }})" wire:loading.attr="disabled" wire:target="sendReply({{ $message->id }})">
                    <span wire:loading.remove wire:target="sendReply({{ $message->id }})">Send Reply</span>
                    <span wire:loading wire:target="sendReply({{ $message->id }})" class="inline-flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                        Sending&hellip;
                    </span>
                </x-button>
                <x-button variant="secondary" wire:click="cancelReply" wire:loading.attr="disabled" wire:target="sendReply({{ $message->id }})">Cancel</x-button>
            </div>
        </div>
    @else
        <div class="mt-3">
            <x-button variant="secondary" wire:click="startReply({{ $message->id }})" class="inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                Reply
            </x-button>
        </div>
    @endif
</div>
