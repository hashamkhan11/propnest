<?php

namespace Tests\Feature\Admin;

use App\Enums\User\AgentVerificationStatus;
use App\Livewire\Admin\AgentVerification;
use App\Mail\AgentVerifiedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AgentVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verifying_an_agent_sends_notification_email(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();

        Livewire::actingAs($admin)->test(AgentVerification::class)
            ->call('verify', $agent->id);

        $this->assertEquals(
            AgentVerificationStatus::Verified,
            $agent->agentProfile->fresh()->verification_status
        );

        Mail::assertSent(AgentVerifiedMail::class, function ($mail) use ($agent) {
            return $mail->hasTo($agent->email);
        });
    }
}
