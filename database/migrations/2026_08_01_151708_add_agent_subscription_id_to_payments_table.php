<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('agent_subscription_id')->nullable()->after('featured_pricing_tier_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('property_id')->nullable()->change();
            $table->timestamp('featured_until')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agent_subscription_id');
            $table->timestamp('featured_until')->nullable(false)->change();
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
        });
    }
};
