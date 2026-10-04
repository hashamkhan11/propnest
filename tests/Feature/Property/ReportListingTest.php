<?php

namespace Tests\Feature\Property;

use App\Enums\Property\PropertyStatus;
use App\Livewire\Property\PropertyDetail;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_logged_in_user_can_report_a_listing(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create(['status' => PropertyStatus::Published]);

        Livewire::actingAs($buyer)->test(PropertyDetail::class, ['property' => $property])
            ->set('reportReason', 'spam')
            ->call('submitReport');

        $this->assertDatabaseHas('reports', [
            'property_id' => $property->id,
            'reported_by_user_id' => $buyer->id,
            'reason' => 'spam',
        ]);
    }

    public function test_cannot_submit_a_second_pending_report_for_the_same_listing(): void
    {
        $buyer = User::factory()->create();
        $property = Property::factory()->create(['status' => PropertyStatus::Published]);

        Livewire::actingAs($buyer)->test(PropertyDetail::class, ['property' => $property])
            ->set('reportReason', 'spam')
            ->call('submitReport');

        Livewire::actingAs($buyer)->test(PropertyDetail::class, ['property' => $property])
            ->set('reportReason', 'fraud')
            ->call('submitReport');

        $this->assertEquals(1, Report::where('property_id', $property->id)->count());
    }
}
