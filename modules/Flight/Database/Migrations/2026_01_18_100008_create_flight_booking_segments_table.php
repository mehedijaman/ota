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
        Schema::create('flight_booking_segments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();
            $table->foreignId('flight_id')->nullable()->constrained('flights')->nullOnDelete();
            $table->foreignId('fare_id')->nullable()->constrained('flight_fares')->nullOnDelete();

            // Segment Order
            $table->unsignedTinyInteger('segment_order')->default(1);
            $table->enum('segment_type', ['outbound', 'inbound', 'multi'])->default('outbound');

            // Airline & Flight
            $table->foreignId('airline_id')->constrained('flight_airlines')->restrictOnDelete();
            $table->foreignId('operating_airline_id')->nullable()->constrained('flight_airlines')->nullOnDelete();
            $table->string('flight_number', 10);
            $table->string('operating_flight_number', 10)->nullable();
            $table->boolean('is_codeshare')->default(false);

            // Route
            $table->foreignId('departure_airport_id')->constrained('flight_airports')->restrictOnDelete();
            $table->foreignId('arrival_airport_id')->constrained('flight_airports')->restrictOnDelete();
            $table->string('departure_terminal')->nullable();
            $table->string('arrival_terminal')->nullable();

            // Schedule
            $table->dateTime('departure_datetime');
            $table->dateTime('arrival_datetime');
            $table->unsignedSmallInteger('duration_minutes');
            $table->tinyInteger('day_offset')->default(0)->comment('Days difference');

            // Stops
            $table->unsignedTinyInteger('stops_count')->default(0);
            $table->json('stopover_details')->nullable();

            // Aircraft
            $table->foreignId('aircraft_type_id')->nullable()->constrained('flight_aircraft_types')->nullOnDelete();

            // Cabin & Class
            $table->enum('cabin_class', ['economy', 'premium_economy', 'business', 'first'])->default('economy');
            $table->string('booking_class', 2)->nullable()->comment('RBD - Y, B, M, etc.');
            $table->string('fare_basis', 15)->nullable()->comment('Fare basis code');

            // Baggage
            $table->string('baggage_allowance')->nullable()->comment('e.g., 2PC, 23K');
            $table->unsignedTinyInteger('checked_bags')->default(0);
            $table->unsignedTinyInteger('checked_bag_weight_kg')->default(0);

            // Status
            $table->enum('status', [
                'confirmed',
                'waitlisted',
                'on_request',
                'schedule_changed',
                'cancelled',
                'flown',
                'no_show',
            ])->default('confirmed');

            // Supplier Reference
            $table->string('supplier_segment_id')->nullable();
            $table->string('airline_confirmation')->nullable();

            // Pricing Per Segment
            $table->decimal('segment_fare', 12, 2)->default(0);
            $table->decimal('segment_tax', 12, 2)->default(0);
            $table->decimal('segment_total', 12, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');

            // Services
            $table->json('meal_service')->nullable();
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_entertainment')->default(false);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'segment_order']);
            $table->index(['departure_airport_id', 'arrival_airport_id']);
            $table->index('departure_datetime');
            $table->index('status');
            $table->index('flight_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_segments');
    }
};
