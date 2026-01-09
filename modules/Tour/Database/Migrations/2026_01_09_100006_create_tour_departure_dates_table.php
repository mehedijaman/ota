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
        Schema::create('tour_departure_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->unsignedInteger('available_slots')->default(0);
            $table->unsignedInteger('booked_slots')->default(0);
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->enum('status', ['available', 'limited', 'sold_out', 'cancelled'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tour_id', 'departure_date']);
            $table->index(['departure_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_departure_dates');
    }
};
