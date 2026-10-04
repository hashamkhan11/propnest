<?php

namespace App\Enums\User;

use Illuminate\Support\Str;

enum UserRole: string
{
    case Buyer = 'buyer';
    case Agent = 'agent';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::SuperAdmin => 'primary',
            self::Admin => 'danger',
            self::Agent => 'success',
            self::Buyer => 'gray',
        };
    }
}
