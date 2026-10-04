<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\FeaturedTierManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FeaturedTierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_new_tier(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(FeaturedTierManagement::class)
            ->set('name', 'Premium')
            ->set('durationDays', '60')
            ->set('priceDollars', '49.00')
            ->call('save');

        $this->assertDatabaseHas('featured_pricing_tiers', [
            'name' => 'Premium',
            'duration_days' => 60,
            'price_cents' => 4900,
        ]);
    }
}
