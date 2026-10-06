<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-page-header kicker="Account" title="Settings" description="How you appear on PropNest, how you sign in, and the way out if you ever need it." />

            <div class="bg-white rounded-xl border border-gray-900/10 divide-y divide-gray-900/10">
                <div class="p-6 sm:p-8">
                    <livewire:profile.update-profile-photo-form />
                </div>

                <div class="p-6 sm:p-8">
                    <livewire:profile.update-profile-information-form />
                </div>

                @if (auth()->user()->role === \App\Enums\User\UserRole::Agent)
                    <div class="p-6 sm:p-8">
                        <livewire:agent.profile-form />
                    </div>
                @endif

                <div class="p-6 sm:p-8">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="mt-8 bg-white rounded-xl border border-red-200 p-6 sm:p-8">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
