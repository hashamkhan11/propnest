<?php

namespace App\Enums\RefundRequest;

use Illuminate\Support\Str;

enum RefundRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'info',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
