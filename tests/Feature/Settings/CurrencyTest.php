<?php

namespace Tests\Feature\Settings;

use App\Enums\Settings\Currency;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_currency_is_usd(): void
    {
        $this->assertSame(Currency::USD, Settings::currency());
    }

    public function test_set_currency_persists_and_invalidates_cache(): void
    {
        Settings::setCurrency(Currency::AED);

        $this->assertSame(Currency::AED, Settings::currency());
        $this->assertDatabaseHas('site_settings', ['currency' => 'AED']);
    }

    public function test_format_uses_symbol_currencies_correctly(): void
    {
        $this->assertSame('$1,234.50', Currency::USD->format(1234.5));
        $this->assertSame('£1,234.50', Currency::GBP->format(1234.5));
        $this->assertSame('€1,234.50', Currency::EUR->format(1234.5));
        $this->assertSame('C$1,234.50', Currency::CAD->format(1234.5));
        $this->assertSame('A$1,234.50', Currency::AUD->format(1234.5));
    }

    public function test_format_uses_code_prefix_for_currencies_without_common_symbols(): void
    {
        $this->assertSame('PKR 1,234.50', Currency::PKR->format(1234.5));
        $this->assertSame('AED 1,234.50', Currency::AED->format(1234.5));
        $this->assertSame('SAR 1,234.50', Currency::SAR->format(1234.5));
    }
}
