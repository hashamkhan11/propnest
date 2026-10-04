<?php

namespace Tests\Feature\Settings;

use App\Enums\Settings\Currency;
use App\Livewire\Admin\SiteSettings;
use App\Models\User;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_settings_page(): void
    {
        $buyer = User::factory()->create();

        $this->actingAs($buyer)->get('/admin/settings')->assertForbidden();
    }

    public function test_admin_can_change_site_currency(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(SiteSettings::class)
            ->set('currency', 'AED')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(Currency::AED, Settings::currency());
    }
}
