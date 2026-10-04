<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * US states and cities used by the demo listings. Each city lists real
 * neighborhoods with their approximate center and a street that runs through
 * them, so listings land in believable places on the map.
 */
class LocationSeeder extends Seeder
{
    /**
     * @var array<string, array{state: string, abbr: string, neighborhoods: list<array{0: string, 1: float, 2: float, 3: string}>}>
     */
    public const CITIES = [
        'Austin' => ['state' => 'Texas', 'abbr' => 'TX', 'neighborhoods' => [
            ['Zilker', 30.2600, -97.7700, 'Barton Springs Rd'],
            ['Travis Heights', 30.2470, -97.7440, 'S Congress Ave'],
            ['Hyde Park', 30.3060, -97.7290, 'Duval St'],
            ['Mueller', 30.2980, -97.7050, 'Aldrich St'],
            ['East Austin', 30.2620, -97.7240, 'E 6th St'],
            ['Southwest Austin', 30.1900, -97.8700, 'W Slaughter Ln'],
        ]],
        'Dallas' => ['state' => 'Texas', 'abbr' => 'TX', 'neighborhoods' => [
            ['Uptown', 32.8000, -96.8010, 'McKinney Ave'],
            ['Lakewood', 32.8180, -96.7480, 'Gaston Ave'],
            ['Bishop Arts', 32.7490, -96.8280, 'N Bishop Ave'],
            ['Lower Greenville', 32.8130, -96.7700, 'Greenville Ave'],
        ]],
        'Denver' => ['state' => 'Colorado', 'abbr' => 'CO', 'neighborhoods' => [
            ['Highlands', 39.7620, -105.0110, 'W 32nd Ave'],
            ['Capitol Hill', 39.7310, -104.9790, 'E 13th Ave'],
            ['Washington Park', 39.7000, -104.9700, 'S Downing St'],
            ['RiNo', 39.7680, -104.9800, 'Larimer St'],
            ['Green Valley Ranch', 39.7900, -104.7600, 'E 48th Ave'],
        ]],
        'Seattle' => ['state' => 'Washington', 'abbr' => 'WA', 'neighborhoods' => [
            ['Capitol Hill', 47.6180, -122.3180, 'E Pine St'],
            ['Ballard', 47.6680, -122.3840, 'Ballard Ave NW'],
            ['Fremont', 47.6510, -122.3500, 'Fremont Ave N'],
            ['Queen Anne', 47.6370, -122.3570, 'Queen Anne Ave N'],
        ]],
        'Miami' => ['state' => 'Florida', 'abbr' => 'FL', 'neighborhoods' => [
            ['Brickell', 25.7620, -80.1960, 'Brickell Ave'],
            ['Coconut Grove', 25.7280, -80.2430, 'Grand Ave'],
            ['Wynwood', 25.8010, -80.1990, 'NW 2nd Ave'],
            ['Little Havana', 25.7670, -80.2190, 'SW 8th St'],
        ]],
        'Orlando' => ['state' => 'Florida', 'abbr' => 'FL', 'neighborhoods' => [
            ['Thornton Park', 28.5420, -81.3700, 'E Central Blvd'],
            ['College Park', 28.5700, -81.3940, 'Edgewater Dr'],
            ['Baldwin Park', 28.5650, -81.3290, 'Lake Baldwin Ln'],
            ['Lake Nona', 28.3900, -81.2600, 'Narcoossee Rd'],
        ]],
        'Chicago' => ['state' => 'Illinois', 'abbr' => 'IL', 'neighborhoods' => [
            ['Lincoln Park', 41.9210, -87.6480, 'N Clark St'],
            ['Wicker Park', 41.9090, -87.6770, 'N Damen Ave'],
            ['Logan Square', 41.9230, -87.7090, 'N Milwaukee Ave'],
            ['West Loop', 41.8830, -87.6500, 'W Randolph St'],
        ]],
        'San Diego' => ['state' => 'California', 'abbr' => 'CA', 'neighborhoods' => [
            ['North Park', 32.7480, -117.1300, 'University Ave'],
            ['Hillcrest', 32.7480, -117.1640, 'Washington St'],
            ['Mission Hills', 32.7520, -117.1840, 'Fort Stockton Dr'],
            ['South Park', 32.7240, -117.1290, 'Juniper St'],
        ]],
    ];

    public function run(): void
    {
        foreach (self::CITIES as $cityName => $city) {
            $region = Region::firstOrCreate(
                ['slug' => Str::slug($city['state'])],
                ['name' => $city['state']],
            );

            City::firstOrCreate(
                ['region_id' => $region->id, 'name' => $cityName],
                ['slug' => Str::slug($cityName)],
            );
        }
    }
}
