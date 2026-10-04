<?php

namespace App\Enums\User;

use Illuminate\Support\Str;

enum UserStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Suspended => 'danger',
        };
    }
}
