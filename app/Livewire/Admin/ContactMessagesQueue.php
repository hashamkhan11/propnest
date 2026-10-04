<?php

namespace App\Livewire\Admin;

use App\Enums\ContactMessage\ContactMessageStatus;
use App\Jobs\NotifyContactMessageSender;
use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Contact Messages'])]
class ContactMessagesQueue extends Component
{
    use WithPagination;

    public ?int $viewingMessageId = null;

    public ?int $replyingId = null;

    public string $replyMessage = '';

    public function view(ContactMessage $contactMessage): void
    {
        $this->viewingMessageId = $contactMessage->id;

        if ($contactMessage->status === ContactMessageStatus::New) {
            $contactMessage->update(['status' => ContactMessageStatus::Read]);
        }
    }

    public function closeView(): void
    {
        $this->viewingMessageId = null;
        $this->cancelReply();
    }

    public function startReply(int $contactMessageId): void
    {
        $this->replyingId = $contactMessageId;
        $this->replyMessage = '';
    }

    public function cancelReply(): void
    {
        $this->replyingId = null;
        $this->replyMessage = '';
    }

    public function sendReply(ContactMessage $contactMessage): void
    {
        $this->validate([
            'replyMessage' => 'required|string|max:5000',
        ]);

        $contactMessage->update([
            'reply' => $this->replyMessage,
            'replied_at' => now(),
            'replied_by_user_id' => auth()->id(),
            'status' => ContactMessageStatus::Replied,
        ]);

        NotifyContactMessageSender::dispatch($contactMessage);

        $this->cancelReply();

        $this->dispatch('toast', type: 'success', message: 'Reply sent to '.$contactMessage->email.'.');
    }

    public function render()
    {
        return view('livewire.admin.contact-messages-queue', [
            'messages' => ContactMessage::latest()->paginate(15),
        ]);
    }
}
