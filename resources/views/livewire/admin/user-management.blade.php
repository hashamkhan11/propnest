<div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
    <x-admin.page-header
        kicker="People"
        title="Users"
        icon="users"
        :subtitle="$users->total() . ' ' . \Illuminate\Support\Str::plural('user', $users->total())"
    >
        @if (auth()->user()->role === \App\Enums\User\UserRole::SuperAdmin)
            <x-slot name="actions">
                <a href="{{ route('admin.create-admin') }}" wire:navigate>
                    <x-button>Add an admin</x-button>
                </a>
            </x-slot>
        @endif
    </x-admin.page-header>

    <x-card class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <x-input wire:model.live.debounce.400ms="search" type="text" placeholder="Search name or email…" class="sm:max-w-xs" />

            <select wire:model.live="roleFilter" class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md">
                <option value="">All roles</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}">{{ $role->label() }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
    </x-card>

    @if ($users->isEmpty())
        <x-empty-state title="No users found" description="Try adjusting your search or filters.">
            <x-slot name="icon">
                <x-heroicon-o-users class="w-7 h-7" />
            </x-slot>
        </x-empty-state>
    @else
        <x-admin.data-table :headers="['Name', 'Email', 'Role', 'Status', 'Joined', 'Actions']">
            @foreach ($users as $user)
                <tr wire:key="user-row-{{ $user->id }}" class="border-b border-gray-50 last:border-0 hover:bg-gray-50/60 transition-colors align-top">
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ $user->name }}</td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="py-3 px-4">
                        <x-badge :variant="$user->role->badgeVariant()">{{ $user->role->label() }}</x-badge>
                    </td>
                    <td class="py-3 px-4">
                        <x-badge :variant="$user->status->badgeVariant()">{{ $user->status->label() }}</x-badge>
                        @if ($user->status === \App\Enums\User\UserStatus::Suspended && $user->suspension_reason)
                            <p class="text-xs text-gray-500 mt-1 max-w-[200px]">{{ $user->suspension_reason }}</p>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="py-3 px-4 text-right">
                        @php
                            $isSuperAdmin = auth()->user()->role === \App\Enums\User\UserRole::SuperAdmin;
                            $targetIsSuperAdmin = $user->role === \App\Enums\User\UserRole::SuperAdmin;
                            $targetIsAdminOrAbove = in_array($user->role, [\App\Enums\User\UserRole::Admin, \App\Enums\User\UserRole::SuperAdmin], true);
                            $canManageStatus = $isSuperAdmin || ! $targetIsAdminOrAbove;
                        @endphp
                        <div class="flex flex-col items-end gap-2">
                            @if ($canManageStatus)
                                @if ($user->status === \App\Enums\User\UserStatus::Suspended)
                                    <x-button variant="secondary" wire:click="unsuspend({{ $user->id }})">Reinstate</x-button>
                                @elseif ($suspendingUserId === $user->id)
                                    <div class="space-y-2 text-left">
                                        <textarea wire:model="suspensionReason" rows="2" placeholder="Reason (required)"
                                                  class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md w-full text-sm"></textarea>
                                        <x-input-error :messages="$errors->get('suspensionReason')" />
                                        <div class="flex gap-2">
                                            <x-button wire:click="suspend({{ $user->id }})">Confirm Suspend</x-button>
                                            <x-button variant="secondary" wire:click="cancelSuspend">Cancel</x-button>
                                        </div>
                                    </div>
                                @elseif ($user->id !== auth()->id())
                                    <x-button variant="secondary" wire:click="startSuspend({{ $user->id }})">Suspend</x-button>
                                @endif
                            @endif

                            @if ($isSuperAdmin && $user->id !== auth()->id())
                                @if ($user->role === \App\Enums\User\UserRole::Admin)
                                    <x-button variant="secondary" wire:click="promoteToSuperAdmin({{ $user->id }})">Promote to Super Admin</x-button>
                                @elseif ($targetIsSuperAdmin)
                                    <x-button variant="secondary" wire:click="demoteToAdmin({{ $user->id }})">Demote to Admin</x-button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-admin.data-table>

        {{-- Mobile card list --}}
        <div class="md:hidden space-y-3">
            @foreach ($users as $user)
                <div wire:key="user-card-{{ $user->id }}" class="bg-white rounded-xl border border-gray-900/10 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <x-badge :variant="$user->role->badgeVariant()" class="shrink-0">{{ $user->role->label() }}</x-badge>
                    </div>

                    <div class="mt-3 flex items-center justify-between text-sm">
                        <x-badge :variant="$user->status->badgeVariant()">{{ $user->status->label() }}</x-badge>
                        <span class="text-gray-500">Joined {{ $user->created_at->format('M j, Y') }}</span>
                    </div>

                    @if ($user->status === \App\Enums\User\UserStatus::Suspended && $user->suspension_reason)
                        <p class="text-xs text-gray-500 mt-2">{{ $user->suspension_reason }}</p>
                    @endif

                    <div class="mt-4 pt-3 border-t border-gray-900/10 space-y-2">
                        @php
                            $isSuperAdmin = auth()->user()->role === \App\Enums\User\UserRole::SuperAdmin;
                            $targetIsSuperAdmin = $user->role === \App\Enums\User\UserRole::SuperAdmin;
                            $targetIsAdminOrAbove = in_array($user->role, [\App\Enums\User\UserRole::Admin, \App\Enums\User\UserRole::SuperAdmin], true);
                            $canManageStatus = $isSuperAdmin || ! $targetIsAdminOrAbove;
                        @endphp
                        @if ($canManageStatus)
                            @if ($user->status === \App\Enums\User\UserStatus::Suspended)
                                <x-button variant="secondary" wire:click="unsuspend({{ $user->id }})" class="w-full justify-center">Reinstate</x-button>
                            @elseif ($suspendingUserId === $user->id)
                                <div class="space-y-2">
                                    <textarea wire:model="suspensionReason" rows="2" placeholder="Reason (required)"
                                              class="border-gray-300 focus:border-primary-900 focus:ring-primary-900 rounded-md w-full text-sm"></textarea>
                                    <x-input-error :messages="$errors->get('suspensionReason')" />
                                    <div class="flex gap-2">
                                        <x-button wire:click="suspend({{ $user->id }})" class="flex-1 justify-center">Confirm</x-button>
                                        <x-button variant="secondary" wire:click="cancelSuspend" class="flex-1 justify-center">Cancel</x-button>
                                    </div>
                                </div>
                            @elseif ($user->id !== auth()->id())
                                <x-button variant="secondary" wire:click="startSuspend({{ $user->id }})" class="w-full justify-center">Suspend</x-button>
                            @endif
                        @endif

                        @if ($isSuperAdmin && $user->id !== auth()->id())
                            @if ($user->role === \App\Enums\User\UserRole::Admin)
                                <x-button variant="secondary" wire:click="promoteToSuperAdmin({{ $user->id }})" class="w-full justify-center">Promote to Super Admin</x-button>
                            @elseif ($targetIsSuperAdmin)
                                <x-button variant="secondary" wire:click="demoteToAdmin({{ $user->id }})" class="w-full justify-center">Demote to Admin</x-button>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</div>
