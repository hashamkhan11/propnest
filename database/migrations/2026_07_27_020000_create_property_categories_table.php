<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed one row per existing PropertyType enum case so property_type
        // values already stored on properties keep matching a category slug.
        $now = now();
        DB::table('property_categories')->insert([
            ['name' => 'House', 'slug' => 'house', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Apartment', 'slug' => 'apartment', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Condo', 'slug' => 'condo', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Townhouse', 'slug' => 'townhouse', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Land', 'slug' => 'land', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Commercial', 'slug' => 'commercial', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('property_categories');
    }
};
