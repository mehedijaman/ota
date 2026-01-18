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
        Schema::create('flight_booking_exchanges', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('booking_id')->constrained('flight_bookings')->cascadeOnDelete();

            // Exchange Type
            $table->enum('type', [
                'date_change',
                'route_change',
                'class_upgrade',
                'passenger_name_change',
                'full_reissue',
            ]);

            // Reason
            $table->string('reason');
            $table->text('change_details');

            // Original vs New
            $table->json('original_segments')->nullable();
            $table->json('new_segments')->nullable();
            $table->date('original_departure_date')->nullable();
            $table->date('new_departure_date')->nullable();

            // Financial
            $table->decimal('original_fare', 14, 2);
            $table->decimal('new_fare', 14, 2);
            $table->decimal('fare_difference', 14, 2)->default(0);
            $table->decimal('exchange_fee', 14, 2)->default(0)->comment('Airline change fee');
            $table->decimal('service_fee', 14, 2)->default(0)->comment('OTA service fee');
            $table->decimal('total_due', 14, 2)->default(0);
            $table->decimal('total_refund', 14, 2)->default(0)->comment('If downgrade');
            $table->decimal('amount_collected', 14, 2)->default(0);
            $table->string('currency_code', 3)->default('BDT');

            // Status
            $table->enum('status', [
                'requested',
                'pending_availability',
                'pending_payment',
                'processing',
                'completed',
                'rejected',
                'cancelled',
            ])->default('requested');

            // New Ticket
            $table->string('new_pnr')->nullable();
            $table->json('new_ticket_numbers')->nullable();

            // Supplier
            $table->string('supplier_exchange_id')->nullable();
            $table->json('supplier_response')->nullable();

            // Timestamps
            $table->timestamp('requested_at');
            $table->timestamp('completed_at')->nullable();

            // Audit
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['booking_id', 'status']);
            $table->index('type');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_booking_exchanges');
    }
};
