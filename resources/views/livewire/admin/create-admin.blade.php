<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Create Admin"
        icon="user-plus"
        subtitle="Grant admin access to a new team member"
    />

    <x-card class="max-w-xl">
        <form wire:submit="save" class="space-y-6">
            <div>
                <x-input-label for="name" value="Name" />
                <x-input wire:model="name" id="name" type="text" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-input wire:model="email" id="email" type="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <x-input wire:model="password" id="password" type="password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-input wire:model="password_confirmation" id="password_confirmation" type="password" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-button type="submit">
                    <span wire:loading.remove wire:target="save">Create Admin</span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Creating&hellip;
                    </span>
                </x-button>

                @if (session('success'))
                    <p class="text-sm text-primary-700 font-medium">{{ session('success') }}</p>
                @endif
            </div>
        </form>
    </x-card>
</div>
