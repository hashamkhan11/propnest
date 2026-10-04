<?php

namespace App\Support;

use App\Enums\Settings\Currency;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class Settings
{
    private const CACHE_KEY = 'site_settings.currency';

    public static function currency(): Currency
    {
        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => SiteSetting::query()->firstOrFail()->currency,
        );
    }

    public static function setCurrency(Currency $currency): void
    {
        SiteSetting::query()->firstOrFail()->update(['currency' => $currency]);

        Cache::forget(self::CACHE_KEY);
    }

    public static function maxFeaturedListings(): ?int
    {
        return SiteSetting::query()->firstOrFail()->max_featured_listings;
    }

    public static function setMaxFeaturedListings(?int $max): void
    {
        SiteSetting::query()->firstOrFail()->update(['max_featured_listings' => $max]);
    }

    public static function freeListingLimit(): ?int
    {
        return SiteSetting::query()->firstOrFail()->free_listing_limit;
    }

    public static function setFreeListingLimit(?int $limit): void
    {
        SiteSetting::query()->firstOrFail()->update(['free_listing_limit' => $limit]);
    }
}
