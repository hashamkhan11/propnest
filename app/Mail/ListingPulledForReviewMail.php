<?php

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingPulledForReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Property $property) {}

    public function build(): self
    {
        return $this->subject('Your listing is under review: '.$this->property->title)
            ->markdown('emails.listing-pulled-for-review');
    }
}
