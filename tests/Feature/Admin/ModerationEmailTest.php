<?php

namespace Tests\Feature\Admin;

use App\Enums\User\UserRole;
use App\Livewire\Admin\ModerationQueue;
use App\Mail\ListingPulledForReviewMail;
use App\Mail\ListingRejectedMail;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ModerationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejecting_a_listing_notifies_the_agent(): void
    {
        Mail::fake();

        $agent = User::factory()->agent()->create();
        $property = Property::factory()->create(['agent_id' => $agent->id]);
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)->test(ModerationQueue::class)
            ->set('rejectionReason', 'Missing photos')
            ->call('reject', $property->id);

        Mail::assertSent(ListingRejectedMail::class, function ($mail) use ($agent) {
            return $mail->hasTo($agent->email);
        });
    }

    public function test_pulling_a_listing_for_review_notifies_the_agent(): void
    {
        Mail::fake();

        $agent = User::factory()->agent()->create();
        $property = Property::factory()->create(['agent_id' => $agent->id]);
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)->test(ModerationQueue::class)
            ->call('pullForReview', $property->id);

        Mail::assertSent(ListingPulledForReviewMail::class, function ($mail) use ($agent) {
            return $mail->hasTo($agent->email);
        });
    }
}
