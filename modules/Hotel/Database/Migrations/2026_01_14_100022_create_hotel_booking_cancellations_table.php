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
        Schema::create('hotel_booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('hotel_bookings')->cascadeOnDelete();
            $table->foreignId('policy_id')->nullable()->constrained('hotel_cancellation_policies')->nullOnDelete();
            $table->string('cancellation_number')->unique();

            // Users
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'refund_pending',
                'refunded',
                'partially_refunded',
            ])->default('pending');

            // Reason
            $table->text('reason')->nullable();
            $table->enum('reason_category', [
                'change_of_plans',
                'found_better_deal',
                'personal_emergency',
                'medical',
                'travel_restrictions',
                'natural_disaster',
                'hotel_issue',
                'wrong_booking',
                'duplicate_booking',
                'other',
            ])->default('other');

            // Timing
            $table->unsignedSmallInteger('hours_before_checkin')->nullable();

            // Financial
            $table->decimal('refund_percentage', 5, 2)->default(0);
            $table->decimal('original_amount', 12, 2)->default(0);
            $table->decimal('cancellation_fee', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->string('refund_method')->nullable();

            // Supplier Integration
            $table->string('supplier_cancellation_id')->nullable();
            $table->json('supplier_response')->nullable();
            $table->boolean('supplier_confirmed')->default(false);

            // Notes
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Timestamps
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('supplier_notified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index('status');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_cancellations');
    }
};
