<?php

namespace App\Enums\Subscription;

use Illuminate\Support\Str;

enum AgentSubscriptionStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Processing',
            default => Str::headline($this->value),
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Pending => 'info',
            self::Failed => 'danger',
            self::Cancelled, self::Expired => 'gray',
        };
    }
}
