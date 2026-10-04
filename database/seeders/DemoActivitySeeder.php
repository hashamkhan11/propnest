<?php

namespace Database\Seeders;

use App\Enums\ContactMessage\ContactMessageStatus;
use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Enums\RefundRequest\RefundRequestStatus;
use App\Enums\Report\ReportReason;
use App\Enums\Report\ReportStatus;
use App\Enums\Subscriber\SubscriberStatus;
use App\Enums\Subscription\AgentSubscriptionStatus;
use App\Enums\User\UserRole;
use App\Enums\User\UserStatus;
use App\Models\AgentSubscription;
use App\Models\Amenity;
use App\Models\ContactMessage;
use App\Models\FeaturedPricingTier;
use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RefundRequest;
use App\Models\Report;
use App\Models\Subscriber;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Payment\FeaturedListingRefundCalculator;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoActivitySeeder extends Seeder
{
    private const INQUIRIES = [
        'Hi, is this still available? I would love to schedule a viewing this weekend if possible.',
        'Could you share the HOA fees and what they cover?',
        'Are pets allowed? I have a small, well-behaved dog.',
        'What are the property taxes like for this one? Also, has the roof been replaced recently?',
        'Is the price negotiable? We are pre-approved and could close within 30 days.',
        'Could I get a video walkthrough? I am relocating from out of state.',
        'How far is it from the nearest elementary school?',
        'Is parking included, or is it an extra monthly cost?',
        'What is the earliest move-in date, and is a 6-month lease an option?',
        'Are there any known issues from the last inspection?',
    ];

    private const REPLIES = [
        'Thanks for reaching out! It is still available. Does Saturday at 11am work for a showing?',
        'Great question. I have sent the full details to your email, including the last two years of statements.',
        'Yes, pets are welcome with a one-time deposit. Happy to answer anything else.',
        'The seller is open to reasonable offers. Let me know when you would like to talk numbers.',
        'Absolutely, I will record a walkthrough tomorrow and send it over.',
        'It is about a 10-minute walk. The school ratings are in the listing packet I just emailed you.',
    ];

    /** @var Collection<int, User> */
    private Collection $buyers;

    /** @var Collection<int, Property> */
    private Collection $published;

    private User $moderator;

    public function __construct(private FeaturedListingRefundCalculator $refunds) {}

    public function run(): void
    {
        $this->moderator = User::where('email', DemoUserSeeder::ADMIN_EMAIL)->firstOrFail();
        $this->buyers = User::where('role', UserRole::Buyer)->where('status', UserStatus::Active)->orderBy('id')->get();
        $this->published = Property::with('agent')->where('status', PropertyStatus::Published)->orderBy('id')->get();

        $this->seedSubscriptions();
        $this->seedFeaturedPayments();
        $this->seedInquiries();
        $this->seedFavoritesAndSavedSearches();
        $this->seedReports();
        $this->seedContactMessagesAndSubscribers();
    }

    private function seedSubscriptions(): void
    {
        $plans = SubscriptionPlan::pluck('id', 'name');

        /** @var list<array{string, string, AgentSubscriptionStatus, int}> $subscriptions agent email, plan, status, started days ago */
        $subscriptions = [
            [DemoUserSeeder::AGENT_EMAIL, 'Professional', AgentSubscriptionStatus::Expired, 42],
            [DemoUserSeeder::AGENT_EMAIL, 'Professional', AgentSubscriptionStatus::Active, 12],
            ['marcus.bennett@example.com', 'Agency', AgentSubscriptionStatus::Active, 40],
            ['priya.natarajan@example.com', 'Starter', AgentSubscriptionStatus::Active, 5],
            ['daniel.reyes@example.com', 'Starter', AgentSubscriptionStatus::Expired, 75],
            ['daniel.reyes@example.com', 'Professional', AgentSubscriptionStatus::Active, 20],
            ['elena.petrova@example.com', 'Professional', AgentSubscriptionStatus::Cancelled, 26],
            ['elena.petrova@example.com', 'Professional', AgentSubscriptionStatus::Active, 25],
        ];

        foreach ($subscriptions as [$email, $planName, $status, $daysAgo]) {
            $agent = User::where('email', $email)->firstOrFail();
            $plan = SubscriptionPlan::findOrFail($plans[$planName]);
            $startedAt = now()->subDays($daysAgo)->setTime(mt_rand(8, 20), mt_rand(0, 59));
            $paid = $status !== AgentSubscriptionStatus::Cancelled;

            $subscription = AgentSubscription::create([
                'agent_id' => $agent->id,
                'subscription_plan_id' => $plan->id,
                'status' => $status,
                'stripe_checkout_session_id' => $this->stripeId('cs_test'),
                'stripe_payment_intent_id' => $paid ? $this->stripeId('pi') : null,
                'amount_cents' => $plan->price_cents,
                'listing_limit' => $paid ? $plan->listing_limit : null,
                'featured_credits_remaining' => $paid ? max(0, $plan->featured_credits - mt_rand(0, 2)) : 0,
                'started_at' => $paid ? $startedAt : null,
                'expires_at' => $paid ? $startedAt->copy()->addDays($plan->duration_days) : null,
                'created_at' => $startedAt,
                'updated_at' => $startedAt,
            ]);

            Payment::create([
                'agent_id' => $agent->id,
                'agent_subscription_id' => $subscription->id,
                'stripe_checkout_session_id' => $subscription->stripe_checkout_session_id,
                'stripe_payment_intent_id' => $subscription->stripe_payment_intent_id,
                'amount' => $plan->price_cents,
                'status' => $paid ? PaymentStatus::Completed : PaymentStatus::Cancelled,
                'created_at' => $startedAt,
                'updated_at' => $startedAt,
            ]);
        }
    }

    private function seedFeaturedPayments(): void
    {
        $standard = FeaturedPricingTier::where('name', 'Standard')->firstOrFail();
        $boost = FeaturedPricingTier::where('name', 'Weekend Boost')->firstOrFail();

        // Featured right now: one listing per city where possible.
        $featured = $this->published->unique('city')->take(7)->values();

        foreach ($featured as $i => $property) {
            $tier = $i % 3 === 2 ? $boost : $standard;
            $payment = $this->featuredPayment($property, $tier, PaymentStatus::Completed, now()->subDays(mt_rand(1, $tier->duration_days - 1)));

            $property->update(['is_featured' => true, 'featured_until' => $payment->featured_until]);
        }

        // A pending refund request on one of the active placements.
        $refundable = $featured->firstWhere('agent.email', 'marcus.bennett@example.com') ?? $featured->last();
        $payment = $refundable->payments()->latest('id')->firstOrFail();
        $this->refundRequest($payment, RefundRequestStatus::Pending, 'Sold the unit off-market two days after featuring it.', now()->subDay());

        // Past placements, spread over the last year, for the revenue chart.
        $history = $this->published->diff($featured)->values();

        foreach (range(1, 11) as $monthsAgo) {
            foreach (range(1, mt_rand(1, 3)) as $n) {
                $from = now()->subMonths($monthsAgo)->subDays(mt_rand(0, 25));
                $listedBefore = $history->filter(fn (Property $p) => $p->created_at->lessThan($from))->values();

                if ($listedBefore->isEmpty()) {
                    continue;
                }

                $property = $listedBefore[mt_rand(0, $listedBefore->count() - 1)];

                $this->featuredPayment($property, mt_rand(0, 2) ? $standard : $boost, PaymentStatus::Completed, $from);
            }
        }

        // A refunded placement: the agent asked five days in and an admin approved it.
        $refunded = $history->firstWhere('agent.email', 'elena.petrova@example.com') ?? $history->first();
        $payment = $this->featuredPayment($refunded, $standard, PaymentStatus::Completed, now()->subDays(20));
        $request = $this->refundRequest($payment, RefundRequestStatus::Approved, 'Listing went under contract, so the placement is no longer needed.', now()->subDays(15));
        $request->update([
            'admin_notes' => 'Approved: listing was under contract within the first week.',
            'reviewed_by_user_id' => $this->moderator->id,
            'reviewed_at' => now()->subDays(14),
        ]);
        $payment->update([
            'status' => PaymentStatus::Refunded,
            'refunded_at' => now()->subDays(14),
            'stripe_refund_id' => $this->stripeId('re'),
        ]);

        // Checkouts that never completed.
        $this->featuredPayment($history[1], $standard, PaymentStatus::Expired, now()->subDays(3), $this->stripeId('cs_test'))
            ->update(['stripe_payment_intent_id' => null]);
        $this->featuredPayment($history[2], $boost, PaymentStatus::Failed, now()->subDays(6))
            ->update(['failure_reason' => 'Your card was declined.']);
    }

    private function featuredPayment(Property $property, FeaturedPricingTier $tier, PaymentStatus $status, CarbonInterface $from, ?string $session = null): Payment
    {
        return Payment::create([
            'property_id' => $property->id,
            'agent_id' => $property->agent_id,
            'featured_pricing_tier_id' => $tier->id,
            'stripe_checkout_session_id' => $session ?? $this->stripeId('cs_test'),
            'stripe_payment_intent_id' => $this->stripeId('pi'),
            'amount' => $tier->price_cents,
            'status' => $status,
            'featured_from' => $from,
            'featured_until' => $from->copy()->addDays($tier->duration_days),
            'created_at' => $from,
            'updated_at' => $from,
        ]);
    }

    private function refundRequest(Payment $payment, RefundRequestStatus $status, string $reason, CarbonInterface $requestedAt): RefundRequest
    {
        return RefundRequest::create([
            'payment_id' => $payment->id,
            'property_id' => $payment->property_id,
            'agent_id' => $payment->agent_id,
            'reason' => $reason,
            'refund_amount_cents' => $this->refunds->calculateCents($payment, $requestedAt),
            'status' => $status,
            'created_at' => $requestedAt,
            'updated_at' => $requestedAt,
        ]);
    }

    private function seedInquiries(): void
    {
        $demoBuyer = $this->buyers->firstWhere('email', DemoUserSeeder::BUYER_EMAIL);
        $demoAgentListings = $this->published->where('agent.email', DemoUserSeeder::AGENT_EMAIL)->values();

        // The demo buyer has a conversation going with the demo agent, so both
        // demo logins have something in their inbox.
        $pairs = $demoAgentListings->take(3)->map(fn (Property $p) => [$demoBuyer, $p]);

        foreach (range(1, 30) as $i) {
            $pairs->push([$this->buyers[mt_rand(0, $this->buyers->count() - 1)], $this->published[mt_rand(0, $this->published->count() - 1)]]);
        }

        foreach ($pairs->unique(fn (array $pair) => $pair[0]->id.'-'.$pair[1]->id) as $i => [$buyer, $property]) {
            $sentAt = $this->randomTimeAfter($property->created_at);
            $replied = $i % 3 !== 0;
            $read = $replied || mt_rand(0, 1);

            Inquiry::create([
                'property_id' => $property->id,
                'buyer_id' => $buyer->id,
                'agent_id' => $property->agent_id,
                'message' => self::INQUIRIES[mt_rand(0, count(self::INQUIRIES) - 1)],
                'read_at' => $read ? $sentAt->copy()->addHours(mt_rand(1, 20)) : null,
                'reply' => $replied ? self::REPLIES[mt_rand(0, count(self::REPLIES) - 1)] : null,
                'replied_at' => $replied ? $sentAt->copy()->addHours(mt_rand(21, 40)) : null,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);
        }
    }

    private function seedFavoritesAndSavedSearches(): void
    {
        foreach ($this->buyers as $buyer) {
            $count = $buyer->email === DemoUserSeeder::BUYER_EMAIL ? 6 : mt_rand(0, 5);

            $ids = $this->published->pluck('id')->all();

            for ($i = count($ids) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            }

            foreach (array_slice($ids, 0, $count) as $propertyId) {
                $buyer->favorites()->create(['property_id' => $propertyId]);
            }
        }

        $demoBuyer = $this->buyers->firstWhere('email', DemoUserSeeder::BUYER_EMAIL);
        $pool = Amenity::where('name', 'Pool')->value('id');

        $searches = [
            [$demoBuyer, ['location' => 'Austin', 'propertyType' => 'house', 'purpose' => 'for_sale', 'minPrice' => '400000', 'maxPrice' => '800000', 'bedrooms' => '3'], true],
            [$demoBuyer, ['location' => 'Seattle', 'propertyType' => 'apartment', 'purpose' => 'for_rent', 'maxPrice' => '3500'], false],
            [$this->buyers[1], ['location' => 'Miami', 'propertyType' => 'condo', 'amenityIds' => [$pool]], true],
            [$this->buyers[2], ['location' => 'Chicago', 'purpose' => 'for_rent', 'bedrooms' => '2'], true],
            [$this->buyers[3], ['keyword' => 'garage', 'location' => 'Denver', 'minArea' => '1500'], true],
        ];

        foreach ($searches as [$buyer, $filters, $alerts]) {
            $buyer->savedSearches()->create([
                'filters' => $this->searchFilters($filters),
                'alerts_enabled' => $alerts,
            ]);
        }
    }

    /**
     * Same shape the search page saves, so the saved search reopens cleanly.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function searchFilters(array $filters): array
    {
        return array_merge([
            'keyword' => '', 'location' => '', 'minPrice' => '', 'maxPrice' => '',
            'propertyType' => '', 'purpose' => '', 'bedrooms' => '', 'bathrooms' => '',
            'minArea' => '', 'amenityIds' => [],
        ], $filters);
    }

    private function seedReports(): void
    {
        $rejected = Property::where('status', PropertyStatus::Rejected)->firstOrFail();
        $commercial = $this->published->firstWhere('property_type', PropertyType::Commercial);
        $rental = $this->published->firstWhere('purpose', PropertyPurpose::ForRent);

        $reports = [
            [$this->published[4], ReportReason::IncorrectInfo, 'The square footage looks too high compared to the floor plan photo.', ReportStatus::Pending],
            [$rental, ReportReason::Fraud, 'Someone claiming to be the landlord asked me to wire a deposit before any viewing.', ReportStatus::Pending],
            [$rejected, ReportReason::IncorrectInfo, 'These photos are of a different house. I live on this street.', ReportStatus::ActionTaken],
            [$commercial, ReportReason::Spam, 'Posted twice with slightly different prices.', ReportStatus::Dismissed],
            [$this->published[9], ReportReason::Other, 'Listing says pet friendly but the agent told me no pets.', ReportStatus::Dismissed],
        ];

        foreach ($reports as $i => [$property, $reason, $details, $status]) {
            $reportedAt = $this->randomTimeAfter($property->created_at);
            $resolved = $status !== ReportStatus::Pending;

            Report::create([
                'property_id' => $property->id,
                'reported_by_user_id' => $this->buyers[$i + 4]->id,
                'reason' => $reason,
                'details' => $details,
                'status' => $status,
                'resolved_by_user_id' => $resolved ? $this->moderator->id : null,
                'resolved_at' => $resolved ? $reportedAt->copy()->addHours(mt_rand(2, 30)) : null,
                'created_at' => $reportedAt,
                'updated_at' => $reportedAt,
            ]);
        }
    }

    private function seedContactMessagesAndSubscribers(): void
    {
        $messages = [
            ['Rachel Green', 'rachel.green@example.com', 'Listing my agency', 'We are a team of four agents in Phoenix. Do you offer team accounts or volume pricing?', ContactMessageStatus::New, null],
            ['Tom Fischer', 'tom.fischer@example.com', 'Verification taking long', 'I submitted my license details last week and my profile still shows pending.', ContactMessageStatus::New, null],
            ['Amara Okafor', 'amara.okafor@example.com', 'Saved search alerts', 'I am not getting emails for my saved search in Denver. Is there a setting I am missing?', ContactMessageStatus::Read, null],
            ['Kevin Liu', 'kevin.liu@example.com', 'Refund timing', 'How long does a featured listing refund take to show up on my card?', ContactMessageStatus::Replied,
                'Refunds are sent to Stripe the moment we approve them. Your bank usually shows them within 5 to 10 business days.'],
            ['Isabel Santos', 'isabel.santos@example.com', 'Partnership', 'We run a moving company in Austin and would like to advertise to new buyers.', ContactMessageStatus::Replied,
                'Thanks, Isabel. We do not sell ad space yet, but we have added you to our partner waitlist.'],
        ];

        foreach ($messages as [$name, $email, $subject, $message, $status, $reply]) {
            $sentAt = now()->subDays(mt_rand(1, 40))->subMinutes(mt_rand(0, 1440));

            ContactMessage::create([
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'status' => $status,
                'reply' => $reply,
                'replied_at' => $reply ? $sentAt->copy()->addHours(mt_rand(3, 24)) : null,
                'replied_by_user_id' => $reply ? $this->moderator->id : null,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);
        }

        $emails = ['julia.r', 'mark.t', 'sam.patel', 'nina.k', 'leo.garcia', 'emma.w', 'david.o', 'sara.m', 'yusuf.a', 'kate.b', 'paul.d', 'irene.c'];

        foreach ($emails as $i => $local) {
            $joined = now()->subDays(mt_rand(1, 300));

            Subscriber::create([
                'email' => $local.'@example.com',
                'status' => $i < 10 ? SubscriberStatus::Active : SubscriberStatus::Unsubscribed,
                'created_at' => $joined,
                'updated_at' => $joined,
            ]);
        }
    }

    private function randomTimeAfter(CarbonInterface $start): CarbonInterface
    {
        $seconds = max(3_600, now()->diffInSeconds($start, true) - 3_600);

        return $start->copy()->addSeconds(mt_rand(1_800, (int) $seconds));
    }

    /** Looks like a Stripe id without pointing at anything real. */
    private function stripeId(string $prefix): string
    {
        return $prefix.'_demo'.Str::random(20);
    }
}
