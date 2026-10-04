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
            $table->foreignId('city_id')->nullable()->after('city')->constrained('cities');
        });

        DB::table('cities')->get(['id', 'name'])->each(function ($city) {
            DB::table('properties')->where('city', $city->name)->update(['city_id' => $city->id]);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('city_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
        });
    }
};
