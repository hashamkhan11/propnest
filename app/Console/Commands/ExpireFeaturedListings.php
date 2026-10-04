<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class ExpireFeaturedListings extends Command
{
    protected $signature = 'properties:expire-featured';

    protected $description = 'Unfeature properties whose 30-day featured period has ended (fallback for when the queued expiry job never ran).';

    public function handle(): int
    {
        $count = Property::query()
            ->where('is_featured', true)
            ->whereNotNull('featured_until')
            ->where('featured_until', '<=', now())
            ->update(['is_featured' => false]);

        $this->info("Unfeatured {$count} listing(s).");

        return self::SUCCESS;
    }
}
