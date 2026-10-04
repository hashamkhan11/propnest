<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['region_id', 'name']);
        });

        $now = now();
        $unspecifiedRegionId = DB::table('regions')->insertGetId([
            'name' => 'Unspecified',
            'slug' => 'unspecified',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Backfill one City row per distinct existing free-text city value.
        DB::table('properties')->distinct()->pluck('city')->each(function (string $cityName) use ($unspecifiedRegionId, $now) {
            DB::table('cities')->insert([
                'region_id' => $unspecifiedRegionId,
                'name' => $cityName,
                'slug' => Str::slug($cityName),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
    }
};
