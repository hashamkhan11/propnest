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

    public function test_monthly_revenue_is_in_dollars_and_covers_every_month(): void
    {
        $this->travelTo(now()->setDate(2026, 6, 15));
        $admin = User::factory()->admin()->create(['created_at' => now()->subYears(2)]);
        Payment::factory()->create(['status' => PaymentStatus::Completed, 'amount' => 4900, 'created_at' => now()->subMonths(2)]);
        Payment::factory()->create(['status' => PaymentStatus::Completed, 'amount' => 2900, 'created_at' => now()->subMonths(2)]);

        $revenue = Livewire::actingAs($admin)->test(Analytics::class)->viewData('revenueByMonth');

        $this->assertCount(12, $revenue);
        $this->assertSame('Jul 2025', $revenue->keys()->first());
        $this->assertSame('Jun 2026', $revenue->keys()->last());
        $this->assertEquals(78, $revenue['Apr 2026']);
        $this->assertEquals(0, $revenue['May 2026']);
    }
}
