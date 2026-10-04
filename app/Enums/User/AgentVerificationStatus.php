<?php

namespace App\Enums\User;

use Illuminate\Support\Str;

enum AgentVerificationStatus: string
{
    case Unverified = 'unverified';
    case Pending = 'pending';
    case Verified = 'verified';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Verified => 'success',
            self::Pending => 'info',
            self::Unverified => 'gray',
        };
    }
}
