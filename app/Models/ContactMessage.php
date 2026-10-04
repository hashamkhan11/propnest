<?php

namespace App\Models;

use App\Enums\ContactMessage\ContactMessageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property ContactMessageStatus $status
 * @property string|null $reply
 * @property Carbon|null $replied_at
 * @property int|null $replied_by_user_id
 * @property-read User|null $repliedBy
 */
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

    /** @return BelongsTo<User, $this> */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by_user_id');
    }
}
