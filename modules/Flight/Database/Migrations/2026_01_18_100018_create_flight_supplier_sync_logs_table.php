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
        Schema::create('flight_supplier_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('flight_suppliers')->cascadeOnDelete();

            // Sync Type
            $table->enum('type', [
                'schedule_import',
                'fare_update',
                'inventory_sync',
                'booking_sync',
                'status_update',
                'airport_update',
                'airline_update',
                'full_sync',
            ]);

            // Status
            $table->enum('status', [
                'pending',
                'running',
                'completed',
                'failed',
                'partial',
            ])->default('pending');

            // Counts
            $table->unsignedInteger('total_records')->default(0);
            $table->unsignedInteger('created_count')->default(0);
            $table->unsignedInteger('updated_count')->default(0);
            $table->unsignedInteger('skipped_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);

            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();

            // Details
            $table->json('request_data')->nullable();
            $table->json('response_summary')->nullable();
            $table->json('errors')->nullable();
            $table->text('notes')->nullable();

            // Audit
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Indexes
            $table->index(['supplier_id', 'type']);
            $table->index(['supplier_id', 'status']);
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_supplier_sync_logs');
    }
};
