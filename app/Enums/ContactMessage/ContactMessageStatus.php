<?php

namespace App\Enums\ContactMessage;

use Illuminate\Support\Str;

enum ContactMessageStatus: string
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Read => 'gray',
            self::Replied => 'success',
        };
    }
}
