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
        Schema::create('flight_booking_ancillaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();
            $table->foreignId('segment_id')->nullable()->constrained('flight_booking_segments')->cascadeOnDelete();
            $table->foreignId('passenger_id')->nullable()->constrained('flight_booking_passengers')->cascadeOnDelete();

            // Ancillary Type
            $table->enum('type', [
                'baggage',
                'meal',
                'seat',
                'lounge',
                'priority_boarding',
                'fast_track',
                'insurance',
                'transfer',
                'car_rental',
                'hotel',
                'wifi',
                'entertainment',
                'pet',
                'sports_equipment',
                'musical_instrument',
                'unaccompanied_minor',
                'other',
            ]);
            $table->string('subtype')->nullable()->comment('e.g., extra_bag, premium_meal');

            // Description
            $table->string('name');
            $table->text('description')->nullable();

            // Quantity & Units
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->string('unit')->nullable()->comment('kg, piece, etc.');
            $table->unsignedSmallInteger('weight_kg')->nullable()->comment('For baggage');

            // Pricing
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('currency_code', 3)->default('BDT');

            // Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'used',
            ])->default('pending');

            // Supplier Reference
            $table->string('supplier_ancillary_id')->nullable();
            $table->string('confirmation_number')->nullable();
            $table->json('supplier_data')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'type']);
            $table->index(['segment_id', 'passenger_id']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_ancillaries');
    }
};
