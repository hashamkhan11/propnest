<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
    <x-page-header kicker="Inbox" title="Inquiries">
        <x-slot:description>{{ $inquiries->total() }} {{ Str::plural('message', $inquiries->total()) }} from buyers. @if (count($unreadIds) > 0)<span class="text-primary-900 font-medium">{{ count($unreadIds) }} waiting on you.</span> @endif A quick reply wins viewings.</x-slot:description>
    </x-page-header>

    @if ($inquiries->isEmpty())
        <x-empty-state title="No inquiries yet" description="When a buyer messages you about one of your listings, it'll show up here.">
            <x-slot name="icon">
                <x-icon.mail class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <div wire:loading.class="opacity-50 pointer-events-none" wire:target="previousPage,nextPage,gotoPage" class="transition-opacity duration-150">
            <div class="space-y-4">
                @foreach ($inquiries as $index => $inquiry)
                    @php
                        $isUnread = in_array($inquiry->id, $unreadIds);
                    @endphp
                    <div
                        class="animate-fade-in-up bg-white rounded-xl border {{ $isUnread ? 'border-accent-500/40' : 'border-gray-900/10' }} hover:border-gray-900/25 transition-all p-4 sm:p-5"
                        style="animation-delay: {{ min($index, 8) * 40 }}ms"
                        wire:loading.class="opacity-40 pointer-events-none"
                        wire:target="sendReply({{ $inquiry->id }})"
                    >
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="relative shrink-0">
                                <x-user-avatar :user="$inquiry->buyer" size="w-11 h-11 sm:w-12 sm:h-12" textClass="font-semibold" />
                                @if ($isUnread)
                                    <span class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-accent-500 ring-2 ring-white"></span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-semibold text-gray-900">{{ $inquiry->buyer->name }}</span>
                                            @if ($isUnread)
                                                <x-badge variant="accent">New</x-badge>
                                            @elseif ($inquiry->replied_at)
                                                <x-badge variant="success">Replied</x-badge>
                                            @else
                                                <x-badge variant="warning">Awaiting reply</x-badge>
                                            @endif
                                        </div>
                                        <a href="{{ route('properties.show', $inquiry->property) }}" wire:navigate class="inline-flex items-center gap-1 text-sm text-primary-900 hover:text-primary-800 hover:underline mt-0.5 truncate">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                                            <span class="truncate">{{ $inquiry->property->title }}</span>
                                        </a>
                                    </div>
                                    <div class="text-xs text-gray-400 shrink-0 whitespace-nowrap">{{ $inquiry->created_at->diffForHumans() }}</div>
                                </div>

                                <div class="mt-3 bg-gray-50 rounded-xl rounded-tl-sm px-4 py-3">
                                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $inquiry->message }}</p>
                                </div>

                                <p class="mt-2 text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                    <a href="mailto:{{ $inquiry->buyer->email }}" class="hover:text-accent-700 hover:underline">{{ $inquiry->buyer->email }}</a>
                                </p>

                                @if ($inquiry->replied_at)
                                    <div class="mt-3 flex justify-end">
                                        <div class="max-w-[85%] bg-primary-900 text-white rounded-xl rounded-tr-sm px-4 py-3">
                                            <p class="text-xs text-primary-100 mb-1">Your reply &middot; {{ $inquiry->replied_at->diffForHumans() }}</p>
                                            <p class="text-sm whitespace-pre-line">{{ $inquiry->reply }}</p>
                                        </div>
                                    </div>
                                @elseif ($replyingId === $inquiry->id)
                                    <div class="mt-3 border border-gray-900/10 rounded-xl p-3 bg-white">
                                        <textarea
                                            wire:model="replyMessage"
                                            rows="3"
                                            maxlength="2000"
                                            class="w-full rounded-lg border-gray-300 focus:border-primary-900 focus:ring-primary-900 text-sm resize-none"
                                            placeholder="Write your reply&hellip;"
                                            wire:loading.attr="disabled"
                                            wire:target="sendReply({{ $inquiry->id }})"
                                        ></textarea>
                                        <x-input-error :messages="$errors->get('replyMessage')" class="mt-1" />
                                        <div class="mt-2 flex gap-2">
                                            <x-button wire:click="sendReply({{ $inquiry->id }})" wire:loading.attr="disabled" wire:target="sendReply({{ $inquiry->id }})">
                                                <span wire:loading.remove wire:target="sendReply({{ $inquiry->id }})">Send Reply</span>
                                                <span wire:loading wire:target="sendReply({{ $inquiry->id }})" class="inline-flex items-center gap-2">
                                                    <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                                    Sending&hellip;
                                                </span>
                                            </x-button>
                                            <x-button variant="secondary" wire:click="cancelReply">Cancel</x-button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <x-button variant="secondary" wire:click="startReply({{ $inquiry->id }})" class="inline-flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                            Reply
                                        </x-button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>
