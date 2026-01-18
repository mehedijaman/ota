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
        Schema::create('flight_booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();

            // Cancellation Type
            $table->enum('type', [
                'full',               // Complete booking cancellation
                'partial',            // Some segments/passengers
                'airline_initiated',  // Airline cancelled the flight
                'schedule_change',    // Due to schedule change
                'void',               // Void before ticketing
            ])->default('full');

            // Reason
            $table->enum('reason_category', [
                'customer_request',
                'schedule_change',
                'medical',
                'visa_rejected',
                'death',
                'airline_cancelled',
                'duplicate',
                'fraud',
                'no_payment',
                'other',
            ]);
            $table->text('reason_details');
            $table->text('customer_comments')->nullable();

            // Affected Items
            $table->json('cancelled_segments')->nullable()->comment('Segment IDs if partial');
            $table->json('cancelled_passengers')->nullable()->comment('Passenger IDs if partial');

            // Financial
            $table->decimal('booking_amount', 14, 2);
            $table->decimal('airline_penalty', 14, 2)->default(0);
            $table->decimal('service_charge', 14, 2)->default(0)->comment('OTA cancellation fee');
            $table->decimal('total_deduction', 14, 2)->default(0);
            $table->decimal('refundable_amount', 14, 2)->default(0);
            $table->decimal('refunded_amount', 14, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');

            // Refund Status
            $table->enum('refund_status', [
                'not_applicable',
                'pending_approval',
                'approved',
                'processing',
                'refunded_to_customer',
                'refunded_to_wallet',
                'refunded_to_card',
                'refunded_to_bank',
                'rejected',
                'partially_refunded',
            ])->default('pending_approval');
            $table->enum('refund_method', [
                'original_payment',
                'bank_transfer',
                'wallet',
                'cash',
                'cheque',
                'credit',
            ])->nullable();

            // Refund Processing
            $table->timestamp('refund_approved_at')->nullable();
            $table->timestamp('refund_processed_at')->nullable();
            $table->foreignId('refund_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('refund_reference')->nullable();
            $table->json('refund_details')->nullable();

            // Airline/Supplier
            $table->string('airline_confirmation')->nullable()->comment('Airline cancellation confirm');
            $table->string('supplier_cancellation_id')->nullable();
            $table->json('supplier_response')->nullable();

            // Documentation
            $table->json('supporting_documents')->nullable()->comment('Medical cert, visa rejection, etc.');

            // Status
            $table->enum('status', [
                'requested',
                'pending_airline',
                'confirmed',
                'rejected',
                'completed',
            ])->default('requested');

            // Timestamps
            $table->timestamp('requested_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Audit
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index('refund_status');
            $table->index('type');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_cancellations');
    }
};
