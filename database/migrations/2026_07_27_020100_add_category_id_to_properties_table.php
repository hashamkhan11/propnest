<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('property_type')->constrained('property_categories');
        });

        // Backfill every existing property from its current property_type string.
        DB::table('property_categories')->get(['id', 'slug'])->each(function ($category) {
            DB::table('properties')->where('property_type', $category->slug)->update(['category_id' => $category->id]);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
