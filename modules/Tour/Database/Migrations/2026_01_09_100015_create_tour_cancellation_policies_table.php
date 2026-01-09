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
        Schema::create('tour_cancellation_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->nullable()->constrained('tours')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('days_before_departure')->default(0);
            $table->decimal('refund_percentage', 5, 2)->default(100.00);
            $table->decimal('cancellation_fee', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tour_id', 'is_active']);
            $table->index(['is_default', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_cancellation_policies');
    }
};
