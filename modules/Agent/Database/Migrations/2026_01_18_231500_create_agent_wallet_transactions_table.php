<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->cascadeOnDelete();
            
            $table->enum('type', ['deposit', 'payment', 'refund', 'adjustment', 'withdraw']);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            
            $table->string('payment_method')->nullable()->comment('gateway, bank_transfer, manual, wallet');
            $table->string('payment_gateway')->nullable();
            $table->string('transaction_id')->nullable()->comment('Transaction ID from gateway or bank');
            
            $table->json('details')->nullable();
            $table->string('reference')->nullable()->comment('Internal reference e.g. Booking ID');
            
            $table->enum('status', ['pending', 'completed', 'failed', 'rejected', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['agent_id', 'type']);
            $table->index(['agent_id', 'status']);
            $table->index('transaction_id');
            $table->index('reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_wallet_transactions');
    }
};
