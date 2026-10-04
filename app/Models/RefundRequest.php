<?php

namespace App\Models;

use App\Enums\RefundRequest\RefundRequestStatus;
use Database\Factories\RefundRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $payment_id
 * @property int $property_id
 * @property int $agent_id
 * @property string|null $reason
 * @property RefundRequestStatus $status
 * @property string|null $admin_notes
 * @property int|null $reviewed_by_user_id
 * @property Carbon|null $reviewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $refund_amount_cents
 * @property-read User $agent
 * @property-read Payment $payment
 * @property-read Property $property
 * @property-read User|null $reviewedBy
 *
 * @method static \Database\Factories\RefundRequestFactory factory($count = null, $state = [])
 */
#[Fillable(['payment_id', 'property_id', 'agent_id', 'reason', 'refund_amount_cents', 'status', 'admin_notes', 'reviewed_by_user_id', 'reviewed_at'])]
class RefundRequest extends Model
{
    /** @use HasFactory<RefundRequestFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => RefundRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Payment, $this> */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /** @return BelongsTo<Property, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /** @return BelongsTo<User, $this> */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /** @return BelongsTo<User, $this> */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
