<?php

namespace Tests\Feature\Public;

use App\Livewire\Public\NewsletterForm;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_email_subscribes(): void
    {
        Livewire::test(NewsletterForm::class)
            ->set('email', 'reader@example.com')
            ->call('subscribe')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('subscribers', ['email' => 'reader@example.com']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        Subscriber::create(['email' => 'reader@example.com']);

        Livewire::test(NewsletterForm::class)
            ->set('email', 'reader@example.com')
            ->call('subscribe')
            ->assertHasErrors(['email']);
    }

    public function test_sign_ups_are_rate_limited_per_visitor(): void
    {
        for ($i = 0; $i < NewsletterForm::MAX_PER_HOUR; $i++) {
            Livewire::test(NewsletterForm::class)
                ->set('email', "reader{$i}@example.com")
                ->call('subscribe')
                ->assertHasNoErrors();
        }

        Livewire::test(NewsletterForm::class)
            ->set('email', 'one-more@example.com')
            ->call('subscribe')
            ->assertHasErrors(['email']);

        $this->assertDatabaseMissing('subscribers', ['email' => 'one-more@example.com']);
    }
}
