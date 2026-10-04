<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Analytics"
        icon="chart-bar"
        subtitle="Platform performance over the last 12 months"
    />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <x-admin.stat-card label="Revenue (12mo)" :value="\App\Support\Settings::currency()->format($revenueByMonth->sum())" icon="banknotes" />
        <x-admin.stat-card label="New Users (12mo)" :value="$usersByMonth->sum()" icon="users" />
        <x-admin.stat-card label="Total Listings" :value="$listingsByStatus->sum()" icon="building-office-2" />
        <x-admin.stat-card label="Refunded Payments" :value="$refundTotal" icon="arrow-uturn-left" color="accent" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-card title="Revenue by Month">
            @if ($revenueByMonth->sum() == 0)
                <x-empty-state title="No revenue yet" description="Completed payments will chart here.">
                    <x-slot name="icon">
                        <x-heroicon-o-banknotes class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @else
                <x-admin.chart
                    type="line"
                    :labels="$revenueByMonth->keys()->all()"
                    :datasets="[['label' => 'Revenue', 'data' => $revenueByMonth->values()->all(), 'fill' => true, 'tension' => 0.3]]"
                />
            @endif
        </x-card>

        <x-card title="New Users by Month">
            @if ($usersByMonth->sum() == 0)
                <x-empty-state title="No new users yet" description="New signups will chart here.">
                    <x-slot name="icon">
                        <x-heroicon-o-users class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @else
                <x-admin.chart
                    type="line"
                    :labels="$usersByMonth->keys()->all()"
                    :datasets="[['label' => 'New Users', 'data' => $usersByMonth->values()->all(), 'fill' => true, 'tension' => 0.3]]"
                />
            @endif
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-card title="Listings by Status">
            <x-admin.chart
                type="doughnut"
                height="220px"
                :labels="$listingsByStatus->keys()->map(fn ($s) => \Illuminate\Support\Str::headline($s))->all()"
                :datasets="[['data' => $listingsByStatus->values()->all()]]"
            />
        </x-card>

        <x-card title="Listings by Category">
            <x-admin.chart
                type="bar"
                height="220px"
                :labels="$listingsByCategory->keys()->all()"
                :datasets="[['data' => $listingsByCategory->values()->all()]]"
            />
        </x-card>

        <x-card title="Listings by Region">
            <x-admin.chart
                type="bar"
                height="220px"
                :labels="$listingsByRegion->keys()->all()"
                :datasets="[['data' => $listingsByRegion->values()->all()]]"
            />
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Top Agents by Revenue">
            <ul class="divide-y divide-gray-50">
                @forelse ($topAgents as $agent)
                    <li class="flex justify-between items-center py-2.5 first:pt-0 last:pb-0 text-sm">
                        <span class="text-gray-900 font-medium">{{ $agent->agent_name }}</span>
                        <span class="text-gray-500"><x-price :amount="$agent->total_revenue / 100" /> <span class="text-gray-400">({{ $agent->payment_count }})</span></span>
                    </li>
                @empty
                    <x-empty-state title="No completed payments yet" description="Top agents will appear here once payments come in.">
                        <x-slot name="icon">
                            <x-heroicon-o-star class="w-7 h-7" />
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </ul>
        </x-card>

        <x-card title="Reports by Status">
            @if ($reportCountsByStatus->isEmpty())
                <x-empty-state title="No reports yet" description="Listing reports will appear here.">
                    <x-slot name="icon">
                        <x-heroicon-o-flag class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @else
                <ul class="divide-y divide-gray-50">
                    @foreach ($reportCountsByStatus as $status => $total)
                        <li class="flex justify-between items-center py-2.5 first:pt-0 last:pb-0 text-sm">
                            <span class="text-gray-900">{{ \Illuminate\Support\Str::headline($status) }}</span>
                            <span class="text-gray-500 font-medium">{{ $total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
</div>
