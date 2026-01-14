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
        Schema::create('hotel_booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('hotel_bookings')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('hotel_rooms')->restrictOnDelete();
            $table->foreignId('rate_plan_id')->nullable()->constrained('hotel_rate_plans')->nullOnDelete();
            $table->string('supplier_room_id')->nullable()->comment('Room ID from supplier');
            $table->string('room_number')->nullable()->comment('Assigned room number');

            // Dates (can differ from main booking for split stays)
            $table->date('check_in_date');
            $table->date('check_out_date');

            // Occupancy
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('infants')->default(0);

            // Meal Plan
            $table->enum('meal_plan', [
                'room_only',
                'breakfast',
                'half_board',
                'full_board',
                'all_inclusive',
            ])->nullable();

            // Rates Breakdown
            $table->json('daily_rates')->nullable()->comment('Rate per night: [{date, rate, cost}]');
            $table->decimal('room_rate', 12, 2)->default(0)->comment('Total room rate');
            $table->decimal('extra_adult_charges', 12, 2)->default(0);
            $table->decimal('extra_child_charges', 12, 2)->default(0);
            $table->decimal('meal_charges', 12, 2)->default(0);
            $table->decimal('extra_charges', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('cost_amount', 12, 2)->default(0);

            // Requests
            $table->text('special_requests')->nullable();
            $table->enum('bed_preference', ['king', 'queen', 'twin', 'double', 'no_preference'])->nullable();
            $table->enum('floor_preference', ['high', 'low', 'no_preference'])->nullable();
            $table->boolean('smoking_room')->default(false);
            $table->boolean('accessible_room')->default(false);
            $table->boolean('connecting_room')->default(false);

            // Status
            $table->enum('status', [
                'reserved',
                'confirmed',
                'assigned',
                'checked_in',
                'checked_out',
                'cancelled',
            ])->default('reserved');

            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index(['room_id', 'check_in_date', 'check_out_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_rooms');
    }
};
