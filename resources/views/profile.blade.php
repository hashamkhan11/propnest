<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-700 text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-photo-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            @if (auth()->user()->role === \App\Enums\User\UserRole::Agent)
                <div class="p-4 sm:p-8 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="max-w-xl">
                        <livewire:agent.profile-form />
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
