<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_credit_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('sms_package_purchase_id')->nullable()->constrained('sms_package_purchases')->nullOnDelete();

            $table->unsignedInteger('credits_total');
            $table->unsignedInteger('credits_used')->default(0);
            $table->unsignedInteger('credits_reserved')->default(0);

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::table('sms_credit_lots', function (Blueprint $table) {
            $table->index(['tenant_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_credit_lots');
    }
};
