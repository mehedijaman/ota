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
        Schema::create('flight_fare_classes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Class Identification
            $table->string('name')->comment('e.g., Economy Flex, Business Saver');
            $table->string('code', 10)->comment('Fare basis code letter');
            $table->foreignId('airline_id')->nullable()->constrained('flight_airlines')->cascadeOnDelete();

            // Cabin Class
            $table->enum('cabin_class', [
                'first',
                'business',
                'premium_economy',
                'economy',
            ])->default('economy');

            // Fare Type
            $table->enum('fare_type', [
                'published',      // Standard published fare
                'private',        // Negotiated/corporate
                'promo',          // Promotional
                'consolidator',   // Net fare
                'award',          // Miles redemption
            ])->default('published');

            // Baggage
            $table->unsignedTinyInteger('checked_bags_included')->default(0);
            $table->unsignedTinyInteger('carry_on_bags_included')->default(1);
            $table->unsignedTinyInteger('checked_bag_weight_kg')->default(23);
            $table->unsignedTinyInteger('carry_on_weight_kg')->default(7);
            $table->json('baggage_details')->nullable()->comment('Detailed baggage policy');

            // Flexibility
            $table->boolean('is_refundable')->default(false);
            $table->boolean('is_changeable')->default(true);
            $table->boolean('is_upgradable')->default(true);
            $table->decimal('change_fee', 10, 2)->nullable();
            $table->decimal('cancellation_fee', 10, 2)->nullable();
            $table->decimal('no_show_fee', 10, 2)->nullable();
            $table->unsignedSmallInteger('change_allowed_until_hours')->nullable()->comment('Hours before departure');
            $table->unsignedSmallInteger('cancel_allowed_until_hours')->nullable();

            // Benefits
            $table->boolean('seat_selection_free')->default(false);
            $table->boolean('priority_boarding')->default(false);
            $table->boolean('lounge_access')->default(false);
            $table->boolean('fast_track_security')->default(false);
            $table->boolean('extra_legroom_available')->default(false);
            $table->unsignedTinyInteger('mileage_earning_percent')->default(100);
            $table->json('additional_benefits')->nullable();

            // Booking Rules
            $table->unsignedSmallInteger('min_advance_booking_hours')->nullable();
            $table->unsignedSmallInteger('max_advance_booking_days')->nullable();
            $table->unsignedTinyInteger('min_stay_days')->nullable();
            $table->unsignedTinyInteger('max_stay_days')->nullable();
            $table->boolean('requires_round_trip')->default(false);

            // Display
            $table->unsignedTinyInteger('display_order')->default(0);
            $table->string('badge_text')->nullable()->comment('e.g., Best Value, Most Flexible');
            $table->string('badge_color', 7)->nullable();
            $table->text('description')->nullable();
            $table->json('terms_conditions')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index('airline_id');
            $table->index('cabin_class');
            $table->index('fare_type');
            $table->index(['is_active', 'display_order']);
            $table->unique(['airline_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_fare_classes');
    }
};
