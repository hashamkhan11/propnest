<?php

namespace App\Livewire\Public;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ContactForm extends Component
{
    /** Messages one visitor (IP) may send per hour. */
    public const MAX_PER_HOUR = 3;

    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ];
    }

    public function send(): void
    {
        $this->ensureIsNotRateLimited();

        $validated = $this->validate();

        $contactMessage = ContactMessage::create($validated);

        RateLimiter::hit($this->throttleKey(), 3600);

        Mail::to(config('mail.from.address'))->send(new ContactMessageReceived($contactMessage));

        $this->reset(['name', 'email', 'subject', 'message']);

        session()->flash('success', "Thanks for reaching out! We'll get back to you soon.");
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_PER_HOUR)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'message' => 'Too many messages sent. Please try again in '.ceil($seconds / 60).' minute(s).',
        ]);
    }

    protected function throttleKey(): string
    {
        return 'contact:'.request()->ip();
    }

    public function render()
    {
        return view('livewire.public.contact-form');
    }
}
