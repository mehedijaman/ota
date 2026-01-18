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
        Schema::create('flight_booking_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();

            // Status Change
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->string('status_type')->default('booking')->comment('booking, payment, ticketing');

            // Context
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable()->comment('Additional context data');

            // Source
            $table->enum('source', [
                'system',
                'user',
                'customer',
                'agent',
                'supplier',
                'api',
                'schedule_change',
            ])->default('user');

            // Audit
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'created_at']);
            $table->index('new_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_status_histories');
    }
};
