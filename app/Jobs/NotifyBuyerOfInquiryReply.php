<?php

namespace App\Jobs;

use App\Mail\InquiryReplyMail;
use App\Models\Inquiry;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyBuyerOfInquiryReply
{
    use Dispatchable;

    public function __construct(public Inquiry $inquiry) {}

    public function handle(): void
    {
        Mail::to($this->inquiry->buyer)->send(new InquiryReplyMail($this->inquiry));
    }
}
