<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgentVerifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $agent) {}

    public function build(): self
    {
        return $this->subject('Your agent account is verified')
            ->markdown('emails.agent-verified');
    }
}
