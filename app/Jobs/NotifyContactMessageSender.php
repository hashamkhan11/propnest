<?php

namespace App\Jobs;

use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyContactMessageSender
{
    use Dispatchable;

    public function __construct(public ContactMessage $contactMessage) {}

    public function handle(): void
    {
        Mail::to($this->contactMessage->email, $this->contactMessage->name)
            ->send(new ContactMessageReplyMail($this->contactMessage));
    }
}
