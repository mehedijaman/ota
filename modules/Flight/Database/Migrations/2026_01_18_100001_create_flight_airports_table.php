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
        Schema::create('flight_airports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Location - Reference to nnjeim/world countries
            $table->foreignId('country_id')->constrained('countries')->restrictOnDelete();

            // Airport Identification
            $table->string('name');
            $table->string('name_local')->nullable()->comment('Local language name');
            $table->string('iata_code', 3)->unique()->comment('IATA 3-letter code');
            $table->string('icao_code', 4)->nullable()->comment('ICAO 4-letter code');
            $table->string('faa_code', 3)->nullable()->comment('FAA identifier (US airports)');

            // Location Details
            $table->string('city');
            $table->string('city_code', 3)->nullable()->comment('City IATA code if different');
            $table->string('state')->nullable();
            $table->string('region')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('timezone')->comment('IANA timezone identifier');
            $table->smallInteger('utc_offset_minutes')->default(0);

            // Airport Classification
            $table->enum('type', [
                'large_airport',
                'medium_airport',
                'small_airport',
                'heliport',
                'seaplane_base',
                'closed',
            ])->default('medium_airport');
            $table->boolean('is_international')->default(false);
            $table->boolean('is_domestic')->default(true);
            $table->unsignedTinyInteger('terminals_count')->nullable();

            // Operational
            $table->boolean('is_active')->default(true);
            $table->boolean('is_hub')->default(false)->comment('Major airline hub');
            $table->boolean('is_popular')->default(false)->comment('Popular destination');
            $table->unsignedInteger('annual_passengers')->nullable()->comment('Annual passenger traffic');

            // Contact & Info
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // Search Optimization
            $table->string('search_keywords')->nullable()->comment('Additional search terms');
            $table->unsignedInteger('search_priority')->default(0)->comment('Higher = shown first');

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('country_id');
            $table->index('city');
            $table->index('city_code');
            $table->index(['is_active', 'is_popular']);
            $table->index(['latitude', 'longitude']);
            $table->index('type');
            // Note: Add fullText index for MySQL: $table->fullText(['name', 'city', 'iata_code', 'search_keywords']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_airports');
    }
};
