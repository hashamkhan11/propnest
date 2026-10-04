<div>
    @if (auth()->user()?->role === \App\Enums\User\UserRole::Buyer)
        <x-card>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                    <x-icon.mail class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="font-heading font-700 text-lg text-gray-900 leading-tight">Contact Agent</h3>
                    <p class="text-xs text-gray-500">Usually responds within a day</p>
                </div>
            </div>

            @if ($justSent)
                <div class="animate-fade-in-up text-center py-4">
                    <div class="w-14 h-14 mx-auto rounded-full bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                        <x-icon.check-circle class="w-7 h-7" />
                    </div>
                    <p class="font-heading font-700 text-gray-900">Message sent!</p>
                    <p class="text-sm text-gray-500 mt-1">The agent has been notified and will get back to you soon.</p>
                    <button
                        type="button"
                        wire:click="sendAnother"
                        class="mt-4 text-sm font-semibold text-primary-600 hover:text-primary-700"
                    >
                        Send another message
                    </button>
                </div>
            @else
                <form wire:submit="send" class="space-y-4">
                    <div x-data="{ count: {{ strlen($message) }} }">
                        <div class="flex items-center justify-between mb-1">
                            <x-input-label for="message" value="Your message" />
                            <span class="text-xs text-gray-400" x-text="count + ' / 2000'"></span>
                        </div>
                        <textarea
                            wire:model="message"
                            id="message"
                            rows="4"
                            maxlength="2000"
                            x-on:input="count = $el.value.length"
                            class="border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-xl shadow-sm w-full text-sm transition-colors resize-none disabled:bg-gray-50 disabled:text-gray-400"
                            placeholder="I'm interested in this property. Could you share more details or arrange a viewing?"
                            wire:loading.attr="disabled"
                            wire:target="send"
                        ></textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>

                    <x-button type="submit" class="w-full justify-center" wire:loading.attr="disabled" wire:target="send">
                        <span wire:loading.remove wire:target="send" class="inline-flex items-center gap-2">
                            <x-icon.mail class="w-4 h-4" />
                            Send Inquiry
                        </span>
                        <span wire:loading wire:target="send" class="inline-flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                            Sending&hellip;
                        </span>
                    </x-button>

                    <p class="text-xs text-gray-400 text-center">Your contact details will be shared with the agent so they can reply.</p>
                </form>
            @endif
        </x-card>
    @elseif (! auth()->check())
        <x-card>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0">
                    <x-icon.mail class="w-5 h-5" />
                </div>
                <h3 class="font-heading font-700 text-lg text-gray-900">Contact Agent</h3>
            </div>
            <p class="text-gray-600 text-sm">
                <a href="{{ route('login') }}" wire:navigate class="text-primary-700 font-semibold hover:underline">Log in</a>
                as a buyer to send this agent a message about this property.
            </p>
        </x-card>
    @endif
</div>
