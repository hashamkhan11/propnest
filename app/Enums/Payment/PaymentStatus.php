<?php

namespace App\Enums\Payment;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Processing',
            default => \Illuminate\Support\Str::headline($this->value),
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Pending => 'info',
            self::Failed => 'danger',
            self::Cancelled, self::Expired => 'gray',
            self::Refunded => 'accent',
        };
    }
}
