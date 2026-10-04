<?php

namespace App\Jobs;

use App\Mail\PaymentRefundedMail;
use App\Models\Payment;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyAgentOfRefund
{
    use Dispatchable;

    public function __construct(public Payment $payment) {}

    public function handle(): void
    {
        Mail::to($this->payment->agent)->send(new PaymentRefundedMail($this->payment));
    }
}
