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
        Schema::create('flight_booking_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();

            // Payment Type
            $table->enum('type', [
                'payment',
                'refund',
                'chargeback',
                'adjustment',
                'penalty',
                'waiver',
            ])->default('payment');

            // Payment Method
            $table->enum('method', [
                'cash',
                'bank_transfer',
                'credit_card',
                'debit_card',
                'bkash',
                'nagad',
                'rocket',
                'upay',
                'paypal',
                'stripe',
                'points',
                'credit_balance',
                'cheque',
                'corporate_credit',
                'agent_credit',
                'other',
            ]);
            $table->string('method_details')->nullable()->comment('Card last 4 digits, bank name, etc.');

            // Amount
            $table->decimal('amount', 14, 2);
            $table->string('currency_code', 3)->default('BDT');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('amount_in_base_currency', 14, 2)->nullable();

            // Fee/Charges
            $table->decimal('processing_fee', 10, 2)->default(0);
            $table->decimal('gateway_fee', 10, 2)->default(0);
            $table->decimal('net_amount', 14, 2)->comment('Amount after fees');

            // Status
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded',
            ])->default('pending');

            // Gateway Information
            $table->string('gateway_name')->nullable();
            $table->string('transaction_id')->nullable()->comment('Gateway transaction ID');
            $table->string('reference_number')->nullable()->comment('Internal reference');
            $table->string('authorization_code')->nullable();
            $table->json('gateway_response')->nullable();

            // Card Details (for card payments) - stored securely/masked
            $table->string('card_type')->nullable()->comment('Visa, Mastercard, etc.');
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_holder_name')->nullable();

            // Bank Details (for bank transfers)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('verification_notes')->nullable();

            // Receipt/Invoice
            $table->string('receipt_number')->nullable();
            $table->string('invoice_number')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('failure_reason')->nullable();

            // Refund Details (if type is refund)
            $table->foreignId('original_payment_id')->nullable();
            $table->foreign('original_payment_id')->references('id')->on('flight_booking_payments')->nullOnDelete();
            $table->text('refund_reason')->nullable();

            // Audit
            $table->foreignId('collected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'type']);
            $table->index(['booking_id', 'status']);
            $table->index('transaction_id');
            $table->index('reference_number');
            $table->index(['method', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_payments');
    }
};
