<?php

namespace App\Enums\Property;

enum PropertyPurpose: string
{
    case ForSale = 'for_sale';
    case ForRent = 'for_rent';

    public function label(): string
    {
        return match ($this) {
            self::ForSale => 'For Sale',
            self::ForRent => 'For Rent',
        };
    }
}
