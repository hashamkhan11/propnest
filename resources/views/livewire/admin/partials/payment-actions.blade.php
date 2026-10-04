@if ($payment->status === \App\Enums\Payment\PaymentStatus::Refunded)
    <x-badge variant="gray">Refunded{{ $payment->refunded_at ? ' '.$payment->refunded_at->format('M j, Y') : '' }}</x-badge>
@elseif ($payment->status === \App\Enums\Payment\PaymentStatus::Completed && $payment->pendingRefundRequest)
    <x-badge variant="info">Refund Requested</x-badge>
@else
    <span class="text-sm text-gray-300">&mdash;</span>
@endif
