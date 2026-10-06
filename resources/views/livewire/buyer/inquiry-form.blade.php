<div>
    @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer)
        <div class="rounded-xl border border-gray-900/10 bg-white p-5">
            @if ($justSent)
                <div class="py-2">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                        <x-icon.check-circle class="w-5 h-5" />
                    </div>
                    <p class="font-semibold text-primary-900">Sent. The agent has your message.</p>
                    <p class="text-sm text-gray-500 mt-1">You will get their reply by email, and it will show up in your dashboard.</p>
                    <button type="button" wire:click="sendAnother" class="mt-4 text-sm text-primary-900 font-medium link-underline">
                        Write another message
                    </button>
                </div>
            @else
                <h3 class="font-semibold text-primary-900">Ask about this home</h3>
                <p class="text-sm text-gray-500 mt-1 mb-4">Goes straight to the agent. Most reply within a day.</p>

                <form wire:submit="send" class="space-y-3">
                    <div x-data="{ count: {{ strlen($message) }} }">
                        <label for="message" class="sr-only">Your message</label>
                        <textarea
                            wire:model="message"
                            id="message"
                            rows="5"
                            maxlength="2000"
                            x-on:input="count = $el.value.length"
                            class="border-gray-300 hover:border-gray-400 focus:border-primary-900 focus:ring-1 focus:ring-primary-900 rounded-md w-full text-sm leading-relaxed resize-none disabled:bg-gray-50 disabled:text-gray-400"
                            placeholder="Hi, is this still available? I'd like to arrange a viewing this week."
                            wire:loading.attr="disabled"
                            wire:target="send"
                        ></textarea>
                        <div class="flex justify-between mt-1">
                            <x-input-error :messages="$errors->get('message')" />
                            <span class="font-mono text-[11px] text-gray-400 ml-auto" x-text="count + ' / 2000'"></span>
                        </div>
                    </div>

                    <x-button type="submit" variant="accent" class="w-full" wire:loading.attr="disabled" wire:target="send">
                        <span wire:loading.remove wire:target="send">Send message</span>
                        <span wire:loading wire:target="send" class="inline-flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                            Sending&hellip;
                        </span>
                    </x-button>

                    <p class="text-xs text-gray-500">The agent will see your name and email so they can reply.</p>
                </form>
            @endif
        </div>
    @elseif (! auth()->check())
        <div class="rounded-xl bg-primary-900 text-gray-300 p-5">
            <h3 class="font-semibold text-white">Ask about this home</h3>
            <p class="text-sm mt-1 mb-4">Sign in to message the agent directly. Free, and no spam.</p>
            <a href="{{ route('login') }}" @click="openAuth('login', $event)">
                <x-button variant="accent" class="w-full">Sign in to message</x-button>
            </a>
        </div>
    @endif
</div>
