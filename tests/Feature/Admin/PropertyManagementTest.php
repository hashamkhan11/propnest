<?php

namespace Tests\Feature\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyStatus;
use App\Livewire\Admin\PropertyManagement;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_a_listing_with_no_payment_history(): void
    {
        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create();

        Livewire::actingAs($admin)->test(PropertyManagement::class)
            ->call('delete', $property->id);

        $this->assertNull($property->fresh());
    }

    public function test_admin_cannot_delete_a_listing_with_completed_payment(): void
    {
        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create();
        Payment::factory()->create(['property_id' => $property->id, 'status' => PaymentStatus::Completed]);

        Livewire::actingAs($admin)->test(PropertyManagement::class)
            ->call('delete', $property->id);

        $this->assertNotNull($property->fresh());
    }

    public function test_admin_can_filter_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        Property::factory()->create(['status' => PropertyStatus::Published, 'title' => 'Published One']);
        Property::factory()->create(['status' => PropertyStatus::Draft, 'title' => 'Draft One']);

        Livewire::actingAs($admin)->test(PropertyManagement::class)
            ->set('statusFilter', PropertyStatus::Published->value)
            ->assertSee('Published One')
            ->assertDontSee('Draft One');
    }
}
