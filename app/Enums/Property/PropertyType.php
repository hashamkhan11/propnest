<?php

namespace App\Enums\Property;

enum PropertyType: string
{
    case House = 'house';
    case Apartment = 'apartment';
    case Condo = 'condo';
    case Townhouse = 'townhouse';
    case Land = 'land';
    case Commercial = 'commercial';

    /**
     * Fallback for any admin-created PropertyCategory whose slug doesn't
     * match one of the original fixed cases above — this legacy string
     * column is kept in sync with category_id, which is the real source of
     * truth for anything beyond these original six.
     */
    case Other = 'other';
}
