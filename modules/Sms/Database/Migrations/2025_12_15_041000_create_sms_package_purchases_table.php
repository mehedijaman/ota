<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_package_purchases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sms_package_id')->constrained('sms_packages')->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('total_credits');
            $table->decimal('total_price', 12, 2);
            $table->string('currency', 10)->default('BDT');

            $table->string('status', 30)->default('pending'); // pending|approved|rejected|cancelled

            $table->string('payment_method', 30)->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('payment_notes')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });

        Schema::table('sms_package_purchases', function (Blueprint $table) {
            $table->index(['tenant_id', 'status']);
            $table->index(['sms_package_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_package_purchases');
    }
};
