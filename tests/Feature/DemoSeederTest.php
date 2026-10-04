<?php

namespace Tests\Feature;

use App\Enums\Payment\PaymentStatus;
use App\Enums\Property\PropertyStatus;
use App\Enums\User\AgentVerificationStatus;
use App\Models\Inquiry;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RefundRequest;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Seeding is slow (it processes ~150 photos), so it runs once and every
 * check lives in a single test.
 */
class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_builds_a_consistent_marketplace(): void
    {
        Storage::fake('public');

        $this->seed(DemoSeeder::class);

        // Demo logins exist with the documented roles.
        $this->assertSame('super_admin', User::where('email', 'admin@propnest.test')->value('role')->value);
        $this->assertSame('admin', User::where('email', 'moderator@propnest.test')->value('role')->value);
        $this->assertSame('agent', User::where('email', 'agent@propnest.test')->value('role')->value);
        $this->assertSame('buyer', User::where('email', 'buyer@propnest.test')->value('role')->value);

        // Every listing has a cover photo whose files exist.
        $properties = Property::with('images', 'cityRecord', 'agent.agentProfile')->get();
        $this->assertGreaterThanOrEqual(40, $properties->count());

        foreach ($properties as $property) {
            $this->assertSame(1, $property->images->where('is_cover', true)->count(), $property->title);
            $this->assertSame($property->city, $property->cityRecord->name);

            foreach ($property->images as $image) {
                Storage::disk('public')->assertExists([$image->path, $image->thumbnail_path]);
            }
        }

        // Published listings belong to agents with a filled-in profile.
        $published = $properties->where('status', PropertyStatus::Published);
        $this->assertGreaterThanOrEqual(30, $published->count());
        $this->assertTrue($published->every(fn (Property $p) => filled($p->agent->agentProfile->agency_name)));
        $this->assertTrue(User::where('email', 'agent@propnest.test')->first()->agentProfile->verification_status === AgentVerificationStatus::Verified);

        // Every currently featured listing is backed by a completed payment covering today.
        foreach ($properties->where('is_featured', true) as $property) {
            $this->assertTrue($property->payments()
                ->where('status', PaymentStatus::Completed)
                ->where('featured_until', $property->featured_until)
                ->exists(), $property->title);
        }

        // Refund requests never exceed what was paid.
        foreach (RefundRequest::with('payment')->get() as $request) {
            $this->assertLessThanOrEqual($request->payment->amount, $request->refund_amount_cents);
        }

        // Nothing happens before the listing exists or after today.
        $this->assertSame(0, Inquiry::join('properties', 'properties.id', '=', 'inquiries.property_id')
            ->whereColumn('inquiries.created_at', '<', 'properties.created_at')->count());
        $this->assertSame(0, Payment::where('created_at', '>', now())->count());

        // Both demo accounts have something to look at.
        $this->assertGreaterThan(0, Inquiry::whereRelation('buyer', 'email', 'buyer@propnest.test')->count());
        $this->assertGreaterThan(0, User::where('email', 'buyer@propnest.test')->first()->favorites()->count());

        // Every page renders with realistic data (and no lazy loading in strict mode).
        $listing = $published->first();
        $pages = [
            'admin@propnest.test' => ['admin.dashboard', 'admin.moderation.index', 'admin.agents.index', 'admin.users.index',
                'admin.payments.index', 'admin.refund-requests.index', 'admin.settings.edit', 'admin.categories.index',
                'admin.regions.index', 'admin.featured-tiers.index', 'admin.subscription-plans.index', 'admin.reports.index',
                'admin.properties.index', 'admin.analytics', 'admin.contact-messages.index', 'admin.subscribers.index', 'admin.create-admin'],
            'agent@propnest.test' => ['agent.dashboard', 'agent.properties.index', 'agent.properties.create',
                'agent.inquiries.index', 'agent.subscriptions.index'],
            'buyer@propnest.test' => ['buyer.dashboard', 'favorites.index', 'saved-searches.index', 'compare.index'],
        ];

        foreach ($pages as $email => $routes) {
            $this->actingAs(User::where('email', $email)->first());

            foreach ([...$routes, 'home', 'properties.index', 'agents.index', 'profile'] as $route) {
                $this->get(route($route))->assertOk();
            }

            $this->get(route('properties.show', $listing))->assertOk();
            $this->get(route('agents.show', $listing->agent))->assertOk();
        }
    }
}
