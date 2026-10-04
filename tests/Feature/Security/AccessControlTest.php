<?php

namespace Tests\Feature\Security;

use App\Enums\User\UserStatus;
use App\Http\Middleware\LogoutSuspendedUsers;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspended_user_is_signed_out_on_next_request(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')->assertOk();

        $user->update(['status' => UserStatus::Suspended]);

        $this->get('/profile')
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', LogoutSuspendedUsers::MESSAGE);

        $this->assertGuest();
    }

    public function test_suspended_user_gets_403_on_livewire_requests(): void
    {
        $user = User::factory()->create(['status' => UserStatus::Suspended]);

        $this->actingAs($user)
            ->withHeaders(['X-Livewire' => '1'])
            ->postJson('/livewire/update', ['components' => []])
            ->assertForbidden();

        $this->assertGuest();
    }

    public function test_livewire_actions_on_admin_components_enforce_the_role_check(): void
    {
        $admin = User::factory()->admin()->create();
        $buyer = User::factory()->create();

        $snapshot = $this->snapshotFor(
            $this->actingAs($admin)->get(route('admin.properties.index'))->assertOk(),
            'admin.property-management',
        );

        // The admin can still use the component...
        $this->callRefresh($snapshot)->assertOk();

        // ...but a buyer replaying the same snapshot is refused.
        $this->actingAs($buyer);
        $this->callRefresh($snapshot)->assertForbidden();
    }

    public function test_super_admin_can_edit_any_listing(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $property = Property::factory()->create();

        $this->actingAs($superAdmin)
            ->get(route('admin.properties.edit', $property))
            ->assertOk();
    }

    private function snapshotFor(TestResponse $response, string $component): string
    {
        preg_match_all('/wire:snapshot="([^"]+)"/', $response->getContent(), $matches);

        foreach ($matches[1] as $encoded) {
            $snapshot = html_entity_decode($encoded, ENT_QUOTES);

            if (json_decode($snapshot, true)['memo']['name'] === $component) {
                return $snapshot;
            }
        }

        $this->fail("No snapshot for {$component} on the page.");
    }

    private function callRefresh(string $snapshot): TestResponse
    {
        return $this->withHeaders(['X-Livewire' => '1'])->postJson('/livewire/update', [
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => [],
                'calls' => [],
            ]],
        ]);
    }
}
