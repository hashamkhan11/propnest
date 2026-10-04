<?php

namespace App\Livewire\Admin;

use App\Enums\User\UserRole;
use App\Enums\User\UserStatus;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'User Management'])]
class UserManagement extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $roleFilter = '';

    #[Url]
    public string $statusFilter = '';

    public ?int $suspendingUserId = null;

    public string $suspensionReason = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'roleFilter', 'statusFilter'], true)) {
            $this->resetPage();
        }
    }

    public function startSuspend(int $userId): void
    {
        $user = User::findOrFail($userId);

        if ($this->cannotManage($user)) {
            session()->flash('error', 'Only a Super Admin can suspend an Admin account.');

            return;
        }

        $this->suspendingUserId = $userId;
        $this->suspensionReason = '';
    }

    public function cancelSuspend(): void
    {
        $this->suspendingUserId = null;
        $this->suspensionReason = '';
    }

    public function suspend(User $user): void
    {
        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot suspend your own account.');
            $this->suspendingUserId = null;

            return;
        }

        if ($this->cannotManage($user)) {
            session()->flash('error', 'Only a Super Admin can suspend an Admin account.');
            $this->suspendingUserId = null;

            return;
        }

        $this->validate(['suspensionReason' => 'required|string|max:1000']);

        $user->update([
            'status' => UserStatus::Suspended,
            'suspended_at' => now(),
            'suspension_reason' => $this->suspensionReason,
        ]);

        $this->suspendingUserId = null;
        $this->suspensionReason = '';

        session()->flash('success', "{$user->name} has been suspended.");
    }

    public function unsuspend(User $user): void
    {
        if ($this->cannotManage($user)) {
            session()->flash('error', 'Only a Super Admin can reinstate an Admin account.');

            return;
        }

        $user->update([
            'status' => UserStatus::Active,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        session()->flash('success', "{$user->name} has been reinstated.");
    }

    public function promoteToSuperAdmin(User $user): void
    {
        if (auth()->user()->role !== UserRole::SuperAdmin || $user->role !== UserRole::Admin) {
            session()->flash('error', 'Only a Super Admin can promote an Admin to Super Admin.');

            return;
        }

        $user->update(['role' => UserRole::SuperAdmin]);

        session()->flash('success', "{$user->name} has been promoted to Super Admin.");
    }

    public function demoteToAdmin(User $user): void
    {
        if (auth()->user()->role !== UserRole::SuperAdmin || $user->role !== UserRole::SuperAdmin) {
            session()->flash('error', 'Only a Super Admin can demote a Super Admin to Admin.');

            return;
        }

        if (User::where('role', UserRole::SuperAdmin)->count() <= 1) {
            session()->flash('error', 'Cannot demote the last remaining Super Admin.');

            return;
        }

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot demote your own account.');

            return;
        }

        $user->update(['role' => UserRole::Admin]);

        session()->flash('success', "{$user->name} has been demoted to Admin.");
    }

    /**
     * Normal Admins may not act on Admin or Super Admin accounts (suspend/reinstate).
     * Only a Super Admin can manage another Admin or Super Admin account.
     */
    protected function cannotManage(User $user): bool
    {
        $targetIsAdminOrAbove = in_array($user->role, [UserRole::Admin, UserRole::SuperAdmin], true);

        return $targetIsAdminOrAbove && auth()->user()->role !== UserRole::SuperAdmin;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter !== '', fn ($query) => $query->where('role', $this->roleFilter))
            ->when(
                $this->statusFilter !== '',
                fn ($query) => $query->where('status', $this->statusFilter),
                fn ($query) => $query->where('status', UserStatus::Active),
            )
            ->latest()
            ->paginate(15);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }
}
