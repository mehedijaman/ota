<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->boolean('sale_sms_enabled')->default(false);
            $table->text('sale_sms_template')->nullable();

            $table->boolean('invoice_sms_enabled')->default(false);
            $table->text('invoice_sms_template')->nullable();

            $table->boolean('due_reminder_sms_enabled')->default(false);
            $table->text('due_reminder_template')->nullable();

            $table->timestamps();

            $table->unique('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_tenant_settings');
    }
};
