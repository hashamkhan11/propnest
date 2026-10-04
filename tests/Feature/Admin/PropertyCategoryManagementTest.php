<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\PropertyCategoryManagement;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PropertyCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_deactivate_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(PropertyCategoryManagement::class)
            ->set('name', 'Farmhouse')
            ->call('save');

        $category = PropertyCategory::where('slug', 'farmhouse')->firstOrFail();
        $this->assertTrue($category->is_active);

        Livewire::actingAs($admin)->test(PropertyCategoryManagement::class)
            ->call('toggleActive', $category->id);

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_cannot_delete_a_category_in_use(): void
    {
        $admin = User::factory()->admin()->create();
        $category = PropertyCategory::where('slug', 'house')->firstOrFail();
        Property::factory()->create(['category_id' => $category->id, 'property_type' => 'house']);

        Livewire::actingAs($admin)->test(PropertyCategoryManagement::class)
            ->call('delete', $category->id);

        $this->assertNotNull($category->fresh());
    }
}
