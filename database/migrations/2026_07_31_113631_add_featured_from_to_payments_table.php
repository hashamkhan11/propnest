<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('featured_from')->nullable()->after('amount');
        });

        // Backfill existing rows: featured_until is always set to
        // created_at->addDays(duration) at purchase time, so created_at is
        // an accurate stand-in for the missing start date.
        DB::table('payments')->whereNull('featured_from')->update([
            'featured_from' => DB::raw('created_at'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('featured_from');
        });
    }
};
