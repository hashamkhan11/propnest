<div class="max-w-5xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="People"
        title="Agent checks"
        icon="identification"
        :subtitle="$agents->count() . ' ' . \Illuminate\Support\Str::plural('agent', $agents->count()) . '. Verified agents get a badge on every listing, so only verify people you have checked.'"
    />

    @if ($agents->isEmpty())
        <x-empty-state title="No agents yet" description="Agent accounts will appear here once they register.">
            <x-slot name="icon">
                <x-heroicon-o-identification class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <div class="bg-white rounded-xl border border-gray-900/10 divide-y divide-gray-900/10">
            @foreach ($agents as $agent)
                <div wire:key="agent-{{ $agent->id }}" class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-4">
                    <x-user-avatar :user="$agent" size="w-10 h-10" textClass="font-semibold text-sm" class="shrink-0 hidden sm:flex" />
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-primary-900">{{ $agent->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $agent->email }} &middot; {{ $agent->agentProfile->agency_name ?? 'No agency name set' }}</p>
                    </div>
                    <x-badge :variant="$agent->agentProfile->verification_status->badgeVariant()" class="shrink-0 self-start sm:self-center">
                        {{ $agent->agentProfile->verification_status->label() }}
                    </x-badge>

                    @if ($agent->agentProfile->verification_status !== \App\Enums\User\AgentVerificationStatus::Verified)
                        <x-button
                            variant="secondary"
                            class="shrink-0"
                            x-on:click="$store.confirmDialog.open({
                                title: 'Verify this agent?',
                                message: 'Mark {{ $agent->name }} as a verified agent. They will be notified by email.',
                                confirmText: 'Verify',
                                onConfirm: () => $wire.verify({{ $agent->id }}),
                            })"
                        >
                            Verify
                        </x-button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
