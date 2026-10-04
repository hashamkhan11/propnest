<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-admin.page-header
            title="Dashboard"
            icon="squares-2x2"
            subtitle="Overview of platform activity"
        />

        @php
            $stats = [
                ['label' => 'Total Users', 'value' => $totalUsers, 'icon' => 'users', 'color' => 'primary'],
                ['label' => 'Agents', 'value' => $totalAgents, 'icon' => 'identification', 'color' => 'primary'],
                ['label' => 'Buyers', 'value' => $totalBuyers, 'icon' => 'user', 'color' => 'primary'],
                ['label' => 'Properties', 'value' => $totalProperties, 'icon' => 'building-office-2', 'color' => 'primary'],
                ['label' => 'Featured Listings', 'value' => $featuredCount, 'icon' => 'star', 'color' => 'accent'],
                ['label' => 'Pending Reviews', 'value' => $pendingReviewCount, 'icon' => 'clock', 'color' => 'accent'],
                ['label' => 'Admins', 'value' => $totalAdmins, 'icon' => 'shield-check', 'color' => 'primary'],
                ['label' => 'Pending Reports', 'value' => $pendingReportsCount, 'icon' => 'flag', 'color' => 'accent'],
            ];
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            @foreach ($stats as $i => $stat)
                <x-admin.stat-card
                    :label="$stat['label']"
                    :value="$stat['value']"
                    :icon="$stat['icon']"
                    :color="$stat['color']"
                    style="animation-delay: {{ $i * 40 }}ms"
                />
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <x-admin.stat-card label="Revenue" :value="\App\Support\Settings::currency()->format($revenue)" icon="banknotes" color="primary" class="col-span-2 lg:col-span-1" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <x-card title="Recent Payments">
                @forelse ($recentPayments as $payment)
                    <div class="py-2.5 first:pt-0 last:pb-0 border-b border-gray-50 last:border-0 text-sm">
                        <span class="font-medium text-gray-900"><x-price :amount="$payment->amount / 100" /></span>
                        <span class="text-gray-500">by {{ $payment->agent->name }}</span>
                        <span class="block text-gray-400 text-xs mt-0.5">{{ $payment->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-empty-state title="No payments yet" description="Payments will appear here once agents start featuring listings.">
                        <x-slot name="icon">
                            <x-heroicon-o-banknotes class="w-7 h-7" />
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-card>

            <x-card title="Recent Listings">
                @forelse ($recentProperties as $property)
                    <div class="py-2.5 first:pt-0 last:pb-0 border-b border-gray-50 last:border-0 text-sm">
                        <span class="font-medium text-gray-900 truncate">{{ $property->title }}</span>
                        <span class="block text-gray-400 text-xs mt-0.5">{{ $property->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-empty-state title="No listings yet" description="New property listings will appear here.">
                        <x-slot name="icon">
                            <x-heroicon-o-building-office-2 class="w-7 h-7" />
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-card>

            <x-card title="Recent New Users">
                @forelse ($recentUsers as $user)
                    <div class="py-2.5 first:pt-0 last:pb-0 border-b border-gray-50 last:border-0 text-sm">
                        <span class="font-medium text-gray-900">{{ $user->name }}</span>
                        <span class="block text-gray-400 text-xs mt-0.5">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <x-empty-state title="No users yet" description="New user signups will appear here.">
                        <x-slot name="icon">
                            <x-heroicon-o-users class="w-7 h-7" />
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-card>
        </div>
    </div>
