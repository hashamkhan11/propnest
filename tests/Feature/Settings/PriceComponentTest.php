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

    public function test_price_component_reflects_admin_configured_currency(): void
    {
        Settings::setCurrency(Currency::PKR);

        $view = $this->blade('<x-price :amount="$amount" />', ['amount' => 1500]);

        $view->assertSee('PKR 1,500.00');
    }
}
