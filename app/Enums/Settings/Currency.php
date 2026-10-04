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

    public function format(float|string $amount): string
    {
        return $this->prefix().number_format((float) $amount, 2);
    }
}
