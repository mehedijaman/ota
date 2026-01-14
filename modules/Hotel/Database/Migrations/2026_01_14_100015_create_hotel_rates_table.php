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
        Schema::create('hotel_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('hotel_rooms')->cascadeOnDelete();
            $table->foreignId('rate_plan_id')->constrained('hotel_rate_plans')->cascadeOnDelete();
            $table->date('date');

            // Rates
            $table->decimal('base_rate', 12, 2)->default(0)->comment('Base room rate');
            $table->decimal('extra_adult_rate', 12, 2)->default(0)->comment('Per extra adult');
            $table->decimal('extra_child_rate', 12, 2)->default(0)->comment('Per extra child');
            $table->decimal('single_occupancy_rate', 12, 2)->nullable()->comment('Single use rate');
            $table->decimal('extra_bed_rate', 12, 2)->default(0);

            // Cost & Selling
            $table->decimal('cost_rate', 12, 2)->nullable()->comment('Net cost from supplier');
            $table->decimal('selling_rate', 12, 2)->nullable()->comment('Final selling rate');
            $table->decimal('rack_rate', 12, 2)->nullable()->comment('Published rate for comparison');

            // Currency
            $table->string('currency_code', 3)->default('BDT');

            // Restrictions
            $table->unsignedTinyInteger('min_stay')->nullable()->comment('Min nights override');
            $table->unsignedTinyInteger('max_stay')->nullable()->comment('Max nights override');
            $table->unsignedTinyInteger('min_advance_days')->nullable();

            // Availability Controls
            $table->boolean('is_closed')->default(false)->comment('Stop sell this date');
            $table->boolean('close_on_arrival')->default(false)->comment('No check-in on this date');
            $table->boolean('close_on_departure')->default(false)->comment('No check-out on this date');

            $table->timestamps();

            // Unique constraint
            $table->unique(['room_id', 'rate_plan_id', 'date'], 'hotel_rates_unique');

            // Indexes for fast lookups
            $table->index(['room_id', 'date']);
            $table->index(['rate_plan_id', 'date']);
            $table->index(['date', 'is_closed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rates');
    }
};
