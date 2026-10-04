<?php

namespace App\Http\Controllers\Agent;

use App\Enums\Payment\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Stripe sends the agent here when they leave Checkout without paying for a
 * featured placement. The pending payment is closed so it doesn't linger.
 */
class CancelFeatureCheckoutController extends Controller
{
    public function __invoke(Request $request, Property $property): RedirectResponse
    {
        abort_unless($property->agent_id === $request->user()->id, 403);

        if ($sessionId = $request->query('session_id')) {
            Payment::where('property_id', $property->id)
                ->where('stripe_checkout_session_id', $sessionId)
                ->where('status', PaymentStatus::Pending)
                ->update(['status' => PaymentStatus::Cancelled]);
        }

        return redirect()->route('agent.properties.index')
            ->with('error', 'Payment cancelled — your listing was not featured. You can try again anytime from My Listings.');
    }
}
