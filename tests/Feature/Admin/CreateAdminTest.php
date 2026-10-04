<?php

namespace Tests\Feature\Admin;

use App\Enums\User\UserRole;
use App\Livewire\Admin\CreateAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_an_admin_account(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        Livewire::actingAs($superAdmin)->test(CreateAdmin::class)
            ->set('name', 'New Admin')
            ->set('email', 'new-admin@example.com')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('save');

        $created = User::where('email', 'new-admin@example.com')->first();
        $this->assertNotNull($created);
        $this->assertEquals(UserRole::Admin, $created->role);
    }

    public function test_normal_admin_cannot_reach_the_create_admin_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.create-admin'))->assertForbidden();
    }

    public function test_normal_admin_cannot_invoke_the_create_admin_component_directly(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(CreateAdmin::class)
            ->assertForbidden();
    }
}
