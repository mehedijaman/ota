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
        Schema::create('hotel_supplier_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('hotel_suppliers')->cascadeOnDelete();
            $table->enum('sync_type', [
                'availability',
                'rates',
                'inventory',
                'hotel_content',
                'room_content',
                'booking_create',
                'booking_confirm',
                'booking_cancel',
                'booking_modify',
                'search',
                'full_sync',
            ]);
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->enum('status', [
                'started',
                'in_progress',
                'success',
                'partial',
                'failed',
                'timeout',
            ])->default('started');

            // Request/Response Data
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->json('request_headers')->nullable();
            $table->unsignedSmallInteger('http_status_code')->nullable();

            // Error Handling
            $table->text('error_message')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_trace')->nullable();

            // Statistics
            $table->unsignedInteger('hotels_synced')->default(0);
            $table->unsignedInteger('rooms_synced')->default(0);
            $table->unsignedInteger('rates_synced')->default(0);
            $table->unsignedInteger('records_created')->default(0);
            $table->unsignedInteger('records_updated')->default(0);
            $table->unsignedInteger('records_failed')->default(0);

            // Performance
            $table->unsignedInteger('duration_ms')->nullable()->comment('API call duration');
            $table->unsignedInteger('total_duration_ms')->nullable()->comment('Total processing time');

            // Related Records
            $table->foreignId('booking_id')->nullable()->constrained('hotel_bookings')->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();

            // Timing
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();

            // Trigger
            $table->string('trigger_type')->nullable()->comment('manual, scheduler, webhook, api');
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['supplier_id', 'sync_type']);
            $table->index(['supplier_id', 'status']);
            $table->index('status');
            $table->index('started_at');
            $table->index('sync_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_supplier_sync_logs');
    }
};
