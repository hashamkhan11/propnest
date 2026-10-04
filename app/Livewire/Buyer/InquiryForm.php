<?php

namespace App\Livewire\Buyer;

use App\Enums\User\UserRole;
use App\Jobs\NotifyAgentOfInquiry;
use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class InquiryForm extends Component
{
    public Property $property;

    public string $message = '';

    public bool $justSent = false;

    public function mount(Property $property): void
    {
        $this->property = $property;
    }

    public function send(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === UserRole::Buyer, 403);

        $this->ensureIsNotRateLimited();

        $this->validate([
            'message' => 'required|string|max:2000',
        ]);

        $inquiry = Inquiry::create([
            'property_id' => $this->property->id,
            'buyer_id' => auth()->id(),
            'agent_id' => $this->property->agent_id,
            'message' => $this->message,
        ]);

        RateLimiter::hit($this->throttleKey(), 600);

        NotifyAgentOfInquiry::dispatch($inquiry);

        $this->message = '';
        $this->justSent = true;

        $this->dispatch('toast', type: 'success', message: 'Your inquiry has been sent to the agent.');
    }

    public function sendAnother(): void
    {
        $this->justSent = false;
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'message' => 'Too many inquiries sent. Please try again in '.ceil($seconds / 60).' minute(s).',
        ]);
    }

    protected function throttleKey(): string
    {
        return 'inquiry:'.auth()->id();
    }

    public function render()
    {
        return view('livewire.buyer.inquiry-form');
    }
}
