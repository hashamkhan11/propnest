<?php

namespace Database\Seeders;

use App\Enums\Property\PropertyPurpose;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Services\Property\PropertyImageThumbnailService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoListingSeeder extends Seeder
{
    /**
     * agent email => list of [city, neighborhood, type, purpose, status?]
     */
    private const LISTINGS = [
        DemoUserSeeder::AGENT_EMAIL => [
            ['Austin', 'Zilker', 'house', 'for_sale'],
            ['Austin', 'Travis Heights', 'house', 'for_sale', 'sold'],
            ['Austin', 'Hyde Park', 'house', 'for_rent'],
            ['Austin', 'Mueller', 'townhouse', 'for_sale'],
            ['Austin', 'East Austin', 'commercial', 'for_rent'],
            ['Austin', 'Southwest Austin', 'land', 'for_sale'],
            ['Dallas', 'Uptown', 'condo', 'for_sale'],
            ['Dallas', 'Lakewood', 'house', 'for_sale'],
            ['Dallas', 'Lower Greenville', 'apartment', 'for_rent'],
        ],
        'daniel.reyes@example.com' => [
            ['Denver', 'Highlands', 'house', 'for_sale'],
            ['Denver', 'Capitol Hill', 'apartment', 'for_rent'],
            ['Denver', 'Washington Park', 'house', 'for_sale'],
            ['Denver', 'RiNo', 'commercial', 'for_sale'],
            ['Denver', 'RiNo', 'condo', 'for_rent', 'rented'],
            ['Denver', 'Green Valley Ranch', 'land', 'for_sale'],
            ['Denver', 'Highlands', 'townhouse', 'for_rent'],
        ],
        'priya.natarajan@example.com' => [
            ['Seattle', 'Capitol Hill', 'apartment', 'for_rent'],
            ['Seattle', 'Ballard', 'house', 'for_sale'],
            ['Seattle', 'Fremont', 'commercial', 'for_rent'],
            ['Seattle', 'Queen Anne', 'condo', 'for_sale'],
            ['Seattle', 'Ballard', 'townhouse', 'for_sale'],
            ['Seattle', 'Queen Anne', 'house', 'for_sale', 'rejected'],
            ['Seattle', 'Fremont', 'apartment', 'for_rent'],
        ],
        'marcus.bennett@example.com' => [
            ['Miami', 'Brickell', 'condo', 'for_sale'],
            ['Miami', 'Brickell', 'apartment', 'for_rent'],
            ['Miami', 'Coconut Grove', 'house', 'for_sale', 'under_offer'],
            ['Miami', 'Wynwood', 'commercial', 'for_rent'],
            ['Miami', 'Little Havana', 'apartment', 'for_rent'],
            ['Orlando', 'College Park', 'house', 'for_sale'],
            ['Orlando', 'Thornton Park', 'condo', 'for_rent'],
            ['Orlando', 'Baldwin Park', 'townhouse', 'for_sale'],
            ['Orlando', 'Lake Nona', 'land', 'for_sale'],
        ],
        'elena.petrova@example.com' => [
            ['Chicago', 'Lincoln Park', 'house', 'for_sale'],
            ['Chicago', 'Wicker Park', 'apartment', 'for_rent'],
            ['Chicago', 'Logan Square', 'townhouse', 'for_sale'],
            ['Chicago', 'West Loop', 'commercial', 'for_sale'],
            ['Chicago', 'West Loop', 'condo', 'for_sale'],
            ['Chicago', 'Logan Square', 'apartment', 'for_rent', 'archived'],
            ['Chicago', 'Lincoln Park', 'condo', 'for_rent'],
        ],
        'jordan.hayes@example.com' => [
            ['San Diego', 'North Park', 'house', 'for_sale'],
            ['San Diego', 'Hillcrest', 'condo', 'for_rent'],
            ['San Diego', 'Mission Hills', 'house', 'for_sale', 'pending_review'],
            ['San Diego', 'South Park', 'apartment', 'for_rent', 'pending_review'],
            ['San Diego', 'North Park', 'townhouse', 'for_sale'],
        ],
        'sofia.alvarez@example.com' => [
            ['Austin', 'Hyde Park', 'apartment', 'for_rent', 'pending_review'],
            ['Austin', 'Zilker', 'house', 'for_sale', 'draft'],
        ],
    ];

    /** How expensive each city is relative to the national baseline. */
    private const CITY_PRICE_FACTOR = [
        'Austin' => 1.0, 'Dallas' => 0.9, 'Denver' => 1.1, 'Seattle' => 1.5,
        'Miami' => 1.3, 'Orlando' => 0.8, 'Chicago' => 0.9, 'San Diego' => 1.6,
    ];

    /** Baseline sale price and monthly rent in USD. */
    private const BASE_PRICE = [
        'house' => [520_000, 2_900],
        'townhouse' => [420_000, 2_500],
        'condo' => [340_000, 2_100],
        'apartment' => [300_000, 1_900],
        'commercial' => [900_000, 4_800],
        'land' => [180_000, 0],
    ];

    /** Which photo folder supplies the cover image, and how many interior shots follow. */
    private const PHOTOS = [
        'house' => ['house', 3],
        'townhouse' => ['house', 3],
        'condo' => ['apartment', 2],
        'apartment' => ['apartment', 2],
        'commercial' => ['commercial', 0],
        'land' => ['land', 0],
    ];

    private const AMENITIES = [
        'house' => [['Garage', 'Garden', 'Parking', 'Air Conditioning'], ['Pool', 'Pet Friendly', 'Security']],
        'townhouse' => [['Garage', 'Parking', 'Air Conditioning'], ['Balcony', 'Pet Friendly', 'Garden']],
        'condo' => [['Balcony', 'Elevator', 'Air Conditioning'], ['Pool', 'Security', 'Parking', 'Pet Friendly']],
        'apartment' => [['Air Conditioning', 'Elevator'], ['Balcony', 'Furnished', 'Pet Friendly', 'Parking']],
        'commercial' => [['Parking', 'Security', 'Air Conditioning'], ['Elevator']],
        'land' => [[], []],
    ];

    private const FEATURES = [
        'house' => [
            'an open-plan kitchen with quartz countertops', 'wide-plank hardwood floors', 'a fenced backyard with a covered patio',
            'a primary suite with a walk-in closet', 'a dedicated home office', 'energy-efficient windows', 'a two-car garage',
        ],
        'townhouse' => [
            'an attached garage', 'a private rooftop deck', 'a chef-style kitchen with an island',
            'two separate living areas', 'nine-foot ceilings', 'a primary suite with a soaking tub',
        ],
        'condo' => [
            'floor-to-ceiling windows', 'an in-unit washer and dryer', 'stainless steel appliances',
            'a private balcony', 'a 24-hour concierge', 'a residents-only fitness center', 'one assigned parking space',
        ],
        'apartment' => [
            'large south-facing windows', 'an in-unit washer and dryer', 'a renovated kitchen',
            'built-in closet storage', 'a shared rooftop terrace', 'secure package lockers',
        ],
        'commercial' => [
            'a large street-facing frontage', 'a flexible open floor plan', 'ADA-accessible restrooms',
            'updated HVAC', 'dedicated customer parking', 'a loading area at the rear',
        ],
    ];

    /** @var array<string, list<string>> */
    private array $photos = [];

    /** @var array<string, int> */
    private array $photoCursor = [];

    /** @var array<string, true> */
    private array $usedTitles = [];

    public function __construct(private PropertyImageThumbnailService $thumbnails) {}

    public function run(): void
    {
        foreach (['house', 'apartment', 'commercial', 'land', 'interior'] as $folder) {
            $this->photos[$folder] = glob(database_path("seeders/images/{$folder}/*.jpg")) ?: [];
            sort($this->photos[$folder]);
            $this->photoCursor[$folder] = 0;
        }

        $categories = PropertyCategory::pluck('id', 'slug');
        $cities = City::pluck('id', 'name');
        $amenities = Amenity::pluck('id', 'name');

        foreach (self::LISTINGS as $email => $listings) {
            $agent = User::where('email', $email)->firstOrFail();

            foreach ($listings as $listing) {
                [$cityName, $hood, $type, $purpose] = $listing;
                $status = PropertyStatus::from($listing[4] ?? 'published');

                $property = $this->createProperty(
                    $agent, $cityName, $hood, $type, PropertyPurpose::from($purpose), $status,
                    $categories[$type], $cities[$cityName],
                );

                $this->attachAmenities($property, $type, $amenities->all());
                $this->attachPhotos($property, $type);
            }
        }
    }

    private function createProperty(
        User $agent, string $cityName, string $hood, string $type, PropertyPurpose $purpose,
        PropertyStatus $status, int $categoryId, int $cityId,
    ): Property {
        $city = LocationSeeder::CITIES[$cityName];
        [, $lat, $lng, $street] = collect($city['neighborhoods'])->firstWhere(0, $hood);

        [$bedrooms, $bathrooms, $area] = $this->size($type);
        $price = $this->price($type, $purpose, $cityName, $bedrooms);
        $listedAt = now()->subDays(mt_rand(3, 300))->subMinutes(mt_rand(0, 1440));
        $everPublished = ! in_array($status, [PropertyStatus::Draft, PropertyStatus::PendingReview, PropertyStatus::Rejected], true);

        return Property::create([
            'agent_id' => $agent->id,
            'title' => $this->title($type, $purpose, $hood, $street, $bedrooms, $area),
            'description' => $this->description($type, $purpose, $hood, $cityName, $bedrooms, $bathrooms, $area),
            'price' => $price,
            'property_type' => PropertyType::from($type),
            'category_id' => $categoryId,
            'purpose' => $purpose,
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'area' => $area,
            'address' => mt_rand(100, 4800).' '.$street.', '.$cityName.', '.$city['abbr'],
            'city' => $cityName,
            'city_id' => $cityId,
            // Within roughly 500 m of the neighborhood center.
            'latitude' => round($lat + mt_rand(-50, 50) / 10_000, 7),
            'longitude' => round($lng + mt_rand(-50, 50) / 10_000, 7),
            'status' => $status,
            'has_been_published' => $everPublished,
            'rejection_reason' => $status === PropertyStatus::Rejected
                ? 'The photos show a different house from the one at this address. Please upload photos of the actual property.'
                : null,
            'views_count' => $everPublished ? mt_rand(40, 1200) : 0,
            'is_featured' => false,
            'created_at' => $listedAt,
            'updated_at' => $listedAt,
        ]);
    }

    /**
     * @return array{0: int, 1: int, 2: int} bedrooms, bathrooms, area in sq ft
     */
    private function size(string $type): array
    {
        return match ($type) {
            'house' => [$beds = mt_rand(3, 5), max(2, $beds - mt_rand(0, 1)), $this->roundTo(900 + $beds * mt_rand(380, 520), 10)],
            'townhouse' => [$beds = mt_rand(2, 4), $beds, $this->roundTo(700 + $beds * mt_rand(320, 420), 10)],
            'condo' => [$beds = mt_rand(1, 3), max(1, $beds - mt_rand(0, 1)), $this->roundTo(450 + $beds * mt_rand(300, 400), 10)],
            'apartment' => [$beds = mt_rand(1, 3), max(1, $beds - 1), $this->roundTo(350 + $beds * mt_rand(280, 360), 10)],
            'commercial' => [0, mt_rand(1, 3), $this->roundTo(mt_rand(1_800, 6_500), 50)],
            'land' => [0, 0, $this->roundTo(mt_rand(10, 80) * 43_560 / 40, 10)],
        };
    }

    private function price(string $type, PropertyPurpose $purpose, string $cityName, int $bedrooms): int
    {
        [$sale, $rent] = self::BASE_PRICE[$type];
        $factor = self::CITY_PRICE_FACTOR[$cityName] * mt_rand(80, 125) / 100;

        // More bedrooms cost more; land and commercial have none.
        $factor *= 1 + max(0, $bedrooms - 2) * 0.12;

        return $purpose === PropertyPurpose::ForRent
            ? $this->roundTo($rent * $factor, 50)
            : $this->roundTo($sale * $factor, 5_000);
    }

    private function title(string $type, PropertyPurpose $purpose, string $hood, string $street, int $beds, int $area): string
    {
        $forRent = $purpose === PropertyPurpose::ForRent;

        do {
            $title = match ($type) {
                'house' => $this->pick(['Craftsman', 'Mid-Century', 'Modern', 'Renovated', 'Ranch-Style', 'Colonial'])
                    ." {$beds}-Bed ".($forRent ? 'House for Rent' : 'Home')." in {$hood}",
                'townhouse' => $this->pick(['End-Unit', 'Three-Story', 'Modern', 'Corner'])." Townhouse in {$hood}",
                'condo' => "{$hood} Condo with ".$this->pick(['City Views', 'a Private Balcony', 'a Rooftop Deck', 'Skyline Views']),
                'apartment' => $this->pick(['Bright', 'Sunny', 'Loft-Style', 'Updated', 'Corner-Unit'])." {$beds}-Bed Apartment in {$hood}",
                'commercial' => $this->pick($forRent
                    ? ['Retail Space', 'Creative Office Space', 'Street-Level Storefront']
                    : ['Mixed-Use Building', 'Retail Building', 'Office Building'])." on {$street}",
                'land' => number_format($area / 43_560, 2).'-Acre Residential Lot in '.$hood,
            };
        } while (isset($this->usedTitles[$title]));

        $this->usedTitles[$title] = true;

        return $title;
    }

    private function description(
        string $type, PropertyPurpose $purpose, string $hood, string $cityName, int $beds, int $baths, int $area,
    ): string {
        $sqft = number_format($area);

        if ($type === 'land') {
            $acres = number_format($area / 43_560, 2);

            return implode("\n\n", [
                "A level {$acres}-acre lot in {$hood}, zoned for single-family residential use. Water, sewer and electric are available at the street.",
                'The site has mature trees along the back boundary and a recent survey is available on request. Ideal for a custom home or a small build-to-rent project.',
                "Ten minutes from shopping and schools, with quick access to downtown {$cityName}. Ask about the builder partners we work with.",
            ]);
        }

        [$first, $second, $third] = $this->pickMany(self::FEATURES[$type], 3);

        $intro = match ($type) {
            'commercial' => "{$sqft} sq ft of commercial space in {$hood}, one of {$cityName}'s busiest areas for foot traffic.",
            default => "This {$beds}-bedroom, {$baths}-bathroom {$type} offers {$sqft} sq ft of living space in {$hood}, one of {$cityName}'s most sought-after neighborhoods.",
        };

        $closing = match (true) {
            $type === 'commercial' && $purpose === PropertyPurpose::ForRent => 'Available on a 3 to 5 year lease. Tenant improvements negotiable for the right business.',
            $type === 'commercial' => 'Sold with existing tenants in place. Financials available to qualified buyers after an NDA.',
            $purpose === PropertyPurpose::ForRent => 'Available now on a 12-month lease. Pets considered case by case. Utilities not included.',
            default => 'Contact me to schedule a private showing or to request the full disclosure package.',
        };

        return implode("\n\n", [
            $intro,
            "Highlights include {$first}, {$second} and {$third}.",
            "You're a short walk from {$hood}'s cafés, parks and shops, with an easy commute to downtown {$cityName}. {$closing}",
        ]);
    }

    /**
     * @param  array<string, int>  $amenityIds
     */
    private function attachAmenities(Property $property, string $type, array $amenityIds): void
    {
        [$always, $sometimes] = self::AMENITIES[$type];
        $names = array_merge($always, $this->pickMany($sometimes, mt_rand(0, count($sometimes))));

        $property->amenities()->attach(array_map(fn (string $name) => $amenityIds[$name], $names));
    }

    private function attachPhotos(Property $property, string $type): void
    {
        [$coverFolder, $interiorCount] = self::PHOTOS[$type];

        $files = [$this->nextPhoto($coverFolder)];

        for ($i = 0; $i < $interiorCount; $i++) {
            $files[] = $this->nextPhoto('interior');
        }

        // Commercial and land listings get a second exterior shot instead.
        if ($interiorCount === 0) {
            $files[] = $this->nextPhoto($coverFolder);
        }

        foreach ($files as $order => $file) {
            $path = 'properties/'.Str::random(40).'.jpg';
            Storage::disk('public')->put($path, file_get_contents($file));

            $property->images()->create([
                'path' => $path,
                'thumbnail_path' => $this->thumbnails->generate($path),
                'is_cover' => $order === 0,
                'sort_order' => $order,
            ]);
        }
    }

    /** Hands out photos in rotation so neighboring listings don't share a cover. */
    private function nextPhoto(string $folder): string
    {
        $photos = $this->photos[$folder];
        $photo = $photos[$this->photoCursor[$folder] % count($photos)];
        $this->photoCursor[$folder]++;

        return $photo;
    }

    private function roundTo(float $value, int $step): int
    {
        return (int) (round($value / $step) * $step);
    }

    /**
     * @template T
     *
     * @param  list<T>  $items
     * @return T
     */
    private function pick(array $items): mixed
    {
        return $items[mt_rand(0, count($items) - 1)];
    }

    /**
     * @template T
     *
     * @param  list<T>  $items
     * @return list<T>
     */
    private function pickMany(array $items, int $count): array
    {
        for ($i = count($items) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$items[$i], $items[$j]] = [$items[$j], $items[$i]];
        }

        return array_slice($items, 0, $count);
    }
}
