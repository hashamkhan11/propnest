<div class="max-w-7xl px-4 sm:px-6 lg:px-10 py-10">
        <header class="flex items-end justify-between gap-6 flex-wrap pb-8 mb-10 border-b border-gray-900/10">
            <div>
                <p class="kicker mb-3">{{ now()->format('l, j F') }}</p>
                <h1 class="display text-5xl sm:text-6xl">Where things stand</h1>
            </div>
            <a href="{{ route('admin.analytics') }}" wire:navigate class="link-underline text-sm text-primary-900">Open analytics &rarr;</a>
        </header>

        {{-- Work that needs a person --}}
        @php
            $queues = [
                ['count' => $pendingReviewCount, 'label' => 'Listings waiting for review', 'route' => 'admin.moderation.index', 'cta' => 'Review now'],
                ['count' => $pendingAgentsCount, 'label' => 'Agents asking to be verified', 'route' => 'admin.agents.index', 'cta' => 'Check agents'],
                ['count' => $pendingReportsCount, 'label' => 'Open reports from buyers', 'route' => 'admin.reports.index', 'cta' => 'See reports'],
            ];
        @endphp
        <section class="mb-12">
            <h2 class="kicker mb-4">Needs you</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                @foreach ($queues as $queue)
                    <a href="{{ route($queue['route']) }}" wire:navigate
                       class="group bg-white rounded-xl border p-5 flex flex-col transition-colors {{ $queue['count'] > 0 ? 'border-accent-500/40 hover:border-accent-500' : 'border-gray-900/10 hover:border-gray-900/25' }}">
                        <div class="flex items-baseline gap-2">
                            <span class="figure text-4xl font-semibold tracking-tight {{ $queue['count'] > 0 ? 'text-accent-600' : 'text-gray-300' }}">{{ $queue['count'] }}</span>
                            @if ($queue['count'] === 0)
                                <span class="text-sm text-emerald-700">All clear</span>
                            @endif
                        </div>
                        <p class="mt-2 text-[15px] text-primary-900">{{ $queue['label'] }}</p>
                        <span class="mt-4 text-sm text-gray-500 group-hover:text-primary-900 inline-flex items-center gap-1">
                            {{ $queue['cta'] }}
                            <x-heroicon-o-arrow-right class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- The numbers --}}
        @php
            $figures = [
                ['label' => 'Users', 'value' => $totalUsers],
                ['label' => 'Buyers', 'value' => $totalBuyers],
                ['label' => 'Agents', 'value' => $totalAgents],
                ['label' => 'Listings', 'value' => $totalProperties],
                ['label' => 'Featured now', 'value' => $featuredCount],
                ['label' => 'Admins', 'value' => $totalAdmins],
            ];
        @endphp
        <section class="mb-12">
            <h2 class="kicker mb-4">The numbers</h2>
            <div class="grid lg:grid-cols-12 gap-4">
                <div class="lg:col-span-4 bg-primary-900 text-white rounded-xl p-6 flex flex-col justify-between min-h-[168px]">
                    <p class="kicker !text-gray-400">Revenue, all time</p>
                    <div>
                        <p class="figure text-4xl sm:text-5xl font-semibold tracking-tight">{{ \App\Support\Settings::currency()->format($revenue) }}</p>
                        <p class="mt-1 text-sm text-gray-400">From featured listings and agent plans</p>
                    </div>
                </div>
                <dl class="lg:col-span-8 grid grid-cols-2 sm:grid-cols-3 gap-px bg-gray-900/10 rounded-xl border border-gray-900/10 overflow-hidden">
                    @foreach ($figures as $figure)
                        <div class="bg-white px-5 py-4">
                            <dt class="text-sm text-gray-500">{{ $figure['label'] }}</dt>
                            <dd class="figure mt-1 text-2xl font-semibold tracking-tight text-primary-900">{{ number_format($figure['value']) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- Latest activity --}}
        <section>
            <h2 class="kicker mb-4">Latest activity</h2>
            <div class="grid lg:grid-cols-3 bg-white rounded-xl border border-gray-900/10 divide-y lg:divide-y-0 lg:divide-x divide-gray-900/10">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[15px] font-semibold text-primary-900">Payments</h3>
                        <a href="{{ route('admin.payments.index') }}" wire:navigate class="text-xs text-gray-500 hover:text-primary-900">All</a>
                    </div>
                    <ul class="divide-y divide-gray-900/10">
                        @forelse ($recentPayments as $payment)
                            <li class="py-3 first:pt-0 flex items-baseline justify-between gap-3 text-sm">
                                <div class="min-w-0">
                                    <p class="text-primary-900 truncate">{{ $payment->agent->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="figure font-medium text-primary-900 shrink-0"><x-price :amount="$payment->amount / 100" /></span>
                            </li>
                        @empty
                            <li class="py-6 text-sm text-gray-500">No payments yet. They show up here once agents feature a listing.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[15px] font-semibold text-primary-900">New listings</h3>
                        <a href="{{ route('admin.properties.index') }}" wire:navigate class="text-xs text-gray-500 hover:text-primary-900">All</a>
                    </div>
                    <ul class="divide-y divide-gray-900/10">
                        @forelse ($recentProperties as $property)
                            <li class="py-3 first:pt-0 text-sm">
                                <p class="text-primary-900 truncate">{{ $property->title }}</p>
                                <p class="text-xs text-gray-500">{{ $property->agent?->name }} &middot; {{ $property->created_at->diffForHumans() }}</p>
                            </li>
                        @empty
                            <li class="py-6 text-sm text-gray-500">No listings yet.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[15px] font-semibold text-primary-900">New people</h3>
                        <a href="{{ route('admin.users.index') }}" wire:navigate class="text-xs text-gray-500 hover:text-primary-900">All</a>
                    </div>
                    <ul class="divide-y divide-gray-900/10">
                        @forelse ($recentUsers as $user)
                            <li class="py-3 first:pt-0 flex items-center gap-3 text-sm">
                                <x-user-avatar :user="$user" size="w-8 h-8" textClass="font-semibold text-xs" class="shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <p class="text-primary-900 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->role->label() }} &middot; {{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="py-6 text-sm text-gray-500">No sign-ups yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>
    </div>
