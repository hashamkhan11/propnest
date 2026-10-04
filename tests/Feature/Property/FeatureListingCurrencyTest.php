<?php

namespace Tests\Feature\Property;

use App\Enums\Settings\Currency;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureListingCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_stripe_currency_param_matches_site_setting(): void
    {
        Settings::setCurrency(Currency::AED);

        $this->assertSame('aed', strtolower(Settings::currency()->value));
    }
}
