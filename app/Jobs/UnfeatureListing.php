<?php

namespace App\Jobs;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnfeatureListing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Property $property) {}

    public function handle(): void
    {
        $property = $this->property->fresh();

        if ($property === null) {
            return;
        }

        if ($property->is_featured && $property->featured_until !== null && $property->featured_until->lessThanOrEqualTo(now())) {
            $property->update(['is_featured' => false]);
        }
    }
}
