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
        Schema::create('hotel_rate_blackouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('hotel_rooms')->cascadeOnDelete()->comment('NULL = all rooms');
            $table->foreignId('rate_plan_id')->nullable()->constrained('hotel_rate_plans')->cascadeOnDelete()->comment('NULL = all plans');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->enum('blackout_type', ['full', 'no_arrival', 'no_departure'])->default('full');
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['hotel_id', 'start_date', 'end_date']);
            $table->index(['room_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rate_blackouts');
    }
};
