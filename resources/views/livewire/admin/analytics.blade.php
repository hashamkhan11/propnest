<div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="Overview"
        title="Analytics"
        icon="chart-bar"
        subtitle="The last twelve months: money in, people joining, and where the listings are."
    />

    @php
        $figures = [
            ['label' => 'Revenue, 12 months', 'value' => \App\Support\Settings::currency()->format($revenueByMonth->sum())],
            ['label' => 'New users, 12 months', 'value' => number_format($usersByMonth->sum())],
            ['label' => 'Listings on file', 'value' => number_format($listingsByStatus->sum())],
            ['label' => 'Refunded payments', 'value' => number_format($refundTotal)],
        ];
        $statusSorted = $listingsByStatus->sortDesc();
        $topRevenue = max(1, (int) ($topAgents->max('total_revenue') ?? 1));
    @endphp

    <dl class="grid grid-cols-2 lg:grid-cols-4 gap-px bg-gray-900/10 rounded-xl border border-gray-900/10 overflow-hidden mb-6">
        @foreach ($figures as $figure)
            <div class="bg-white px-5 py-5">
                <dt class="text-sm text-gray-500">{{ $figure['label'] }}</dt>
                <dd class="figure mt-1 text-[28px] font-semibold tracking-tight text-primary-900">{{ $figure['value'] }}</dd>
            </div>
        @endforeach
    </dl>

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
                    money
                    aria-label="Revenue by month"
                    :labels="$revenueByMonth->keys()->all()"
                    :datasets="[['label' => 'Revenue', 'data' => $revenueByMonth->values()->all(), 'fill' => true]]"
                />
            @endif
        </x-card>

        <x-card title="New users by month">
            @if ($usersByMonth->sum() == 0)
                <x-empty-state title="No new users yet" description="New signups will chart here.">
                    <x-slot name="icon">
                        <x-heroicon-o-users class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @else
                <x-admin.chart
                    type="line"
                    aria-label="New users by month"
                    :labels="$usersByMonth->keys()->all()"
                    :datasets="[['label' => 'New users', 'data' => $usersByMonth->values()->all(), 'fill' => true]]"
                />
            @endif
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-card title="Listings by status">
            <x-admin.chart
                type="bar"
                horizontal
                height="240px"
                aria-label="Listings by status"
                :labels="$statusSorted->keys()->map(fn ($s) => \Illuminate\Support\Str::headline($s))->values()->all()"
                :datasets="[['label' => 'Listings', 'data' => $statusSorted->values()->all()]]"
            />
        </x-card>

        <x-card title="Listings by type">
            <x-admin.chart
                type="bar"
                height="240px"
                aria-label="Listings by type"
                :labels="$listingsByCategory->keys()->all()"
                :datasets="[['label' => 'Listings', 'data' => $listingsByCategory->values()->all()]]"
            />
        </x-card>

        <x-card title="Listings by region">
            <x-admin.chart
                type="bar"
                height="240px"
                aria-label="Listings by region"
                :labels="$listingsByRegion->keys()->all()"
                :datasets="[['label' => 'Listings', 'data' => $listingsByRegion->values()->all()]]"
            />
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-card title="Top agents by revenue">
            <ul class="space-y-4">
                @forelse ($topAgents as $agent)
                    <li class="text-sm">
                        <div class="flex justify-between items-baseline gap-3">
                            <span class="text-primary-900 font-medium truncate">{{ $agent->agent_name }}</span>
                            <span class="figure text-primary-900 shrink-0"><x-price :amount="$agent->total_revenue / 100" /> <span class="text-gray-400">&middot; {{ $agent->payment_count }} {{ \Illuminate\Support\Str::plural('payment', $agent->payment_count) }}</span></span>
                        </div>
                        <div class="mt-1.5 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full bg-accent-500" style="width: {{ round($agent->total_revenue / $topRevenue * 100) }}%"></div>
                        </div>
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

        <x-card title="Reports by outcome">
            @if ($reportCountsByStatus->isEmpty())
                <x-empty-state title="No reports yet" description="Listing reports will appear here.">
                    <x-slot name="icon">
                        <x-heroicon-o-flag class="w-7 h-7" />
                    </x-slot>
                </x-empty-state>
            @else
                <ul class="divide-y divide-gray-900/10">
                    @foreach ($reportCountsByStatus as $status => $total)
                        <li class="flex justify-between items-center py-3 first:pt-0 last:pb-0 text-sm">
                            <span class="text-primary-900">{{ \Illuminate\Support\Str::headline($status) }}</span>
                            <span class="figure font-medium text-primary-900">{{ $total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>
</div>
