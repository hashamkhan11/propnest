@php
    $verified = auth()->user()->agentProfile?->verification_status === \App\Enums\User\AgentVerificationStatus::Verified;
@endphp

<div>
    <div class="flex items-center justify-between gap-4 mb-4 flex-wrap">
        <h2 class="font-heading font-700 text-lg text-gray-900">Agent Profile</h2>
        <x-badge variant="{{ $verified ? 'success' : 'warning' }}">
            {{ $verified ? 'Verified Agent' : 'Verification Pending' }}
        </x-badge>
    </div>

    <p class="text-sm text-gray-500 -mt-2 mb-6">This information appears on your public agent profile.</p>

    <form wire:submit="save" class="space-y-6">
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
                class="mt-1 border-gray-300 focus:border-primary-600 focus:ring-primary-600 rounded-md shadow-sm w-full"
            ></textarea>
            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-button type="submit">Save Changes</x-button>

            @if (session('success'))
                <div class="flex items-center gap-1.5 text-sm font-medium text-primary-700 animate-fade-in-up">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </form>
</div>
