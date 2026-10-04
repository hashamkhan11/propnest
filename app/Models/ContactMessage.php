<?php

namespace App\Models;

use App\Enums\ContactMessage\ContactMessageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'email', 'subject', 'message', 'status', 'reply', 'replied_at', 'replied_by_user_id'])]
class ContactMessage extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
            'replied_at' => 'datetime',
        ];
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by_user_id');
    }
}
