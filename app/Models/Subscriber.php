<?php

namespace App\Models;

use App\Enums\Subscriber\SubscriberStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
