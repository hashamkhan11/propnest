<?php

namespace App\Jobs;

use App\DTO\Property\PropertySearchFilters;
use App\Mail\NewPropertyMatchMail;
use App\Models\Property;
use App\Models\SavedSearch;
use App\Services\Property\PropertySearchService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class MatchSavedSearchesForProperty
{
    use Dispatchable;

    public function __construct(public Property $property) {}

    public function handle(PropertySearchService $service): void
    {
        SavedSearch::where('alerts_enabled', true)
            ->with('user')
            ->get()
            ->each(function (SavedSearch $savedSearch) use ($service) {
                $filters = PropertySearchFilters::fromArray($savedSearch->filters);

                if ($service->matches($this->property, $filters)) {
                    Mail::to($savedSearch->user)->send(new NewPropertyMatchMail($this->property, $savedSearch));
                }
            });
    }
}
