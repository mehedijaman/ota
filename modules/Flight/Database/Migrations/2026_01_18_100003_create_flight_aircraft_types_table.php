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
        Schema::create('flight_aircraft_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Aircraft Identification
            $table->string('name')->comment('e.g., Boeing 777-300ER');
            $table->string('iata_code', 3)->unique()->comment('IATA type designator');
            $table->string('icao_code', 4)->nullable()->comment('ICAO type designator');
            $table->string('manufacturer')->comment('Boeing, Airbus, etc.');
            $table->string('model');
            $table->string('variant')->nullable()->comment('e.g., -300ER, neo');

            // Classification
            $table->enum('category', [
                'wide_body',
                'narrow_body',
                'regional_jet',
                'turboprop',
                'business_jet',
                'cargo',
            ])->default('narrow_body');
            $table->enum('engine_type', ['jet', 'turboprop', 'piston'])->default('jet');
            $table->unsignedTinyInteger('engine_count')->default(2);

            // Capacity
            $table->unsignedSmallInteger('typical_seats')->nullable();
            $table->unsignedSmallInteger('max_seats')->nullable();
            $table->unsignedTinyInteger('first_class_seats')->nullable();
            $table->unsignedTinyInteger('business_class_seats')->nullable();
            $table->unsignedSmallInteger('economy_class_seats')->nullable();

            // Performance
            $table->unsignedSmallInteger('range_km')->nullable();
            $table->unsignedSmallInteger('cruise_speed_kmh')->nullable();
            $table->unsignedSmallInteger('max_altitude_ft')->nullable();
            $table->decimal('wingspan_m', 5, 2)->nullable();
            $table->decimal('length_m', 5, 2)->nullable();

            // Images
            $table->string('image')->nullable();
            $table->string('seat_map_image')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index('manufacturer');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_aircraft_types');
    }
};
