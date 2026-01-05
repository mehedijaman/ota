<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_package_purchases', function (Blueprint $table) {
            $table->string('transaction_id', 100)->nullable()->after('payment_notes');
            $table->string('payment_status', 30)->default('unpaid')->after('transaction_id'); // unpaid|processing|paid|failed|cancelled
            $table->string('gateway_transaction_id', 120)->nullable()->after('payment_status');
            $table->json('payment_details')->nullable()->after('gateway_transaction_id');
            $table->json('gateway_response')->nullable()->after('payment_details');
            $table->timestamp('paid_at')->nullable()->after('gateway_response');
        });

        Schema::table('sms_package_purchases', function (Blueprint $table) {
            $table->index(['transaction_id']);
            $table->index(['payment_status']);
        });
    }

    public function down(): void
    {
        Schema::table('sms_package_purchases', function (Blueprint $table) {
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['payment_status']);

            $table->dropColumn([
                'transaction_id',
                'payment_status',
                'gateway_transaction_id',
                'payment_details',
                'gateway_response',
                'paid_at',
            ]);
        });
    }
};
