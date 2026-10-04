<?php

namespace Tests\Feature\Buyer;

use App\Enums\User\UserRole;
use App\Livewire\Buyer\InquiryForm;
use App\Mail\NewInquiryMail;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_sending_an_inquiry_notifies_the_listing_agent(): void
    {
        Mail::fake();

        $agent = User::factory()->agent()->create();
        $property = Property::factory()->create(['agent_id' => $agent->id]);
        $buyer = User::factory()->create(['role' => UserRole::Buyer]);

        Livewire::actingAs($buyer)->test(InquiryForm::class, ['property' => $property])
            ->set('message', 'Is this still available?')
            ->call('send')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('inquiries', [
            'property_id' => $property->id,
            'buyer_id' => $buyer->id,
            'agent_id' => $agent->id,
        ]);

        Mail::assertSent(NewInquiryMail::class, function ($mail) use ($agent) {
            return $mail->hasTo($agent->email);
        });
    }
}
