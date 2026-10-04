<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Payments & Refunds'])]
class PaymentsIndex extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.payments-index', [
            'payments' => Payment::with(['agent', 'property', 'agentSubscription.plan', 'pendingRefundRequest'])->latest()->paginate(15),
        ]);
    }
}
