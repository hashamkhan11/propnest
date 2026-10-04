<?php

namespace Tests\Feature\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Livewire\Admin\Analytics;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_analytics_page(): void
    {
        $admin = User::factory()->admin()->create();
        Payment::factory()->create(['status' => PaymentStatus::Completed, 'amount' => 5000]);

        Livewire::actingAs($admin)->test(Analytics::class)
            ->assertOk()
            ->assertSee('Revenue by Month');
    }
}
