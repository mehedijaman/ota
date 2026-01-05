<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_credit_reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('sms_credit_lot_id')->constrained('sms_credit_lots')->cascadeOnDelete();
            $table->foreignId('sms_message_id')->constrained('sms_messages')->cascadeOnDelete();

            $table->unsignedInteger('credits');
            $table->timestamps();
        });

        Schema::table('sms_credit_reservations', function (Blueprint $table) {
            $table->index(['tenant_id', 'sms_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_credit_reservations');
    }
};
