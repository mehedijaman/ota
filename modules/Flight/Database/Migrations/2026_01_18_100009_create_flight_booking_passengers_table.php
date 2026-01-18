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
        Schema::create('flight_booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();

            // Passenger Order
            $table->unsignedTinyInteger('passenger_number')->default(1);
            $table->enum('passenger_type', ['adult', 'child', 'infant'])->default('adult');
            $table->boolean('is_lead_passenger')->default(false);

            // Personal Information
            $table->string('title', 10)->nullable()->comment('MR, MRS, MS, MSTR, MISS');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->foreignId('nationality_id')->nullable()->constrained('countries')->nullOnDelete();

            // Passport/ID
            $table->string('passport_number')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->foreignId('passport_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('national_id')->nullable();

            // Contact (for lead passenger or if different)
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_country_code', 5)->nullable();

            // Frequent Flyer
            $table->string('frequent_flyer_airline', 2)->nullable();
            $table->string('frequent_flyer_number')->nullable();
            $table->string('tier_status')->nullable()->comment('Gold, Platinum, etc.');

            // Known Traveler Info
            $table->string('redress_number')->nullable()->comment('TSA Redress');
            $table->string('known_traveler_number')->nullable()->comment('TSA PreCheck/Global Entry');

            // Ticket Information
            $table->string('ticket_number', 20)->nullable()->comment('13-digit e-ticket number');
            $table->enum('ticket_status', [
                'not_issued',
                'issued',
                'void',
                'refunded',
                'exchanged',
                'used',
            ])->default('not_issued');
            $table->timestamp('ticketed_at')->nullable();

            // Seat Preferences
            $table->enum('seat_preference', ['window', 'aisle', 'middle', 'any'])->default('any');
            $table->boolean('prefers_front')->default(false);
            $table->boolean('prefers_exit_row')->default(false);
            $table->boolean('prefers_extra_legroom')->default(false);

            // Special Services
            $table->json('special_service_requests')->nullable()->comment('SSR codes');
            $table->enum('meal_preference', [
                'standard',
                'vegetarian',
                'vegan',
                'halal',
                'kosher',
                'gluten_free',
                'diabetic',
                'child_meal',
                'infant_meal',
                'low_fat',
                'low_sodium',
                'hindu',
                'jain',
                'seafood',
                'other',
            ])->default('standard');
            $table->text('dietary_notes')->nullable();

            // Assistance Required
            $table->boolean('requires_wheelchair')->default(false);
            $table->enum('wheelchair_type', ['wchr', 'wchs', 'wchc', 'none'])->default('none');
            $table->boolean('requires_assistance')->default(false);
            $table->text('assistance_details')->nullable();
            $table->boolean('is_unaccompanied_minor')->default(false);

            // Medical
            $table->boolean('has_medical_condition')->default(false);
            $table->text('medical_notes')->nullable();
            $table->boolean('requires_oxygen')->default(false);
            $table->boolean('traveling_with_pet')->default(false);

            // Infant Details (if passenger is infant)
            $table->foreignId('associated_adult_id')->nullable()->comment('Adult this infant is traveling with');
            $table->foreign('associated_adult_id')->references('id')->on('flight_booking_passengers')->nullOnDelete();

            // Pricing for this passenger
            $table->decimal('base_fare', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');

            // Supplier Reference
            $table->string('supplier_passenger_id')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'passenger_number']);
            $table->index('passenger_type');
            $table->index('ticket_number');
            $table->index('ticket_status');
            $table->index('passport_number');
            $table->index(['last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_passengers');
    }
};
