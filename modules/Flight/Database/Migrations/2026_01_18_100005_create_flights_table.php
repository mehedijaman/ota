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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Supplier Reference
            $table->foreignId('supplier_id')->nullable()->constrained('flight_suppliers')->nullOnDelete();
            $table->string('supplier_flight_id')->nullable()->comment('External flight ID from supplier');

            // Airline & Flight Number
            $table->foreignId('airline_id')->constrained('flight_airlines')->restrictOnDelete();
            $table->foreignId('operating_airline_id')->nullable()->constrained('flight_airlines')->nullOnDelete()->comment('For codeshare flights');
            $table->string('flight_number', 10)->comment('e.g., BG435');
            $table->string('operating_flight_number', 10)->nullable()->comment('Actual operating flight number');

            // Route
            $table->foreignId('departure_airport_id')->constrained('flight_airports')->restrictOnDelete();
            $table->foreignId('arrival_airport_id')->constrained('flight_airports')->restrictOnDelete();
            $table->string('departure_terminal')->nullable();
            $table->string('arrival_terminal')->nullable();
            $table->string('departure_gate')->nullable();
            $table->string('arrival_gate')->nullable();

            // Schedule
            $table->time('scheduled_departure_time');
            $table->time('scheduled_arrival_time');
            $table->unsignedSmallInteger('duration_minutes');
            $table->boolean('crosses_midnight')->default(false)->comment('Arrives next day');
            $table->tinyInteger('day_offset')->default(0)->comment('Days difference arrival vs departure');

            // Operating Days (for scheduled flights)
            $table->boolean('operates_monday')->default(true);
            $table->boolean('operates_tuesday')->default(true);
            $table->boolean('operates_wednesday')->default(true);
            $table->boolean('operates_thursday')->default(true);
            $table->boolean('operates_friday')->default(true);
            $table->boolean('operates_saturday')->default(true);
            $table->boolean('operates_sunday')->default(true);
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();

            // Aircraft
            $table->foreignId('aircraft_type_id')->nullable()->constrained('flight_aircraft_types')->nullOnDelete();
            $table->string('aircraft_registration')->nullable();

            // Stops
            $table->unsignedTinyInteger('stops_count')->default(0);
            $table->json('via_airports')->nullable()->comment('Stopover airport IDs and durations');

            // Flight Type
            $table->enum('flight_type', [
                'scheduled',
                'charter',
                'cargo',
                'ferry',
                'training',
            ])->default('scheduled');
            $table->boolean('is_codeshare')->default(false);
            $table->boolean('is_red_eye')->default(false)->comment('Overnight flight');

            // Services
            $table->json('cabin_services')->nullable()->comment('Meals, entertainment, wifi');
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_power_outlet')->default(false);
            $table->boolean('has_entertainment')->default(false);
            $table->enum('meal_service', ['none', 'snack', 'light_meal', 'full_meal', 'premium'])->default('none');

            // Status
            $table->boolean('is_active')->default(true);
            $table->enum('status', [
                'scheduled',
                'delayed',
                'cancelled',
                'departed',
                'in_air',
                'landed',
                'arrived',
                'diverted',
            ])->default('scheduled');

            // Pricing Defaults
            $table->decimal('base_price', 12, 2)->nullable()->comment('Base economy price');
            $table->string('currency_code', 3)->default('BDT');

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['supplier_id', 'supplier_flight_id']);
            $table->index('airline_id');
            $table->index(['departure_airport_id', 'arrival_airport_id']);
            $table->index('flight_number');
            $table->index(['effective_from', 'effective_until']);
            $table->index(['is_active', 'status']);
            $table->index('scheduled_departure_time');
            $table->unique(['airline_id', 'flight_number', 'effective_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
