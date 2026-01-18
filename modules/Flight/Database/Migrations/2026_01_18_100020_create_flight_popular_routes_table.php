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
        Schema::create('flight_popular_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_airport_id')->constrained('flight_airports')->cascadeOnDelete();
            $table->foreignId('destination_airport_id')->constrained('flight_airports')->cascadeOnDelete();

            // Route Info
            $table->string('origin_code', 3);
            $table->string('destination_code', 3);
            $table->string('route_name')->comment('DAC to DXB');

            // Statistics
            $table->unsignedInteger('search_count')->default(0);
            $table->unsignedInteger('booking_count')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);

            // Pricing
            $table->decimal('avg_price', 12, 2)->nullable();
            $table->decimal('min_price', 12, 2)->nullable();
            $table->decimal('max_price', 12, 2)->nullable();
            $table->string('currency_code', 3)->default('BDT');

            // Popularity
            $table->unsignedSmallInteger('rank')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            // Image/Display
            $table->string('image')->nullable();
            $table->text('description')->nullable();

            // Flight Duration
            $table->unsignedSmallInteger('avg_duration_minutes')->nullable();
            $table->unsignedSmallInteger('direct_flight_duration')->nullable();

            // Timestamps
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['origin_airport_id', 'destination_airport_id']);
            $table->index(['is_active', 'rank']);
            $table->index('is_featured');
            $table->index('booking_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_popular_routes');
    }
};
