<?php

namespace Tests\Feature\Admin;

use App\Enums\User\UserStatus;
use App\Livewire\Admin\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_users_by_name_or_email(): void
    {
        $admin = User::factory()->admin()->create();
        $match = User::factory()->create(['name' => 'Jane Buyer', 'email' => 'jane@example.com']);
        User::factory()->create(['name' => 'Other Person', 'email' => 'other@example.com']);

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->set('search', 'jane')
            ->assertSee('Jane Buyer')
            ->assertDontSee('Other Person');
    }

    public function test_admin_can_suspend_and_reinstate_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->create();

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('startSuspend', $buyer->id)
            ->set('suspensionReason', 'Repeated spam reports.')
            ->call('suspend', $buyer->id);

        $buyer->refresh();
        $this->assertEquals(UserStatus::Suspended, $buyer->status);
        $this->assertNotNull($buyer->suspended_at);
        $this->assertEquals('Repeated spam reports.', $buyer->suspension_reason);

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('unsuspend', $buyer->id);

        $this->assertEquals(UserStatus::Active, $buyer->refresh()->status);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('suspend', $admin->id);

        $this->assertEquals(UserStatus::Active, $admin->refresh()->status);
    }

    public function test_normal_admin_cannot_suspend_a_super_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('startSuspend', $superAdmin->id)
            ->set('suspensionReason', 'Trying to suspend a super admin.')
            ->call('suspend', $superAdmin->id);

        $this->assertEquals(UserStatus::Active, $superAdmin->refresh()->status);
    }

    public function test_normal_admin_cannot_suspend_another_normal_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('startSuspend', $otherAdmin->id)
            ->set('suspensionReason', 'Trying to suspend a fellow admin.')
            ->call('suspend', $otherAdmin->id);

        $this->assertEquals(UserStatus::Active, $otherAdmin->refresh()->status);
    }

    public function test_super_admin_can_suspend_and_reinstate_a_normal_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($superAdmin)->test(UserManagement::class)
            ->call('startSuspend', $admin->id)
            ->set('suspensionReason', 'Policy violation.')
            ->call('suspend', $admin->id);

        $this->assertEquals(UserStatus::Suspended, $admin->refresh()->status);

        Livewire::actingAs($superAdmin)->test(UserManagement::class)
            ->call('unsuspend', $admin->id);

        $this->assertEquals(UserStatus::Active, $admin->refresh()->status);
    }

    public function test_super_admin_can_promote_an_admin_to_super_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($superAdmin)->test(UserManagement::class)
            ->call('promoteToSuperAdmin', $admin->id);

        $this->assertEquals(\App\Enums\User\UserRole::SuperAdmin, $admin->refresh()->role);
    }

    public function test_normal_admin_cannot_promote_another_admin_to_super_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('promoteToSuperAdmin', $otherAdmin->id);

        $this->assertEquals(\App\Enums\User\UserRole::Admin, $otherAdmin->refresh()->role);
    }

    public function test_normal_admin_cannot_demote_a_super_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $superAdmin = User::factory()->superAdmin()->create();

        Livewire::actingAs($admin)->test(UserManagement::class)
            ->call('demoteToAdmin', $superAdmin->id);

        $this->assertEquals(\App\Enums\User\UserRole::SuperAdmin, $superAdmin->refresh()->role);
    }

    public function test_super_admin_can_demote_another_super_admin_to_admin(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $otherSuperAdmin = User::factory()->superAdmin()->create();

        Livewire::actingAs($superAdmin)->test(UserManagement::class)
            ->call('demoteToAdmin', $otherSuperAdmin->id);

        $this->assertEquals(\App\Enums\User\UserRole::Admin, $otherSuperAdmin->refresh()->role);
    }

    public function test_super_admin_cannot_demote_themselves(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        User::factory()->superAdmin()->create();

        Livewire::actingAs($superAdmin)->test(UserManagement::class)
            ->call('demoteToAdmin', $superAdmin->id);

        $this->assertEquals(\App\Enums\User\UserRole::SuperAdmin, $superAdmin->refresh()->role);
    }

    public function test_last_remaining_super_admin_cannot_be_demoted(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        Livewire::actingAs($superAdmin)->test(UserManagement::class)
            ->call('demoteToAdmin', $superAdmin->id);

        $this->assertEquals(\App\Enums\User\UserRole::SuperAdmin, $superAdmin->refresh()->role);
    }
}
