<?php

namespace App\Jobs;

use App\Mail\NewInquiryMail;
use App\Models\Inquiry;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyAgentOfInquiry
{
    use Dispatchable;

    public function __construct(public Inquiry $inquiry) {}

    public function handle(): void
    {
        Mail::to($this->inquiry->agent)->send(new NewInquiryMail($this->inquiry));
    }
}
