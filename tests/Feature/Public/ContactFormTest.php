<?php

namespace Tests\Feature\Public;

use App\Livewire\Public\ContactForm;
use App\Mail\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_submission_creates_message_and_sends_mail(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Jane Buyer')
            ->set('email', 'jane@example.com')
            ->set('subject', 'Question about a listing')
            ->set('message', 'Is this property still available?')
            ->call('send')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Jane Buyer',
            'email' => 'jane@example.com',
        ]);

        Mail::assertSent(ContactMessageReceived::class);
    }

    public function test_invalid_email_is_rejected(): void
    {
        Mail::fake();

        Livewire::test(ContactForm::class)
            ->set('name', 'Jane Buyer')
            ->set('email', 'not-an-email')
            ->set('subject', 'Question')
            ->set('message', 'Hello')
            ->call('send')
            ->assertHasErrors(['email']);

        Mail::assertNotSent(ContactMessageReceived::class);
    }

    public function test_submissions_are_rate_limited_per_visitor(): void
    {
        Mail::fake();

        for ($i = 0; $i < ContactForm::MAX_PER_HOUR; $i++) {
            $this->sendContactMessage()->assertHasNoErrors();
        }

        $this->sendContactMessage()->assertHasErrors(['message']);

        $this->assertDatabaseCount('contact_messages', ContactForm::MAX_PER_HOUR);
        Mail::assertSentCount(ContactForm::MAX_PER_HOUR);
    }

    private function sendContactMessage(): Testable
    {
        return Livewire::test(ContactForm::class)
            ->set('name', 'Jane Buyer')
            ->set('email', 'jane@example.com')
            ->set('subject', 'Question')
            ->set('message', 'Hello there')
            ->call('send');
    }
}
