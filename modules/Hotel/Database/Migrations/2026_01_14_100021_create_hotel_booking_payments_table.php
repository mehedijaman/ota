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
        Schema::create('hotel_booking_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('hotel_bookings')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('payment_method')->nullable()->comment('bkash, nagad, card, bank, cash, etc.');
            $table->string('payment_gateway')->nullable()->comment('sslcommerz, bkash, stripe, etc.');
            $table->string('gateway_transaction_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded',
                'partially_refunded',
            ])->default('pending');
            $table->enum('type', [
                'deposit',
                'partial',
                'full',
                'additional',
                'damage_deposit',
                'refund',
            ])->default('full');

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();

            // Refund Information
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->string('refund_transaction_id')->nullable();

            // Bank Transfer Details
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_reference')->nullable();
            $table->date('bank_deposit_date')->nullable();

            // Card Details (masked)
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_holder_name')->nullable();

            // Gateway Response
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Receipt/Invoice
            $table->string('receipt_number')->nullable();
            $table->string('invoice_number')->nullable();

            // User Tracking
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index('status');
            $table->index('paid_at');
            $table->index('payment_method');
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_payments');
    }
};
