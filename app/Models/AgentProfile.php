<?php

namespace App\Models;

use App\Enums\User\AgentVerificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
