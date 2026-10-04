<?php

namespace Tests\Feature\Property;

use App\Enums\User\UserRole;
use App\Jobs\MatchSavedSearchesForProperty;
use App\Mail\NewPropertyMatchMail;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SavedSearchMatchEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_matching_saved_search_notifies_its_owner(): void
    {
        Mail::fake();

        $buyer = User::factory()->create(['role' => UserRole::Buyer]);
        $savedSearch = $buyer->savedSearches()->create([
            'filters' => [],
            'alerts_enabled' => true,
        ]);

        $property = Property::factory()->create();

        MatchSavedSearchesForProperty::dispatch($property);

        Mail::assertSent(NewPropertyMatchMail::class, function ($mail) use ($buyer) {
            return $mail->hasTo($buyer->email);
        });
    }

    public function test_a_saved_search_with_alerts_disabled_is_not_notified(): void
    {
        Mail::fake();

        $buyer = User::factory()->create(['role' => UserRole::Buyer]);
        $buyer->savedSearches()->create([
            'filters' => [],
            'alerts_enabled' => false,
        ]);

        $property = Property::factory()->create();

        MatchSavedSearchesForProperty::dispatch($property);

        Mail::assertNotSent(NewPropertyMatchMail::class);
    }
}
