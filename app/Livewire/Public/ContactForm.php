<?php

namespace App\Livewire\Public;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ContactForm extends Component
{
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
        $validated = $this->validate();

        $contactMessage = ContactMessage::create($validated);

        Mail::to(config('mail.from.address'))->send(new ContactMessageReceived($contactMessage));

        $this->reset(['name', 'email', 'subject', 'message']);

        session()->flash('success', "Thanks for reaching out! We'll get back to you soon.");
    }

    public function render()
    {
        return view('livewire.public.contact-form');
    }
}
