<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-admin.page-header
        title="Agent Verification"
        icon="identification"
        :subtitle="$agents->count() . ' ' . \Illuminate\Support\Str::plural('agent', $agents->count())"
    />

    @if ($agents->isEmpty())
        <x-empty-state title="No agents yet" description="Agent accounts will appear here once they register.">
            <x-slot name="icon">
                <x-heroicon-o-identification class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <div class="space-y-3">
            @foreach ($agents as $i => $agent)
                <div wire:key="agent-{{ $agent->id }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-fade-in-up" style="animation-delay: {{ min($i, 8) * 40 }}ms">
                    <div class="min-w-0">
                        <p class="font-heading font-700 text-gray-900">{{ $agent->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $agent->email }} &middot; {{ $agent->agentProfile->agency_name ?? 'No agency name set' }}</p>
                        <x-badge :variant="$agent->agentProfile->verification_status->badgeVariant()" class="mt-2">
                            {{ $agent->agentProfile->verification_status->label() }}
                        </x-badge>
                    </div>

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
