<?php

namespace App\Models;

use App\Enums\Subscriber\SubscriberStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property SubscriberStatus $status
 */
#[Fillable(['email', 'status'])]
class Subscriber extends Model
{
    protected function casts(): array
    {
        return [
            'status' => SubscriberStatus::class,
        ];
    }
}
