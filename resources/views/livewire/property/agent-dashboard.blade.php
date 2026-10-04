<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="font-heading font-800 text-2xl text-gray-900 mb-1">Agent Dashboard</h1>
    <p class="text-gray-500 mb-6">Here's how your listings are performing.</p>

    @if ($profileIncomplete)
        <div class="flex items-center justify-between gap-3 flex-wrap bg-accent-50 border border-accent-200 rounded-xl px-4 py-3 mb-6">
            <span class="inline-flex items-center gap-2 text-accent-800 text-sm font-medium">
                <x-icon.alert-circle class="w-5 h-5 shrink-0" />
                Complete your agent profile so buyers can learn more about you.
            </span>
            <a href="{{ route('profile') }}" wire:navigate>
                <x-button variant="accent">Complete Profile</x-button>
            </a>
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <x-icon.house-key class="w-5 h-5" />
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $totalCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Properties</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <x-icon.check-circle class="w-5 h-5" />
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $publishedCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Published</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $draftCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Draft</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-accent-50 text-accent-600 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $soldRentedCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Sold / Rented</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <x-icon.mail class="w-5 h-5" />
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $unreadInquiriesCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Unread Inquiries</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <x-icon.eye class="w-5 h-5" />
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $totalViewsCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Views</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="w-9 h-9 rounded-lg bg-primary-50 text-primary-600 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>
            </div>
            <p class="font-heading font-800 text-2xl text-gray-900">{{ $totalInquiriesCount }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Inquiries</p>
        </div>
    </div>

    <x-card title="Quick Links">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('agent.properties.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <x-icon.house-key class="w-5 h-5 text-primary-600" />
                <span class="text-xs font-semibold text-gray-700">My Properties</span>
            </a>

            <a href="{{ route('agent.properties.create') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                <span class="text-xs font-semibold text-gray-700">Create Property</span>
            </a>

            <a href="{{ route('agent.properties.index', ['statuses' => ['draft']]) }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                <span class="text-xs font-semibold text-gray-700">Draft Listings</span>
            </a>

            <a href="{{ route('agent.properties.index', ['statuses' => ['published']]) }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <x-icon.check-circle class="w-5 h-5 text-primary-600" />
                <span class="text-xs font-semibold text-gray-700">Published Listings</span>
            </a>

            <a href="{{ route('agent.properties.index', ['statuses' => ['sold', 'rented']]) }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
                <span class="text-xs font-semibold text-gray-700">Sold / Rented</span>
            </a>

            <a href="{{ route('agent.inquiries.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <x-icon.mail class="w-5 h-5 text-primary-600" />
                <span class="text-xs font-semibold text-gray-700">Inquiries</span>
            </a>

            <a href="{{ route('agent.subscriptions.index') }}" wire:navigate class="flex flex-col items-center gap-2 text-center rounded-xl border border-gray-100 hover:border-primary-200 hover:bg-primary-50 transition-colors px-3 py-4">
                <x-icon.star class="w-5 h-5 text-primary-600" />
                <span class="text-xs font-semibold text-gray-700">Subscription</span>
            </a>
        </div>
    </x-card>
</div>
