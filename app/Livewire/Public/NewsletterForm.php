<?php

namespace App\Livewire\Public;

use App\Mail\NewsletterSubscribedMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class NewsletterForm extends Component
{
    /**
     * Sign-up attempts one visitor (IP) may make per hour. Each sign-up emails
     * the address given, so without a limit the form could be used to spam.
     */
    public const MAX_PER_HOUR = 5;

    public string $email = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|email|max:255|unique:subscribers,email',
        ];
    }

    public function subscribe(): void
    {
        $this->ensureIsNotRateLimited();

        RateLimiter::hit($this->throttleKey(), 3600);

        $validated = $this->validate();

        $subscriber = Subscriber::create($validated);

        Mail::to($subscriber->email)->send(new NewsletterSubscribedMail($subscriber));

        $this->reset('email');

        session()->flash('success', "You're subscribed!");
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_PER_HOUR)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Too many attempts. Please try again in '.ceil($seconds / 60).' minute(s).',
        ]);
    }

    protected function throttleKey(): string
    {
        return 'newsletter:'.request()->ip();
    }

    public function render()
    {
        return view('livewire.public.newsletter-form');
    }
}
