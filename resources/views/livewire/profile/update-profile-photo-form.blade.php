<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $photo;

    /**
     * Validate and store the newly selected photo, replacing any existing one.
     */
    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();
        $oldPath = $user->profile_photo_path;

        $path = $this->photo->store('avatars', 'public');

        $user->forceFill(['profile_photo_path' => $path])->save();

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->reset('photo');

        $this->dispatch('profile-photo-updated', url: $user->profile_photo_url);
    }

    /**
     * Remove the current photo and fall back to the default initials avatar.
     */
    public function removePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }

        $this->dispatch('profile-photo-updated', url: null);
    }
}; ?>

<section class="grid md:grid-cols-12 gap-x-10 gap-y-5">
    <header class="md:col-span-4">
        <h2 class="text-[15px] font-semibold text-primary-900">Photo</h2>
        <p class="mt-1 text-sm text-gray-500 leading-relaxed">Shown on your inquiries and, for agents, on every listing. JPG, PNG or WEBP up to 2 MB.</p>
    </header>

    <div class="md:col-span-8 flex items-center gap-6">
        <div class="relative shrink-0 w-20 h-20">
            @if ($photo)
                <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 rounded-full object-cover ring-4 ring-white">
            @else
                <x-user-avatar :user="Auth::user()" size="w-20 h-20" textClass="font-semibold text-2xl" class="ring-4 ring-white" />
            @endif

            <div wire:loading wire:target="photo" class="absolute inset-0 rounded-full bg-white/70 flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 text-primary-900" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>
        </div>

        <div>
            <input type="file" wire:model="photo" id="photo" class="hidden" accept="image/jpeg,image/png,image/webp">

            <div class="flex items-center gap-3">
                <x-secondary-button type="button" onclick="document.getElementById('photo').click()">
                    {{ Auth::user()->profile_photo_path ? __('Change photo') : __('Upload a photo') }}
                </x-secondary-button>

                @if (Auth::user()->profile_photo_path)
                    <x-danger-button type="button" wire:click="removePhoto">
                        {{ __('Remove') }}
                    </x-danger-button>
                @endif
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />

            <x-action-message class="mt-2" on="profile-photo-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </div>
</section>
