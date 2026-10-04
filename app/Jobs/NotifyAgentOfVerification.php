<?php

namespace App\Jobs;

use App\Mail\AgentVerifiedMail;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NotifyAgentOfVerification
{
    use Dispatchable;

    public function __construct(public User $agent) {}

    public function handle(): void
    {
        Mail::to($this->agent)->send(new AgentVerifiedMail($this->agent));
    }
}
