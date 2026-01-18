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
        Schema::create('flight_booking_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();
            $table->foreignId('segment_id')->constrained('flight_booking_segments')->cascadeOnDelete();
            $table->foreignId('passenger_id')->constrained('flight_booking_passengers')->cascadeOnDelete();

            // Seat Details
            $table->string('seat_number', 5)->comment('e.g., 12A');
            $table->enum('seat_type', [
                'standard',
                'window',
                'aisle',
                'middle',
                'exit_row',
                'extra_legroom',
                'bulkhead',
                'bassinet',
                'couple',
            ])->default('standard');
            $table->enum('seat_class', ['economy', 'premium_economy', 'business', 'first'])->default('economy');

            // Seat Characteristics
            $table->boolean('is_window')->default(false);
            $table->boolean('is_aisle')->default(false);
            $table->boolean('is_exit_row')->default(false);
            $table->boolean('has_extra_legroom')->default(false);
            $table->boolean('is_reclinable')->default(true);
            $table->boolean('has_power_outlet')->default(false);
            $table->boolean('has_entertainment_screen')->default(false);

            // Pricing
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');
            $table->boolean('is_complimentary')->default(false);

            // Status
            $table->enum('status', [
                'selected',
                'confirmed',
                'checked_in',
                'cancelled',
            ])->default('selected');

            // Supplier Reference
            $table->string('supplier_seat_id')->nullable();
            $table->string('confirmation_number')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'segment_id']);
            $table->index(['segment_id', 'passenger_id']);
            $table->unique(['segment_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_seats');
    }
};
