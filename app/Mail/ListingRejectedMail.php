<?php

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListingRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Property $property, public string $reason) {}

    public function build(): self
    {
        return $this->subject('Your listing was rejected: '.$this->property->title)
            ->markdown('emails.listing-rejected');
    }
}
