<?php

namespace App\Models;

use App\Enums\User\AgentVerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $agency_name
 * @property string|null $phone
 * @property AgentVerificationStatus $verification_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $bio
 * @property-read User $user
 */
#[Fillable(['user_id', 'agency_name', 'phone', 'verification_status', 'bio'])]
class AgentProfile extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'verification_status' => AgentVerificationStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
