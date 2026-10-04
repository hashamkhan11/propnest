<?php

namespace App\Livewire\Admin;

use App\Enums\Payment\PaymentStatus;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Jobs\NotifyAgentOfRefund;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Stripe\Exception\ApiErrorException;
use Stripe\Refund;
use Stripe\Stripe;

#[Layout('layouts.admin', ['title' => 'Refund Requests'])]
class RefundRequestsQueue extends Component
{
    public ?int $rejectingRequestId = null;

    public string $rejectionNote = '';

    public function startReject(int $requestId): void
    {
        $this->rejectingRequestId = $requestId;
        $this->rejectionNote = '';
    }

    public function cancelReject(): void
    {
        $this->rejectingRequestId = null;
    }

    public function reject(RefundRequest $refundRequest): void
    {
        if ($refundRequest->status !== RefundRequestStatus::Pending) {
            session()->flash('error', 'This request has already been reviewed.');
            $this->rejectingRequestId = null;

            return;
        }

        $this->validate(['rejectionNote' => 'nullable|string|max:1000']);

        $refundRequest->update([
            'status' => RefundRequestStatus::Rejected,
            'admin_notes' => $this->rejectionNote !== '' ? $this->rejectionNote : null,
            'reviewed_by_user_id' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $this->rejectingRequestId = null;

        session()->flash('success', 'Refund request rejected.');
    }

    public function approve(RefundRequest $refundRequest): void
    {
        if ($refundRequest->status !== RefundRequestStatus::Pending) {
            $this->dispatch('toast', type: 'error', message: 'This request has already been reviewed.');

            return;
        }

        $payment = $refundRequest->payment;

        if ($payment->status !== PaymentStatus::Completed) {
            $this->dispatch('toast', type: 'error', message: 'This payment can no longer be refunded.');

            return;
        }

        $refundAmountCents = $refundRequest->refund_amount_cents ?? $payment->amount;

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $refund = Refund::create([
                'payment_intent' => $payment->stripe_payment_intent_id,
                'amount' => $refundAmountCents,
            ]);
        } catch (ApiErrorException $e) {
            report($e);

            $this->dispatch('toast', type: 'error', message: 'Stripe refund failed. Please try again in a moment.');

            return;
        }

        if ($refund->status !== 'succeeded') {
            $this->dispatch('toast', type: 'error', message: 'Stripe did not confirm the refund. Please try again in a moment.');

            return;
        }

        DB::transaction(function () use ($refundRequest, $payment, $refund) {
            $payment->update([
                'status' => PaymentStatus::Refunded,
                'refunded_at' => now(),
                'stripe_refund_id' => $refund->id,
            ]);

            $payment->property->update(['is_featured' => false, 'featured_until' => null]);

            $refundRequest->update([
                'status' => RefundRequestStatus::Approved,
                'reviewed_by_user_id' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        });

        NotifyAgentOfRefund::dispatch($payment);

        $this->dispatch('toast', type: 'success', message: 'Refund approved and processed successfully.');
    }

    public function render()
    {
        return view('livewire.admin.refund-requests-queue', [
            'refundRequests' => RefundRequest::with(['agent', 'property', 'payment'])
                ->where('status', RefundRequestStatus::Pending)
                ->latest()
                ->paginate(15),
        ]);
    }
}
