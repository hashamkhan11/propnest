<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_email_subscribes(): void
    {
        Livewire::test(\App\Livewire\Public\NewsletterForm::class)
            ->set('email', 'reader@example.com')
            ->call('subscribe')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('subscribers', ['email' => 'reader@example.com']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        \App\Models\Subscriber::create(['email' => 'reader@example.com']);

        Livewire::test(\App\Livewire\Public\NewsletterForm::class)
            ->set('email', 'reader@example.com')
            ->call('subscribe')
            ->assertHasErrors(['email']);
    }
}
