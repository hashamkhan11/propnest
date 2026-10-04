<?php

namespace App\Jobs;

use App\Mail\ListingPulledForReviewMail;
use App\Models\Property;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyAgentOfPulledForReview
{
    use Dispatchable;

    public function __construct(public Property $property) {}

    public function handle(): void
    {
        Mail::to($this->property->agent)->send(new ListingPulledForReviewMail($this->property));
    }
}
