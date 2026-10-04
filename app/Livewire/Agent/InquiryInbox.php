<?php

namespace App\Livewire\Agent;

use App\Jobs\NotifyBuyerOfInquiryReply;
use App\Models\Inquiry;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InquiryInbox extends Component
{
    use WithPagination;

    public ?int $replyingId = null;

    public string $replyMessage = '';

    public function startReply(int $inquiryId): void
    {
        $this->replyingId = $inquiryId;
        $this->replyMessage = '';
    }

    public function cancelReply(): void
    {
        $this->replyingId = null;
        $this->replyMessage = '';
    }

    public function sendReply(Inquiry $inquiry): void
    {
        abort_unless($inquiry->agent_id === auth()->id(), 403);

        $this->validate([
            'replyMessage' => 'required|string|max:2000',
        ]);

        $inquiry->update([
            'reply' => $this->replyMessage,
            'replied_at' => now(),
        ]);

        NotifyBuyerOfInquiryReply::dispatch($inquiry);

        $this->replyingId = null;
        $this->replyMessage = '';

        $this->dispatch('toast', type: 'success', message: 'Reply sent to the buyer.');
    }

    public function render()
    {
        $inquiries = Inquiry::where('agent_id', auth()->id())
            ->with(['property', 'buyer'])
            ->latest()
            ->paginate(15);

        $unreadIds = $inquiries->getCollection()
            ->whereNull('read_at')
            ->pluck('id')
            ->all();

        if ($unreadIds !== []) {
            Inquiry::whereIn('id', $unreadIds)->update(['read_at' => now()]);
        }

        return view('livewire.agent.inquiry-inbox', [
            'inquiries' => $inquiries,
            'unreadIds' => $unreadIds,
        ]);
    }
}
