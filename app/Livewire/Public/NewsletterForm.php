<?php

namespace App\Livewire\Public;

use App\Mail\NewsletterSubscribedMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class NewsletterForm extends Component
{
    public string $email = '';

    protected function rules(): array
    {
        return [
            'email' => 'required|email|max:255|unique:subscribers,email',
        ];
    }

    public function subscribe(): void
    {
        $validated = $this->validate();

        $subscriber = Subscriber::create($validated);

        Mail::to($subscriber->email)->send(new NewsletterSubscribedMail($subscriber));

        $this->reset('email');

        session()->flash('success', "You're subscribed!");
    }

    public function render()
    {
        return view('livewire.public.newsletter-form');
    }
}
