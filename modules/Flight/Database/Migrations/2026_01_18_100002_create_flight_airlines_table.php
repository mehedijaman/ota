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
        Schema::create('flight_airlines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Reference to nnjeim/world countries
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();

            // Airline Identification
            $table->string('name');
            $table->string('name_local')->nullable()->comment('Local language name');
            $table->string('iata_code', 2)->unique()->comment('IATA 2-letter code');
            $table->string('icao_code', 3)->nullable()->comment('ICAO 3-letter code');
            $table->string('call_sign')->nullable()->comment('Radio telephony call sign');
            $table->string('accounting_code', 3)->nullable()->comment('IATA accounting code');
            $table->string('prefix_code', 3)->nullable()->comment('Ticket number prefix');

            // Classification
            $table->enum('type', [
                'scheduled',         // Full-service scheduled airline
                'low_cost',          // Low-cost carrier
                'charter',           // Charter airline
                'cargo',             // Cargo airline
                'regional',          // Regional carrier
                'commuter',          // Commuter airline
                'private',           // Private aviation
            ])->default('scheduled');
            $table->enum('alliance', [
                'star_alliance',
                'oneworld',
                'skyteam',
                'none',
            ])->default('none');

            // Branding
            $table->string('logo')->nullable();
            $table->string('logo_small')->nullable()->comment('Icon/favicon size');
            $table->string('primary_color', 7)->nullable()->comment('Brand hex color');
            $table->string('secondary_color', 7)->nullable();

            // Operational Info
            $table->foreignId('hub_airport_id')->nullable()->constrained('flight_airports')->nullOnDelete();
            $table->year('founded_year')->nullable();
            $table->unsignedSmallInteger('fleet_size')->nullable();
            $table->unsignedSmallInteger('destinations_count')->nullable();
            $table->string('headquarters')->nullable();

            // Contact
            $table->string('website')->nullable();
            $table->string('customer_service_phone')->nullable();
            $table->string('customer_service_email')->nullable();
            $table->string('baggage_phone')->nullable();

            // Policies
            $table->json('baggage_policy')->nullable()->comment('Default baggage allowances');
            $table->json('cancellation_policy')->nullable();
            $table->json('change_policy')->nullable();
            $table->boolean('allows_web_checkin')->default(true);
            $table->unsignedTinyInteger('checkin_hours_before')->default(24);
            $table->unsignedTinyInteger('checkin_minutes_before')->default(60)->comment('Last checkin before departure');

            // Financial/Business
            $table->decimal('default_commission_rate', 5, 2)->nullable();
            $table->decimal('default_markup_rate', 5, 2)->nullable();
            $table->boolean('is_preferred_partner')->default(false);
            $table->boolean('has_direct_contract')->default(false);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->enum('operating_status', ['active', 'suspended', 'merged', 'defunct'])->default('active');

            // Ratings
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('description')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('country_id');
            $table->index(['is_active', 'is_featured']);
            $table->index('type');
            $table->index('alliance');
            $table->index('operating_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_airlines');
    }
};
