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
        Schema::create('visa_application_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('visa_applications')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('payment_method')->nullable()->comment('bkash, nagad, card, bank, cash, etc.');
            $table->string('payment_gateway')->nullable()->comment('sslcommerz, bkash, stripe, etc.');
            $table->string('gateway_transaction_id')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('type', ['deposit', 'partial', 'full', 'additional', 'refund'])->default('full');

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

            // Gateway Response
            $table->json('gateway_response')->nullable();
            $table->json('metadata')->nullable();

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Receipt/Invoice
            $table->string('receipt_number')->nullable();
            $table->string('invoice_number')->nullable();

            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('refunded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['application_id', 'status']);
            $table->index('status');
            $table->index('paid_at');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_application_payments');
    }
};
