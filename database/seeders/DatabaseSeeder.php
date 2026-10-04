<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Admin User',
            'email' => 'admin@propnest.test',
        ]);

        User::factory(3)->agent()->create();

        User::factory(5)->create();

        collect(['Pool', 'Garage', 'Air Conditioning', 'Balcony', 'Garden', 'Parking', 'Furnished', 'Pet Friendly', 'Security', 'Elevator'])
            ->each(fn (string $name) => Amenity::firstOrCreate(['name' => $name]));
    }
}
