<?php

namespace App\Enums\Subscriber;

enum SubscriberStatus: string
{
    case Active = 'active';
    case Unsubscribed = 'unsubscribed';

    public function label(): string
    {
        return \Illuminate\Support\Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Unsubscribed => 'gray',
        };
    }
}
