<?php

namespace App\Http\Controllers\Agent;

use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\AgentSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Stripe sends the agent here when they leave Checkout without paying for a
 * subscription. The pending subscription is closed so it doesn't linger.
 */
class CancelSubscriptionCheckoutController extends Controller
{
    public function __invoke(Request $request, AgentSubscription $subscription): RedirectResponse
    {
        abort_unless($subscription->agent_id === $request->user()->id, 403);

        if ($sessionId = $request->query('session_id')) {
            AgentSubscription::where('id', $subscription->id)
                ->where('stripe_checkout_session_id', $sessionId)
                ->where('status', AgentSubscriptionStatus::Pending)
                ->update(['status' => AgentSubscriptionStatus::Cancelled]);
        }

        return redirect()->route('agent.subscriptions.index')
            ->with('error', 'Payment cancelled — your subscription was not activated. You can try again anytime.');
    }
}
