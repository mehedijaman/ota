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
        Schema::create('hotel_rate_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('cancellation_policy_id')->nullable()->constrained('hotel_cancellation_policies')->nullOnDelete();
            $table->string('supplier_rate_plan_id')->nullable()->comment('External ID from supplier');

            // Basic Information
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();

            // Meal Plan
            $table->enum('meal_plan', [
                'room_only',
                'breakfast',
                'half_board',
                'full_board',
                'all_inclusive',
                'breakfast_lunch',
                'breakfast_dinner',
                'self_catering',
            ])->default('room_only');

            // Policies
            $table->boolean('is_refundable')->default(true);
            $table->boolean('is_pay_at_hotel')->default(false);
            $table->boolean('requires_prepayment')->default(false);
            $table->decimal('prepayment_percentage', 5, 2)->nullable();

            // Booking Restrictions
            $table->unsignedSmallInteger('advance_booking_min')->nullable()->comment('Min days before check-in');
            $table->unsignedSmallInteger('advance_booking_max')->nullable()->comment('Max days before check-in');
            $table->unsignedTinyInteger('min_nights')->nullable();
            $table->unsignedTinyInteger('max_nights')->nullable();
            $table->unsignedTinyInteger('min_rooms')->nullable();
            $table->unsignedTinyInteger('max_rooms')->nullable();

            // Validity
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();

            // Applicable Days (bitmask or JSON)
            $table->json('applicable_days')->nullable()->comment('Days of week: [0,1,2,3,4,5,6]');

            // Pricing
            $table->decimal('markup_percentage', 5, 2)->nullable()->comment('Rate plan specific markup');
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('Rate plan discount');

            // Status
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'is_active']);
            $table->index('supplier_rate_plan_id');
            $table->index(['valid_from', 'valid_to']);
            $table->index('meal_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rate_plans');
    }
};
