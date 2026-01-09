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
        Schema::create('tour_booking_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('tour_bookings')->cascadeOnDelete();
            $table->foreignId('policy_id')->nullable()->constrained('tour_cancellation_policies')->nullOnDelete();
            $table->string('cancellation_number')->unique();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'refunded'])->default('pending');
            $table->text('reason')->nullable();
            $table->enum('reason_category', ['personal', 'medical', 'weather', 'tour_cancelled', 'other'])->default('other');
            $table->unsignedSmallInteger('days_before_departure')->nullable();
            $table->decimal('refund_percentage', 5, 2)->default(0);
            $table->decimal('original_amount', 12, 2)->default(0);
            $table->decimal('cancellation_fee', 12, 2)->default(0);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->text('admin_notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_id', 'status']);
            $table->index('status');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_booking_cancellations');
    }
};
