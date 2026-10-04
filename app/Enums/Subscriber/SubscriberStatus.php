<?php

namespace App\Enums\Subscriber;

use Illuminate\Support\Str;

enum SubscriberStatus: string
{
    case Active = 'active';
    case Unsubscribed = 'unsubscribed';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Unsubscribed => 'gray',
        };
    }
}
