<?php

namespace Tests\Feature\Admin;

use App\Enums\Property\PropertyStatus;
use App\Enums\Report\ReportStatus;
use App\Livewire\Admin\ReportsQueue;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_dismiss_a_report(): void
    {
        $admin = User::factory()->admin()->create();
        $report = Report::factory()->create();

        Livewire::actingAs($admin)->test(ReportsQueue::class)
            ->call('dismiss', $report->id);

        $this->assertEquals(ReportStatus::Dismissed, $report->fresh()->status);
    }

    public function test_admin_can_resolve_a_report_and_reject_the_listing(): void
    {
        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['status' => PropertyStatus::Published]);
        $report = Report::factory()->create(['property_id' => $property->id]);

        Livewire::actingAs($admin)->test(ReportsQueue::class)
            ->call('startResolve', $report->id)
            ->set('alsoReject', true)
            ->set('rejectionReason', 'Violates listing policy.')
            ->call('resolve', $report->id);

        $this->assertEquals(ReportStatus::ActionTaken, $report->fresh()->status);
        $this->assertEquals(PropertyStatus::Rejected, $property->fresh()->status);
    }
}
