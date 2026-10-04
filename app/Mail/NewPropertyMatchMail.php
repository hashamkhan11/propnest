<?php

namespace App\Mail;

use App\Models\Property;
use App\Models\SavedSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPropertyMatchMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Property $property, public SavedSearch $savedSearch) {}

    public function build(): self
    {
        return $this->subject('New listing matches your saved search')
            ->markdown('emails.property-match');
    }
}
