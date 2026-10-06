@php
    $verified = auth()->user()->agentProfile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
@endphp

<section class="grid md:grid-cols-12 gap-x-10 gap-y-5">
    <header class="md:col-span-4">
        <h2 class="text-[15px] font-semibold text-primary-900">Agent profile</h2>
        <p class="mt-1 text-sm text-gray-500 leading-relaxed">What buyers see on your public page. A short, specific bio gets more inquiries.</p>
        <div class="mt-3">
            <x-badge variant="{{ $verified ? 'success' : 'warning' }}">
                {{ $verified ? 'Verified agent' : 'Verification pending' }}
            </x-badge>
        </div>
    </header>

    <form wire:submit="save" class="md:col-span-8 space-y-6">
        <div>
            <x-input-label for="agency_name" value="Agency Name" />
            <x-input wire:model="agency_name" id="agency_name" type="text" class="mt-1" placeholder="e.g. Ace Realty Group" />
            <x-input-error :messages="$errors->get('agency_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" value="Phone" />
            <x-input wire:model="phone" id="phone" type="text" class="mt-1" placeholder="e.g. (555) 123-4567" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="bio" value="Bio" />
            <textarea
                wire:model="bio"
                id="bio"
                rows="5"
                maxlength="2000"
                placeholder="Tell buyers a little about your experience and specialties..."
                class="mt-1 text-sm border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md w-full"
            ></textarea>
            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-button type="submit">Save</x-button>

            @if (session('success'))
                <div class="flex items-center gap-1.5 text-sm font-medium text-primary-900 animate-fade-in-up">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </form>
</section>
