<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * A populated marketplace for local development and the live demo:
 * agents with listings and photos, buyers with favorites and inquiries,
 * payments, subscriptions, refunds and moderation work for admins.
 *
 *   php artisan migrate:fresh --seed
 *   php artisan db:seed --class=DemoSeeder
 *
 * The data is generated from a fixed random seed, so every run produces the
 * same marketplace (dates are relative to today).
 */
class DemoSeeder extends Seeder
{
    public const RANDOM_SEED = 2026;

    public function run(): void
    {
        if (Amenity::query()->doesntExist()) {
            $this->call(DatabaseSeeder::class);
        }

        mt_srand(self::RANDOM_SEED);

        Model::unguarded(fn () => $this->call([
            LocationSeeder::class,
            DemoUserSeeder::class,
            DemoListingSeeder::class,
            DemoActivitySeeder::class,
        ]));
    }
}
