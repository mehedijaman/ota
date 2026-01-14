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
        Schema::create('hotel_cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->nullable()->constrained('hotels')->cascadeOnDelete()->comment('NULL for system-wide defaults');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->longText('full_policy_text')->nullable()->comment('Customer-facing policy text');

            // Policy Type
            $table->boolean('is_refundable')->default(true);
            $table->boolean('is_free_cancellation')->default(false);

            // Rules as JSON
            $table->json('rules')->nullable()->comment('Array of timeframe rules');
            /*
             * Rules structure:
             * [
             *   {"hours_before_checkin": 72, "refund_percentage": 100, "penalty_nights": 0, "penalty_amount": null},
             *   {"hours_before_checkin": 48, "refund_percentage": 50, "penalty_nights": 1, "penalty_amount": null},
             *   {"hours_before_checkin": 24, "refund_percentage": 0, "penalty_nights": 2, "penalty_amount": null},
             *   {"hours_before_checkin": 0, "refund_percentage": 0, "penalty_nights": null, "penalty_amount": "full"}
             * ]
             */

            // No-show policy
            $table->decimal('no_show_penalty_percentage', 5, 2)->default(100);
            $table->unsignedTinyInteger('no_show_penalty_nights')->nullable();

            $table->boolean('is_default')->default(false)->comment('Default policy for hotel');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(100);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'is_active']);
            $table->index(['is_default', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_cancellation_policies');
    }
};
