<?php

namespace App\Enums\Settings;

enum Currency: string
{
    case PKR = 'PKR';
    case USD = 'USD';
    case GBP = 'GBP';
    case EUR = 'EUR';
    case AED = 'AED';
    case SAR = 'SAR';
    case CAD = 'CAD';
    case AUD = 'AUD';

    public function prefix(): string
    {
        return match ($this) {
            self::USD => '$',
            self::GBP => '£',
            self::EUR => '€',
            self::CAD => 'C$',
            self::AUD => 'A$',
            self::PKR => 'PKR ',
            self::AED => 'AED ',
            self::SAR => 'SAR ',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PKR => 'Pakistani Rupee (PKR)',
            self::USD => 'US Dollar (USD)',
            self::GBP => 'British Pound (GBP)',
            self::EUR => 'Euro (EUR)',
            self::AED => 'UAE Dirham (AED)',
            self::SAR => 'Saudi Riyal (SAR)',
            self::CAD => 'Canadian Dollar (CAD)',
            self::AUD => 'Australian Dollar (AUD)',
        };
    }

    /**
     * Listing prices read better without cents ("$670,000"), while payments
     * keep them so receipts match the charged amount exactly.
     */
    public function format(float|string $amount, bool $whole = false): string
    {
        return $this->prefix().number_format((float) $amount, $whole ? 0 : 2);
    }
}
