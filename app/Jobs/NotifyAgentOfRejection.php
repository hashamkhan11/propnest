<?php

namespace App\Jobs;

use App\Mail\ListingRejectedMail;
use App\Models\Property;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyAgentOfRejection
{
    use Dispatchable;

    public function __construct(public Property $property, public string $reason) {}

    public function handle(): void
    {
        Mail::to($this->property->agent)->send(new ListingRejectedMail($this->property, $this->reason));
    }
}
