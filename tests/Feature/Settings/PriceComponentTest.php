<?php

namespace Tests\Feature\Settings;

use App\Enums\Settings\Currency;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_component_renders_default_currency(): void
    {
        $view = $this->blade('<x-price :amount="$amount" />', ['amount' => 1500]);

        $view->assertSee('$1,500.00');
    }

    public function test_price_component_drops_cents_for_whole_listing_prices(): void
    {
        $view = $this->blade('<x-price whole :amount="$amount" />', ['amount' => 670000]);

        $view->assertSee('$670,000');
        $view->assertDontSee('$670,000.00');
    }

    public function test_price_component_reflects_admin_configured_currency(): void
    {
        Settings::setCurrency(Currency::PKR);

        $view = $this->blade('<x-price :amount="$amount" />', ['amount' => 1500]);

        $view->assertSee('PKR 1,500.00');
    }
}
